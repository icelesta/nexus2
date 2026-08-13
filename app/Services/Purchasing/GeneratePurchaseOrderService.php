<?php

declare(strict_types=1);

namespace App\Services\Purchasing;

use RuntimeException;

use App\Models\AssignmentMaterialRequisition;
use App\Models\PurchaseOrder;
use App\Models\PurchaseOrderItem;
use App\Models\TransactionNumbering;

use App\Services\Numbering\NumberingService;

use Illuminate\Database\DatabaseManager;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;

class GeneratePurchaseOrderService
{
    /**
     * Constructor.
     */
    public function __construct(
        protected PurchaseOrder $model,
        protected NumberingService $numberingService,
        protected DatabaseManager $db,
    ) {
    }

    /*
    |--------------------------------------------------------------------------
    | Generate Purchase Order
    |--------------------------------------------------------------------------
    */

    /**
     * Generate Purchase Order from Assignment Material Requisition.
     */
    public function generate(
        int $assignmentId,
    ): PurchaseOrder {

        return $this->db->transaction(function () use (
            $assignmentId,
        ) {

            /*
            |--------------------------------------------------------------------------
            | Validate Assignment
            |--------------------------------------------------------------------------
            */

            $assignment = $this->validateAssignment(
                $assignmentId,
            );

            /*
            |--------------------------------------------------------------------------
            | Create Purchase Order Header
            |--------------------------------------------------------------------------
            */

            $purchaseOrder = $this->generateHeader(
                $assignment,
            );

            /*
            |--------------------------------------------------------------------------
            | Copy Assignment Items
            |--------------------------------------------------------------------------
            */

            $this->generateItems(
                $assignment,
                $purchaseOrder,
            );

            /*
            |--------------------------------------------------------------------------
            | Refresh Header Summary
            |--------------------------------------------------------------------------
            */

            $this->refreshTotals(
                $purchaseOrder,
            );

            /*
            |--------------------------------------------------------------------------
            | Update Assignment
            |--------------------------------------------------------------------------
            */

            $this->updateAssignment(
                $assignment,
                $purchaseOrder,
            );

            /*
            |--------------------------------------------------------------------------
            | Return
            |--------------------------------------------------------------------------
            */

            return $purchaseOrder->refresh();

        });

    }

    /*
    |--------------------------------------------------------------------------
    | Validation
    |--------------------------------------------------------------------------
    */

    /**
     * Validate Assignment before Purchase Order generation.
     */
    protected function validateAssignment(
        int $assignmentId,
    ): AssignmentMaterialRequisition {

        /*
        |--------------------------------------------------------------------------
        | Load Assignment
        |--------------------------------------------------------------------------
        */

        $assignment = AssignmentMaterialRequisition::query()

            ->with([
                'items',
                'purchaseRequisition',
            ])

            ->findOrFail($assignmentId);

        /*
        |--------------------------------------------------------------------------
        | Status Validation
        |--------------------------------------------------------------------------
        */

        if (! in_array(

            $assignment->status,

            [
                AssignmentMaterialRequisition::STATUS_DRAFT,
                AssignmentMaterialRequisition::STATUS_ASSIGNED,
            ],

            true,

        )) {

            throw new RuntimeException(
                'Assignment Material Requisition cannot generate Purchase Order.'
            );

        }

        /*
        |--------------------------------------------------------------------------
        | Duplicate Validation
        |--------------------------------------------------------------------------
        */

        if (

            method_exists($assignment, 'purchaseOrder')

            &&

            $assignment->purchaseOrder()->exists()

        ) {

            throw new RuntimeException(
                'Purchase Order has already been generated.'
            );

        }


        /*
        |--------------------------------------------------------------------------
        | Item Validation
        |--------------------------------------------------------------------------
        */

        if (! $assignment->items()->exists()) {

            throw new RuntimeException(
                'Assignment has no items.'
            );

        }

        foreach ($assignment->items as $item) {

            if (! $item->supplier_id) {

                throw new RuntimeException(

                    "Supplier is required for item {$item->item_code}."

                );

            }

            if (

                (float) $item->assigned_qty <= 0

            ) {

                throw new RuntimeException(

                    "Assigned Quantity must be greater than zero for {$item->item_code}."

                );

            }

            if (

                (float) $item->unit_price <= 0

            ) {

                throw new RuntimeException(

                    "Unit Price must be greater than zero for {$item->item_code}."

                );

            }

        }

        /*
        |--------------------------------------------------------------------------
        | Return
        |--------------------------------------------------------------------------
        */

        return $assignment;

    }

