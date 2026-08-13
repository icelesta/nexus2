<?php

declare(strict_types=1);

namespace App\Services\Purchasing;

use App\Models\AssignmentMaterialRequisition;
use App\Models\AssignmentMaterialRequisitionItem;
use App\Models\PurchaseOrder;
use App\Models\PurchaseOrderItem;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\DB;
use App\Services\Numbering\NumberingService;

class PurchaseOrderService
{

/**
 * Create a new service instance.
 */
public function __construct(
    protected NumberingService $numberingService,
) {
    //
}


/**
 * Create Purchase Order from Assignment Material Requisition.
 */
public function create(array $data): PurchaseOrder
    {
        return DB::transaction(function () use ($data): PurchaseOrder {

            /**
             * Find Assignment Material Requisition.
             */
            protected function findAssignment(
                int $assignmentId
            ): AssignmentMaterialRequisition
            {
                return AssignmentMaterialRequisition::query()

                    ->with([
                        'items',
                        'supplier',
                        'company',
                        'businessUnit',
                        'branch',
                        'department',
                        'section',
                        'costCenter',
                        'warehouse',
                        'requester',
                        'currency',
                    ])

                    ->findOrFail($assignmentId);
            }

            /**
             * Validate Assignment Material Requisition before creating Purchase Order.
             */
            protected function validateAssignment(
                AssignmentMaterialRequisition $assignment
            ): void
            {
                /*
                |--------------------------------------------------------------------------
                | Soft Delete Validation
                |--------------------------------------------------------------------------
                */

                if ($assignment->trashed()) {

                    throw new \RuntimeException(
                        'Assignment Material Requisition has been deleted.'
                    );

                }

                /*
                |--------------------------------------------------------------------------
                | Supplier Validation
                |--------------------------------------------------------------------------
                */

                if (blank($assignment->supplier_id)) {

                    throw new \RuntimeException(
                        'Supplier has not been assigned.'
                    );

                }

                /*
                |--------------------------------------------------------------------------
                | Item Validation
                |--------------------------------------------------------------------------
                */

                if ($assignment->items->isEmpty()) {

                    throw new \RuntimeException(
                        'Assignment Material Requisition has no items.'
                    );

                }

                /*
                |--------------------------------------------------------------------------
                | Status Validation
                |--------------------------------------------------------------------------
                */

                if (! $assignment->isApproved()) {

                    throw new \RuntimeException(
                        'Assignment Material Requisition must be approved before creating a Purchase Order.'
                    );

                }
            }

            /**
             * Create Purchase Order Header.
             */
            protected function createPurchaseOrderHeader(
                AssignmentMaterialRequisition $assignment,
                array $data
            ): PurchaseOrder
            {
                return PurchaseOrder::create([

                    /*
                    |--------------------------------------------------------------------------
                    | Document Information
                    |--------------------------------------------------------------------------
                    */

                    'document_no' => $this->numberingService->generate(
                        documentType: 'PO',
                        companyId: $assignment->company_id,
                        businessUnitId: $assignment->business_unit_id,
                        branchId: $assignment->branch_id,
                    ),
                    'document_date' => $data['document_date'] ?? now(),

                    /*
                    |--------------------------------------------------------------------------
                    | Source Document
                    |--------------------------------------------------------------------------
                    */

                    'purchase_requisition_id'
                        => $assignment->purchase_requisition_id,

                    'assignment_material_requisition_id'
                        => $assignment->id,

                    /*
                    |--------------------------------------------------------------------------
                    | Supplier
                    |--------------------------------------------------------------------------
                    */

                    'supplier_id'
                        => $assignment->supplier_id,

                    /*
                    |--------------------------------------------------------------------------
                    | Organization Snapshot
                    |--------------------------------------------------------------------------
                    */

                    'company_id'        => $assignment->company_id,
                    'business_unit_id'  => $assignment->business_unit_id,
                    'branch_id'         => $assignment->branch_id,
                    'department_id'     => $assignment->department_id,
                    'section_id'        => $assignment->section_id,
                    'cost_center_id'    => $assignment->cost_center_id,
                    'warehouse_id'      => $assignment->warehouse_id,


                    /*
                    |--------------------------------------------------------------------------
                    | Request Information
                    |--------------------------------------------------------------------------
                    */

                    'requester_id' => $assignment->requester_id,

                    'request_date' => $assignment->request_date,

                    'required_date' => $assignment->required_date,

                    'expected_delivery_date' => $assignment->required_date,

                    'priority' => $assignment->priority,

                    'reference_no' => $assignment->reference_no,

                    'delivery_location' => $assignment->delivery_location,



                    /*
                    |--------------------------------------------------------------------------
                    | Financial
                    |--------------------------------------------------------------------------
                    */

                    'currency_id'       => $assignment->currency_id,
                    'exchange_rate'     => $assignment->exchange_rate ?? 1,

                    'subtotal'          => 0,
                    'discount_amount'   => 0,
                    'tax_amount'        => 0,
                    'grand_total'       => 0,

                    /*
                    |--------------------------------------------------------------------------
                    | Workflow
                    |--------------------------------------------------------------------------
                    */

                    'remarks'           => $assignment->remarks,
                ]);
            }

            /**
             * Copy Assignment Items into Purchase Order Items.
             */
            protected function copyItemSnapshots(
                PurchaseOrder $purchaseOrder,
                AssignmentMaterialRequisition $assignment
            ): void
            {
                foreach ($assignment->items as $item) {

                    PurchaseOrderItem::create([

                        /*
                        |--------------------------------------------------------------------------
                        | Reference
                        |--------------------------------------------------------------------------
                        */

                        'purchase_order_id'                     => $purchaseOrder->id,
                        'assignment_material_requisition_item_id' => $item->id,

                        /*
                        |--------------------------------------------------------------------------
                        | Item Snapshot
                        |--------------------------------------------------------------------------
                        */

                        'item_id'              => $item->item_id,
                        'item_code'            => $item->item_code,
                        'item_name'            => $item->item_name,
                        'item_description'     => $item->item_description,
                        'specification'        => $item->specification,

                        /*
                        |--------------------------------------------------------------------------
                        | Quantity
                        |--------------------------------------------------------------------------
                        */

                        $orderedQty = $item->assigned_qty;

                        'ordered_qty'   => $orderedQty,

                        'received_qty'  => 0,

                        'remaining_qty' => $orderedQty,

                        /*
                        |--------------------------------------------------------------------------
                        | UOM Snapshot
                        |--------------------------------------------------------------------------
                        */

                        'uom_id'               => $item->uom_id,
                        'uom_code'             => $item->uom_code,
                        'uom_name'             => $item->uom_name,

                        /*
                        |--------------------------------------------------------------------------
                        | Warehouse Snapshot
                        |--------------------------------------------------------------------------
                        */

                        'warehouse_id'         => $item->warehouse_id,
                        'warehouse_code'       => $item->warehouse_code,
                        'warehouse_name'       => $item->warehouse_name,

                        /*
                        |--------------------------------------------------------------------------
                        | Supplier
                        |--------------------------------------------------------------------------
                        */

                        'supplier_id'          => $item->supplier_id,

                        /*
                        |--------------------------------------------------------------------------
                        | Pricing
                        |--------------------------------------------------------------------------
                        */

                        'unit_price'           => $item->unit_price,
                        'discount_percent'     => $item->discount_percent,
                        'discount_amount'      => $item->discount_amount,
                        'gross_amount'         => $item->gross_amount,
                        'net_amount'           => $item->net_amount,

                        /*
                        |--------------------------------------------------------------------------
                        | Tax Snapshot
                        |--------------------------------------------------------------------------
                        */

                        'tax_id'               => $item->tax_id,
                        'tax_name'             => $item->tax_name,
                        'tax_percent'          => $item->tax_percent,
                        'tax_amount'           => $item->tax_amount,

                        /*
                        |--------------------------------------------------------------------------
                        | Total
                        |--------------------------------------------------------------------------
                        */

                        'line_total'           => $item->line_total,
                        'grand_total'          => $item->grand_total,

                        /*
                        |--------------------------------------------------------------------------
                        | Delivery
                        |--------------------------------------------------------------------------
                        */

                        'required_date'        => $item->required_date,
                        'delivery_date'        => $item->delivery_date,
                        'delivery_location'    => $item->delivery_location,

                        /*
                        |--------------------------------------------------------------------------
                        | Workflow
                        |--------------------------------------------------------------------------
                        */

                        'status'               => PurchaseOrderItem::STATUS_OPEN,
                        'remarks'              => $item->remarks,

                    ]);
                }
            }

            /**
             * Calculate Purchase Order Totals.
             */
            protected function calculateTotals(
                PurchaseOrder $purchaseOrder
            ): void
            {
                $purchaseOrder->load('items');

                $subtotal = $purchaseOrder->items->sum('net_amount');

                $discountAmount = $purchaseOrder->items->sum('discount_amount');

                $taxAmount = $purchaseOrder->items->sum('tax_amount');

                $grandTotal = $purchaseOrder->items->sum('grand_total');

                $purchaseOrder->update([

                    'subtotal'        => $subtotal,

                    'discount_amount' => $discountAmount,

                    'tax_amount'      => $taxAmount,

                    'grand_total'     => $grandTotal,

                ]);
            }



            /*
            |--------------------------------------------------------------------------
            | Calculate Total
            |--------------------------------------------------------------------------
            */

            $this->calculateTotals($purchaseOrder);



            /**
             * Update Assignment Material Requisition status after
             * Purchase Order has been created.
             */
            protected function updateAssignmentStatus(
                AssignmentMaterialRequisition $assignment
            ): void
            {
                /*
                |--------------------------------------------------------------------------
                | Workflow Synchronization
                |--------------------------------------------------------------------------
                |
                | This method is intentionally isolated so the workflow can evolve
                | without affecting the create() orchestration.
                |
                */

                $assignment->update([
                    'status' => AssignmentMaterialRequisition::STATUS_COMPLETED,
                ]);
            }



            /*
            |--------------------------------------------------------------------------
            | Synchronize Workflow
            |--------------------------------------------------------------------------
            */

            $this->updateAssignmentStatus($assignment);

            return $purchaseOrder;
        });
    }

}