    /*
    |--------------------------------------------------------------------------
    | Header
    |--------------------------------------------------------------------------
    */

    /**
     * Generate Purchase Order Header.
     */
    protected function generateHeader(
        AssignmentMaterialRequisition $assignment,
    ): PurchaseOrder {

        /*
        |--------------------------------------------------------------------------
        | Determine Supplier
        |--------------------------------------------------------------------------
        */

        $supplierId = $assignment->items()
            ->select('supplier_id')
            ->distinct()
            ->pluck('supplier_id');

        if ($supplierId->count() !== 1) {

            throw new RuntimeException(
                'Purchase Order can only be generated when all items use the same supplier.'
            );

        }

        /*
        |--------------------------------------------------------------------------
        | Generate Document Number
        |--------------------------------------------------------------------------
        */

        $documentNumber = $this->numberingService->generate(

            documentType: TransactionNumbering::DOC_PURCHASE_ORDER,

            companyId: $assignment->company_id,

            businessUnitId: $assignment->business_unit_id,

            branchId: $assignment->branch_id,

        );

        /*
        |--------------------------------------------------------------------------
        | Create Header
        |--------------------------------------------------------------------------
        */

        return $this->model->create([

            /*
            |--------------------------------------------------------------------------
            | Document
            |--------------------------------------------------------------------------
            */

            'document_no'   => $documentNumber,
            'document_date' => today(),

            /*
            |--------------------------------------------------------------------------
            | Source Document
            |--------------------------------------------------------------------------
            */

            'purchase_requisition_id'
                => $assignment->purchase_requisition_id,

            'assignment_material_requisition_id'
                => $assignment->id,

            'pr_number'
                => $assignment->pr_number,

            'amr_number'
                => $assignment->document_no,

            /*
            |--------------------------------------------------------------------------
            | Supplier
            |--------------------------------------------------------------------------
            */

            'supplier_id'
                => $supplierId->first(),

            /*
            |--------------------------------------------------------------------------
            | Organization Snapshot
            |--------------------------------------------------------------------------
            */

            'company_id'
                => $assignment->company_id,

            'business_unit_id'
                => $assignment->business_unit_id,

            'branch_id'
                => $assignment->branch_id,

            'department_id'
                => $assignment->department_id,

            'section_id'
                => $assignment->section_id,

            'cost_center_id'
                => $assignment->cost_center_id,

            'warehouse_id'
                => $assignment->warehouse_id,

            /*
            |--------------------------------------------------------------------------
            | Request Information
            |--------------------------------------------------------------------------
            */

            'requester_id'
                => $assignment->requester_id,

            'request_date'
                => $assignment->request_date,

            'required_date'
                => $assignment->required_date,

            'expected_delivery_date'
                => $assignment->required_date,

            'priority'
                => $assignment->priority,

            'reference_no'
                => $assignment->reference_no,

            'shipping_address_id' => $assignment->shipping_address_id,

            /*
            |--------------------------------------------------------------------------
            | Financial
            |--------------------------------------------------------------------------
            */

            'currency_id'   => $assignment->currency_id,

            'exchange_rate' => $assignment->exchange_rate,

            'subtotal'         => 0,

            'discount_amount'  => 0,

            'tax_amount'       => 0,

            'grand_total'      => 0,

            /*
            |--------------------------------------------------------------------------
            | Approval
            |--------------------------------------------------------------------------
            */

            'approval_status'
                => PurchaseOrder::APPROVAL_PENDING,

            /*
            |--------------------------------------------------------------------------
            | Generated
            |--------------------------------------------------------------------------
            */

            'generated_by'
                => auth()->id(),

            'generated_at'
                => now(),

            /*
            |--------------------------------------------------------------------------
            | Workflow
            |--------------------------------------------------------------------------
            */

            'status'
                => PurchaseOrder::STATUS_DRAFT,

            /*
            |--------------------------------------------------------------------------
            | Remarks
            |--------------------------------------------------------------------------
            */

            'remarks'
                => $assignment->remarks,

            /*
            |--------------------------------------------------------------------------
            | Audit
            |--------------------------------------------------------------------------
            */

            'created_by'
                => auth()->id(),

        ]);

    }

    /*
    |--------------------------------------------------------------------------
    | Detail
    |--------------------------------------------------------------------------
    */

    /**
     * Generate Purchase Order Items.
     */
    protected function generateItems(
        AssignmentMaterialRequisition $assignment,
        PurchaseOrder $purchaseOrder,
    ): void {

        /*
        |--------------------------------------------------------------------------
        | Load Items
        |--------------------------------------------------------------------------
        */

        $assignment->loadMissing([
            'items',
        ]);

        /*
        |--------------------------------------------------------------------------
        | Copy Snapshot
        |--------------------------------------------------------------------------
        */

        foreach ($assignment->items as $assignmentItem) {

            $purchaseOrder->items()->create([

                /*
                |--------------------------------------------------------------------------
                | Source Reference
                |--------------------------------------------------------------------------
                */

                'assignment_material_requisition_item_id'
                    => $assignmentItem->id,

                /*
                |--------------------------------------------------------------------------
                | Item Snapshot
                |--------------------------------------------------------------------------
                */

                'item_id'
                    => $assignmentItem->item_id,

                'item_code'
                    => $assignmentItem->item_code,

                'item_name'
                    => $assignmentItem->item_name,

                'item_description'
                    => $assignmentItem->item_description,

                'specification'
                    => $assignmentItem->specification,

                /*
                |--------------------------------------------------------------------------
                | Quantity
                |--------------------------------------------------------------------------
                */

                'ordered_qty'
                    => $assignmentItem->assigned_qty,

                'received_qty'
                    => 0,

                'remaining_qty'
                    => $assignmentItem->assigned_qty,

                /*
                |--------------------------------------------------------------------------
                | UOM Snapshot
                |--------------------------------------------------------------------------
                */

                'uom_id'
                    => $assignmentItem->uom_id,

                'uom_code'
                    => $assignmentItem->uom_code,

                'uom_name'
                    => $assignmentItem->uom_name,

                /*
                |--------------------------------------------------------------------------
                | Warehouse Snapshot
                |--------------------------------------------------------------------------
                */

                'warehouse_id'
                    => $assignmentItem->warehouse_id,

                'warehouse_code'
                    => $assignmentItem->warehouse_code,

                'warehouse_name'
                    => $assignmentItem->warehouse_name,

                /*
                |--------------------------------------------------------------------------
                | Supplier
                |--------------------------------------------------------------------------
                */

                'supplier_id'
                    => $assignmentItem->supplier_id,

                /*
                |--------------------------------------------------------------------------
                | Pricing
                |--------------------------------------------------------------------------
                */

                'unit_price'
                    => $assignmentItem->unit_price,

                'discount_percent'
                    => $assignmentItem->discount_percent,

                'discount_amount'
                    => $assignmentItem->discount_amount,

                'gross_amount'
                    => round(
                        $assignmentItem->assigned_qty
                        * $assignmentItem->unit_price,
                        2
                    ),

                'net_amount'
                    => round(
                        (
                            $assignmentItem->assigned_qty
                            * $assignmentItem->unit_price
                        )
                        - $assignmentItem->discount_amount,
                        2
                    ),

                /*
                |--------------------------------------------------------------------------
                | Tax
                |--------------------------------------------------------------------------
                */

                'tax_id'
                    => $assignmentItem->tax_id,

                'tax_name'
                    => $assignmentItem->tax_name,

                'tax_percent'
                    => $assignmentItem->tax_percent,

                'tax_amount'
                    => $assignmentItem->tax_amount,

                /*
                |--------------------------------------------------------------------------
                | Totals
                |--------------------------------------------------------------------------
                */

                'line_total'
                    => $assignmentItem->line_total,

                'grand_total'
                    => $assignmentItem->grand_total,

                /*
                |--------------------------------------------------------------------------
                | Delivery
                |--------------------------------------------------------------------------
                */

                'required_date'
                    => $assignmentItem->required_date,

                'delivery_date'
                    => $assignmentItem->delivery_date,

                'delivery_location'
                    => $assignmentItem->delivery_location,

                /*
                |--------------------------------------------------------------------------
                | Workflow
                |--------------------------------------------------------------------------
                */

                'status'
                    => PurchaseOrderItem::STATUS_OPEN,

                /*
                |--------------------------------------------------------------------------
                | Remarks
                |--------------------------------------------------------------------------
                */

                'remarks'
                    => $assignmentItem->remarks,

                /*
                |--------------------------------------------------------------------------
                | Audit
                |--------------------------------------------------------------------------
                */

                'created_by'
                    => auth()->id(),

            ]);

        }

    }

    /*
    |--------------------------------------------------------------------------
    | Summary
    |--------------------------------------------------------------------------
    */

    /**
     * Refresh Purchase Order Totals.
     */
    protected function refreshTotals(
        PurchaseOrder $purchaseOrder,
    ): void {

        /*
        |--------------------------------------------------------------------------
        | Reload Items
        |--------------------------------------------------------------------------
        */

        $purchaseOrder->loadMissing([
            'items',
        ]);

        /*
        |--------------------------------------------------------------------------
        | Calculate Totals
        |--------------------------------------------------------------------------
        */

        $subtotal = 0;

        $discount = 0;

        $tax = 0;

        $grandTotal = 0;

        foreach ($purchaseOrder->items as $item) {

            $subtotal += (float) $item->gross_amount;

            $discount += (float) $item->discount_amount;

            $tax += (float) $item->tax_amount;

            $grandTotal += (float) $item->grand_total;

        }

        /*
        |--------------------------------------------------------------------------
        | Update Header
        |--------------------------------------------------------------------------
        */

        $purchaseOrder->update([

            'subtotal' => round(
                $subtotal,
                2,
            ),

            'discount_amount' => round(
                $discount,
                2,
            ),

            'tax_amount' => round(
                $tax,
                2,
            ),

            'grand_total' => round(
                $grandTotal,
                2,
            ),

            'updated_by' => auth()->id(),

        ]);

    }

    /*
    |--------------------------------------------------------------------------
    | Assignment Update
    |--------------------------------------------------------------------------
    */

    /**
     * Update Assignment after Purchase Order generation.
     */
    protected function updateAssignment(
        AssignmentMaterialRequisition $assignment,
        PurchaseOrder $purchaseOrder,
    ): void {

        $assignment->update([

            /*
            |--------------------------------------------------------------------------
            | Purchase Order Reference
            |--------------------------------------------------------------------------
            */

            'purchase_order_id'
                => $purchaseOrder->id,

            /*
            |--------------------------------------------------------------------------
            | Workflow
            |--------------------------------------------------------------------------
            */

            'status'
                => AssignmentMaterialRequisition::STATUS_COMPLETED,

            /*
            |--------------------------------------------------------------------------
            | Audit
            |--------------------------------------------------------------------------
            */

            'updated_by'
                => auth()->id(),

        ]);

    }

    /*
    |--------------------------------------------------------------------------
    | Helper
    |--------------------------------------------------------------------------
    */

    /**
     * Find Assignment Material Requisition.
     *
     * @throws ModelNotFoundException
     */
    protected function findAssignment(
        int $id,
    ): AssignmentMaterialRequisition {

        return AssignmentMaterialRequisition::query()
            ->findOrFail($id);

    }

}