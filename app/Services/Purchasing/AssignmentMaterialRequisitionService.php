<?php

declare(strict_types=1);

namespace App\Services\Purchasing;

use RuntimeException;

use App\Services\Approval\ApprovalTransactionService;

use App\Models\AssignmentMaterialRequisition;
use App\Models\AssignmentMaterialRequisitionItem;
use App\Models\PurchaseRequisition;
use App\Models\TransactionNumbering;

use App\Services\Numbering\NumberingService;

use Illuminate\Database\DatabaseManager;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;


class AssignmentMaterialRequisitionService
{
    /**
     * Constructor.
     */
    public function __construct(
        protected AssignmentMaterialRequisition $model,
        protected AssignmentMaterialRequisitionItemService $itemService,
        protected PricingService $pricingService,
        protected NumberingService $numberingService,
        protected ApprovalTransactionService $approvalTransactionService,
        protected DatabaseManager $db,
    ) {
    }

    public function create(array $data): AssignmentMaterialRequisition
    {
        $this->validateCreate($data);

        return $this->db->transaction(function () use ($data) {

            /*
            |--------------------------------------------------------------------------
            | Prepare Material Requisition
            |--------------------------------------------------------------------------
            */

            $purchaseRequisition = $this->preparePurchaseRequisition(
                $data
            );

            /*
            |--------------------------------------------------------------------------
            | Create Assignment Header
            |--------------------------------------------------------------------------
            */

            $assignment = $this->createAssignmentHeader(
                $purchaseRequisition,
                $data,
            );


            /*
            |--------------------------------------------------------------------------
            | Header Snapshot
            |--------------------------------------------------------------------------
            |
            | Implemented in Sprint 5.8.1.3
            |
            */

            $this->copyHeaderSnapshot(
                $assignment,
                $purchaseRequisition,
            );

            /*
            |--------------------------------------------------------------------------
            | Item Snapshot
            |--------------------------------------------------------------------------
            |
            | Implemented in Sprint 5.8.1.4
            |
            */

            $this->copyPurchaseRequisitionItems(
                $assignment,
                $purchaseRequisition,
            );

            /*
            |--------------------------------------------------------------------------
            | Refresh Summary
            |--------------------------------------------------------------------------
            |
            | Implemented in Sprint 5.8.1.5
            |
            */

            // $this->refreshHeaderSummary(
            //     $assignment,
            // );

            /*
            |--------------------------------------------------------------------------
            | Return
            |--------------------------------------------------------------------------
            */

            return $assignment->refresh();

        });
    }


    /**
     * Create Assignment Material Requisition
     * from an approved Material Requisition.
     *
     * AM-5.5.2
     *
     * This method is the official integration point
     * between Material Requisition Approval and AMR.
     */
    public function createFromApprovedPurchaseRequisition(
        int $purchaseRequisitionId,
    ): AssignmentMaterialRequisition {

        $purchaseRequisition = PurchaseRequisition::query()
            ->with([
                'items',
                'company',
                'businessUnit',
                'branch',
                'department',
                'section',
                'costCenter',
                'warehouse',
            ])
            ->findOrFail($purchaseRequisitionId);

        /*
        |--------------------------------------------------------------------------
        | Approval Gate
        |--------------------------------------------------------------------------
        */

        $this->validatePurchaseRequisition(
            $purchaseRequisition
        );

        /*
        |--------------------------------------------------------------------------
        | Create AMR
        |--------------------------------------------------------------------------
        |
        | Reuse existing create() pipeline so there is
        | only ONE snapshot implementation.
        |
        */

        return $this->create([
            'purchase_requisition_id' => $purchaseRequisition->id,

            'currency_id' => $purchaseRequisition->currency_id,

            'exchange_rate' =>
                $purchaseRequisition->exchange_rate ?? 1.000000,

            'assigned_to' => null,

            'assigned_by' => auth()->id(),

            'remarks' =>
                'Automatically generated from approved Material Requisition '
                . $purchaseRequisition->pr_no,
        ]);
    }
    

    /*
    |--------------------------------------------------------------------------
    | Update
    |--------------------------------------------------------------------------
    */

    /**
     * Update Assignment Material Requisition.
     */
    public function update(
        int $id,
        array $data,
    ): AssignmentMaterialRequisition {

        $this->validateUpdate($data);

        return $this->db->transaction(function () use (
            $id,
            $data,
        ) {

            $assignment = $this->findById($id);

            if (! $this->canModify($assignment)) {

                throw new \RuntimeException(
                    'Assignment Material Requisition cannot be modified.'
                );
            }

            $data['updated_by'] = auth()->id();

            $assignment->update($data);

            return $assignment->refresh();


        });
    }

    /*
    |--------------------------------------------------------------------------
    | Delete
    |--------------------------------------------------------------------------
    */

    /**
     * Delete Assignment Material Requisition.
     */
    public function delete(
        int $id,
    ): bool {

        return $this->db->transaction(function () use ($id) {

            $assignment = $this->findById($id);

            if (! $assignment->canDelete()) {
                throw new RuntimeException(
                    'Only Draft or Assigned Assignment Material Requisition can be deleted.'
                );
            }

            /*
            |--------------------------------------------------------------------------
            | Audit Trail
            |--------------------------------------------------------------------------
            */

            $assignment->update([
                'deleted_by' => auth()->id(),
            ]);

            /*
            |--------------------------------------------------------------------------
            | Soft Delete
            |--------------------------------------------------------------------------
            */

            return (bool) $assignment->delete();

        });
    }

    /*
    |--------------------------------------------------------------------------
    | Workflow
    |--------------------------------------------------------------------------
    */

    /**
     * Submit Assignment Material Requisition.
     *
     * AMR Workflow:
     *
     * Draft
     *   ↓
     * Updated
     *   ↓
     * Submit
     *   ↓
     * Waiting Approval
     *
     * AMR stops here.
     *
     * AMR does NOT perform:
     * - Approval
     * - Rejection
     * - Completion
     *
     * After AMR submission, the next approval level
     * of the original Material Requisition Approval
     * Transaction is activated.
     */
    public function submit(
        int $id,
    ): AssignmentMaterialRequisition {

        return $this->db->transaction(function () use ($id) {

            /*
            |--------------------------------------------------------------------------
            | Load AMR
            |--------------------------------------------------------------------------
            */

            $assignment = $this->findById($id);

            /*
            |--------------------------------------------------------------------------
            | Validate Workflow
            |--------------------------------------------------------------------------
            |
            | Only UPDATED AMR may be submitted.
            |
            */

            $this->validateWorkflowTransition(
                $assignment,
                AssignmentMaterialRequisition::STATUS_WAITING_APPROVAL,
            );

            /*
            |--------------------------------------------------------------------------
            | Move AMR to Waiting Approval
            |--------------------------------------------------------------------------
            */

            $assignment->update([
                'status' => AssignmentMaterialRequisition::STATUS_WAITING_APPROVAL,

                'updated_by' => auth()->id(),
            ]);

            /*
            |--------------------------------------------------------------------------
            | Activate Next MR Approval Level
            |--------------------------------------------------------------------------
            |
            | AMR does NOT approve anything.
            |
            | The approval transaction remains attached
            | to the original Material Requisition.
            |
            | Example:
            |
            | MR Approval Level 1
            |        ↓
            |      APPROVED
            |        ↓
            |      AMR Buyer
            |        ↓
            |    Submit AMR
            |        ↓
            | MR Approval Level 2
            |        ↓
            |      PENDING
            |
            */

            $this->approvalTransactionService
                ->activateNextMaterialRequisitionLevel(
                    (int) $assignment->purchase_requisition_id
                );

            /*
            |--------------------------------------------------------------------------
            | Return Fresh AMR
            |--------------------------------------------------------------------------
            */

            return $assignment->refresh();
        });
    }

    /**
     * Request Approval Assignment Material Requisition.
     */
    public function requestApproval(
        int $id,
    ): AssignmentMaterialRequisition {

        return $this->db->transaction(function () use ($id) {

            $assignment = $this->findById($id);

            $this->validateWorkflowTransition(
                $assignment,
                AssignmentMaterialRequisition::STATUS_WAITING_APPROVAL,
            );

            $assignment->update([
                'status' => AssignmentMaterialRequisition::STATUS_WAITING_APPROVAL,
            ]);

            return $assignment->refresh();

        });
    }


    /**
     * Approve Assignment Material Requisition.
     */
    public function approve(
        int $id,
    ): AssignmentMaterialRequisition {

        return $this->db->transaction(function () use ($id) {

            $assignment = $this->findById($id);

            $this->validateWorkflowTransition(
                $assignment,
                AssignmentMaterialRequisition::STATUS_APPROVED,
            );            

            $assignment->update([
                'status' => AssignmentMaterialRequisition::STATUS_APPROVED,
            ]);

            return $assignment->refresh();


        });
    }

    /**
     * Reject Assignment Material Requisition.
     */
    public function reject(
        int $id,
    ): AssignmentMaterialRequisition {

        return $this->db->transaction(function () use ($id) {

            $assignment = $this->findById($id);

            $this->validateWorkflowTransition(
                $assignment,
                AssignmentMaterialRequisition::STATUS_REJECTED,
            );          

            $assignment->update([
                'status' => AssignmentMaterialRequisition::STATUS_REJECTED,
            ]);

            return $assignment->refresh();


        });
    }

    /**
     * Complete Assignment Material Requisition.
     */
    public function complete(
        int $id,
    ): AssignmentMaterialRequisition {

        return $this->db->transaction(function () use ($id) {

            $assignment = $this->findById($id);

            $this->validateWorkflowTransition(
                $assignment,
                AssignmentMaterialRequisition::STATUS_COMPLETED,
            );           

            $assignment->update([
                'status' => AssignmentMaterialRequisition::STATUS_COMPLETED,
            ]);

            return $assignment->refresh();


        });
    }

    /**
     * Cancel Assignment Material Requisition.
     */
    public function cancel(
        int $id,
    ): AssignmentMaterialRequisition {

        return $this->db->transaction(function () use ($id) {

            $assignment = $this->findById($id);

            $this->validateWorkflowTransition(
                $assignment,
                AssignmentMaterialRequisition::STATUS_CANCELLED,
            );            

            $assignment->update([
                'status' => AssignmentMaterialRequisition::STATUS_CANCELLED,
            ]);

            return $assignment->refresh();


        });
    }


    /*
    |--------------------------------------------------------------------------
    | Buyer Assignment
    |--------------------------------------------------------------------------
    */

    /**
     * Assign Buyer.
     */
    public function assignBuyer(
        int $id,
        int $userId,
    ): AssignmentMaterialRequisition {

        return $this->db->transaction(function () use (
            $id,
            $userId,
        ) {

            $assignment = $this->findById($id);

            if (! $assignment->canEdit()) {
                throw new RuntimeException(
                    'Assignment Material Requisition cannot be modified.'
                );
            }            

            $this->validateBuyerAssignment(
                $userId
            );

            $assignment->update([

                'assigned_to' => $userId,

                'assigned_by' => auth()->id(),

                'assigned_at' => now(),

            ]);

            return $assignment->refresh();


        });
    }

    /**
     * Reassign Buyer.
     */
    public function reassignBuyer(
        int $id,
        int $userId,
    ): AssignmentMaterialRequisition {

        return $this->assignBuyer(
            $id,
            $userId
        );
    }

    /*
    |--------------------------------------------------------------------------
    | Purchase Requisition Reference
    |--------------------------------------------------------------------------
    */

    /**
     * Find Purchase Requisition.
     *
     * @throws ModelNotFoundException
     */
    protected function findPurchaseRequisition(
        int $id,
    ): PurchaseRequisition {
        return PurchaseRequisition::query()
            ->findOrFail($id);
    }

    /**
     * Validate Purchase Requisition before Assignment creation.
     *
     * Ensures that the selected Material Requisition
     * is eligible to become an Assignment Material Requisition.
     *
     * Validation Rules:
     * - Document must not be soft deleted.
     * - Status must be Submitted or Approved.
     * - Assignment must not already exist.
     *
     * @throws RuntimeException
     */
    protected function validatePurchaseRequisition(
        PurchaseRequisition $purchaseRequisition,
    ): void {

        /*
        |--------------------------------------------------------------------------
        | Soft Delete Validation
        |--------------------------------------------------------------------------
        */

        if ($purchaseRequisition->deleted_at !== null) {
            throw new RuntimeException(
                'Material Requisition has been deleted.'
            );
        }

        /*
        |--------------------------------------------------------------------------
        | Status Validation
        |--------------------------------------------------------------------------
        */

        if (! $purchaseRequisition->exists) {

            throw new RuntimeException(
                'Material Requisition not found.'
            );
        }

        /*
        |--------------------------------------------------------------------------
        | Approval Gate
        |--------------------------------------------------------------------------
        |
        | Assignment Material Requisition may ONLY be created
        | after the Material Requisition has completed approval.
        |
        */

        if (
            $purchaseRequisition->status
            !== PurchaseRequisition::STATUS_APPROVED
        ) {

            throw new RuntimeException(
                "Material Requisition [{$purchaseRequisition->pr_no}] "
                . "is not approved yet. "
                . "Current status: [{$purchaseRequisition->status}]."
            );
        }

        /*
        |--------------------------------------------------------------------------
        | Item Validation
        |--------------------------------------------------------------------------
        */

        if (! $purchaseRequisition->items()->exists()) {

            throw new RuntimeException(
                'Material Requisition has no items.'
            );
        }

        /*
        |--------------------------------------------------------------------------
        | Duplicate Assignment Validation
        |--------------------------------------------------------------------------
        */

        $alreadyAssigned = AssignmentMaterialRequisition::query()
            ->where(
                'purchase_requisition_id',
                $purchaseRequisition->id,
            )
            ->exists();

        if ($alreadyAssigned) {
            throw new RuntimeException(
                'Material Requisition has already been assigned.'
            );
        }
    }


    /**
     * Prepare Purchase Requisition.
     *
     * Load and validate the selected
     * Material Requisition before Assignment creation.
     */
    protected function preparePurchaseRequisition(
        array $data,
    ): PurchaseRequisition {

    $purchaseRequisition = PurchaseRequisition::query()
        ->with([
            'items',
            'company',
            'businessUnit',
            'branch',
            'department',
            'section',
            'costCenter',
            'warehouse',
        ])
        ->findOrFail(
            $data['purchase_requisition_id']
        );

        $this->validatePurchaseRequisition(
            $purchaseRequisition
        );

        return $purchaseRequisition;
    }



    /**
     * Create Assignment Material Requisition header.
     */
    protected function createAssignmentHeader(
        PurchaseRequisition $purchaseRequisition,
        array $data,
    ): AssignmentMaterialRequisition {

    $documentNumber = $this->numberingService->generate(
        documentType: TransactionNumbering::DOC_ASSIGNMENT_MATERIAL,
        companyId: $purchaseRequisition->company_id,
        businessUnitId: $purchaseRequisition->business_unit_id,
        branchId: $purchaseRequisition->branch_id,
    );

    return $this->model->create([

        /*
        |--------------------------------------------------------------------------
        | Document Information
        |--------------------------------------------------------------------------
        */

        'document_no'   => $documentNumber,
        'document_date' => today(),

        /*
        |--------------------------------------------------------------------------
        | Purchase Requisition Reference
        |--------------------------------------------------------------------------
        */

        'purchase_requisition_id' => $purchaseRequisition->id,

        /*
        |--------------------------------------------------------------------------
        | Assignment Information
        |--------------------------------------------------------------------------
        */

        'assigned_to' => $data['assigned_to'] ?? null,
        'assigned_by' => $data['assigned_by'] ?? auth()->id(),
        'assigned_at' => $data['assigned_at'] ?? now(),

        /*
        |--------------------------------------------------------------------------
        | Financial
        |--------------------------------------------------------------------------
        */

        'currency_id'   => $data['currency_id'] ?? null,

        'exchange_rate' => $data['exchange_rate'] ?? 1.000000,

        'remarks' => $data['remarks'] ?? null,

        /*
        |--------------------------------------------------------------------------
        | Workflow
        |--------------------------------------------------------------------------
        */

        'status' => AssignmentMaterialRequisition::STATUS_DRAFT,

        /*
        |--------------------------------------------------------------------------
        | Audit Trail
        |--------------------------------------------------------------------------
        */

        'created_by' => auth()->id(),

    ]);

    }


    /*
    |--------------------------------------------------------------------------
    | Purchase Requisition Synchronization
    |--------------------------------------------------------------------------
    */

    /**
     * Synchronize Assignment Items with the selected
     * Material Requisition.
     *
     * Workflow:
     * - Validate Assignment status.
     * - Load Material Requisition.
     * - Clear existing Assignment Items.
     * - Copy Material Requisition Items.
     * - Refresh Assignment summary.
     *
     * @throws RuntimeException
     */
    public function syncPurchaseRequisitionItems(
        AssignmentMaterialRequisition $assignment,
    ): AssignmentMaterialRequisition {

        return $this->db->transaction(function () use ($assignment) {

            /*
            |--------------------------------------------------------------------------
            | Validate Assignment
            |--------------------------------------------------------------------------
            */

            if (! $assignment->canEdit()) {
                throw new RuntimeException(
                    'Assignment Material Requisition cannot be modified.'
                );
            }

            /*
            |--------------------------------------------------------------------------
            | Load Material Requisition
            |--------------------------------------------------------------------------
            */

            $purchaseRequisition = $assignment
                ->purchaseRequisition()
                ->with('items')
                ->first();

            if (! $purchaseRequisition) {
                throw new RuntimeException(
                    'Material Requisition not found.'
                );
            }

            /*
            |--------------------------------------------------------------------------
            | Synchronize Items
            |--------------------------------------------------------------------------
            */

            $this->clearAssignmentItems($assignment);

            $this->copyPurchaseRequisitionItems(
                $assignment,
                $purchaseRequisition,
            );

            /*
            |--------------------------------------------------------------------------
            | Refresh Summary
            |--------------------------------------------------------------------------
            */

            $this->refreshItems(
                $assignment->id,
            );

            return $assignment->refresh();

        });
    }


    /**
     * Remove all Assignment Items before synchronization.
     *
     * Existing Assignment Items are deleted so the latest
     * Material Requisition Items can be copied without
     * creating duplicates.
     */
    protected function clearAssignmentItems(
        AssignmentMaterialRequisition $assignment,
    ): void {

        /*
        |--------------------------------------------------------------------------
        | Delete Existing Assignment Items
        |--------------------------------------------------------------------------
        */

        $assignment
            ->items()
            ->delete();
    }


    /**
     * Copy Material Requisition Items into Assignment Items.
     *
     * Creates a transactional snapshot of every Material
     * Requisition Item so future changes on the original
     * document will not affect this Assignment.
     */
    protected function copyPurchaseRequisitionItems(
        AssignmentMaterialRequisition $assignment,
        PurchaseRequisition $purchaseRequisition,
    ): void {

        /*
        |--------------------------------------------------------------------------
        | Load Relationships
        |--------------------------------------------------------------------------
        */

        $purchaseRequisition->loadMissing([
            'items.item',
            'items.uom',
            'items.warehouse',
        ]);

        /*
        |--------------------------------------------------------------------------
        | Copy Snapshot
        |--------------------------------------------------------------------------
        */

        foreach ($purchaseRequisition->items as $purchaseRequisitionItem) {

            $item = $purchaseRequisitionItem->item;
            $uom = $purchaseRequisitionItem->uom;
            $warehouse = $purchaseRequisitionItem->warehouse;

            $assignment->items()->create([

                /*
                |--------------------------------------------------------------------------
                | Source Reference
                |--------------------------------------------------------------------------
                */

                'purchase_requisition_item_id' => $purchaseRequisitionItem->id,

                /*
                |--------------------------------------------------------------------------
                | Item Snapshot
                |--------------------------------------------------------------------------
                */

                'item_id'              => $purchaseRequisitionItem->item_id,
                'item_code'            => $item?->item_code,
                'item_name'            => $item?->item_name,
                'item_description'     => $item?->description,
                'specification'        => $item?->remarks,

                /*
                |--------------------------------------------------------------------------
                | UOM Snapshot
                |--------------------------------------------------------------------------
                */

                'uom_id'               => $purchaseRequisitionItem->uom_id,
                'uom_code'             => $uom?->uom_code,
                'uom_name'             => $uom?->uom_name,

                /*
                |--------------------------------------------------------------------------
                | Quantity Snapshot
                |--------------------------------------------------------------------------
                */

                'requested_qty'        => $purchaseRequisitionItem->quantity,
                'approved_qty'         => $purchaseRequisitionItem->quantity,
                'remaining_qty'        => $purchaseRequisitionItem->quantity,

                /*
                |--------------------------------------------------------------------------
                | Warehouse Snapshot
                |--------------------------------------------------------------------------
                */

                'warehouse_id'         => $purchaseRequisitionItem->warehouse_id,
                'warehouse_code'       => $warehouse?->warehouse_code,
                'warehouse_name'       => $warehouse?->warehouse_name,

                /*
                |--------------------------------------------------------------------------
                | Requirement
                |--------------------------------------------------------------------------
                */

                'required_date'        => $purchaseRequisitionItem->required_date,
                'delivery_location'    => $purchaseRequisition->delivery_location,

                /*
                |--------------------------------------------------------------------------
                | Buyer Assignment Default
                |--------------------------------------------------------------------------
                */

                'assigned_qty'         => $purchaseRequisitionItem->quantity,

                'supplier_id'          => null,

                'unit_price' => $this->pricingService
                    ->getLastPurchasePrice(
                        $purchaseRequisitionItem->item_id,
                    ),

                'quotation_number'     => null,
                'quotation_date'       => null,

                'lead_time_days'       => null,
                'delivery_date'        => null,

                'discount_percent'     => 0,
                'discount_amount'      => 0,

                'tax_percent'          => 0,
                'tax_amount'           => 0,

                'line_total'           => 0,

                'buyer_notes'          => null,

                'is_selected_supplier' => false,

                /*
                |--------------------------------------------------------------------------
                | Workflow
                |--------------------------------------------------------------------------
                */

                'status' => AssignmentMaterialRequisitionItem::STATUS_DRAFT,

                'approval_status' =>
                    AssignmentMaterialRequisitionItem::APPROVAL_PENDING,

                /*
                |--------------------------------------------------------------------------
                | Remarks
                |--------------------------------------------------------------------------
                */

                'remarks' => $purchaseRequisitionItem->remarks,

            ]);
        }
    }


    /*
    |--------------------------------------------------------------------------
    | Validation
    |--------------------------------------------------------------------------
    */

    /**
     * Validate Create Request.
     *
     * @throws ValidationException
     */
    protected function validateCreate(array $data): void
    {
        Validator::make($data, [

            'purchase_requisition_id' => [
                'required',
                'integer',
                'exists:purchase_requisitions,id',
            ],

            'assigned_to' => [
                'nullable',
                'integer',
                'exists:users,id',
            ],

            'assigned_by' => [
                'nullable',
                'integer',
                'exists:users,id',
            ],

            'remarks' => [
                'nullable',
                'string',
            ],

            'currency_id' => [
                'required',
                'integer',
                'exists:currencies,id',
            ],

            'exchange_rate' => [
                'required',
                'numeric',
                'gt:0',
            ],


        ])->validate();
    }

    /**
     * Validate Update Request.
     *
     * @throws ValidationException
     */
    protected function validateUpdate(array $data): void
    {
        Validator::make($data, [

            'assigned_to' => [
                'sometimes',
                'nullable',
                'integer',
                'exists:users,id',
            ],

            'assigned_by' => [
                'sometimes',
                'nullable',
                'integer',
                'exists:users,id',
            ],

            'remarks' => [
                'sometimes',
                'nullable',
                'string',
            ],


            'currency_id' => [
                'sometimes',
                'required',
                'integer',
                'exists:currencies,id',
            ],

            'exchange_rate' => [
                'sometimes',
                'required',
                'numeric',
                'gt:0',
            ],            

        ])->validate();
    }

    /**
     * Validate Buyer Assignment.
     *
     * @throws ValidationException
     */
    protected function validateBuyerAssignment(
        int $userId,
    ): void {

        Validator::make(

            [
                'assigned_to' => $userId,
            ],

            [
                'assigned_to' => [
                    'required',
                    'integer',
                    'exists:users,id',
                ],
            ]

        )->validate();
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
    protected function findById(
        int $id,
    ): AssignmentMaterialRequisition {

        return $this->model
            ->newQuery()
            ->findOrFail($id);
    }

    /**
     * Determine whether the Assignment Material Requisition
     * can be modified based on the current business rules.
     *
     * Business rules are delegated to the model in order to keep
     * the service independent from document status logic.
     */
    protected function canModify(
        AssignmentMaterialRequisition $assignment,
    ): bool {
        return $assignment->canEdit();
    }





    /*
    |--------------------------------------------------------------------------
    | Item Integration
    |--------------------------------------------------------------------------
    */

    /**
     * Refresh Header.
     */
    public function refreshHeader(
        int $id,
    ): AssignmentMaterialRequisition {

        return $this->findById($id)
            ->refresh();
    }

    /**
     * Determine whether document has items.
     */
    public function hasItems(
        int $id,
    ): bool {

        $assignment = $this->findById($id);

        return $assignment
            ->items()
            ->exists();
    }

    /**
     * Count assignment items.
     */
    public function itemCount(
        int $id,
    ): int {

        return $this
            ->findById($id)
            ->items()
            ->count();
    }

    /**
     * Refresh Item Summary.
     */

    // Implemented in AssignmentMaterialRequisitionItemService
    public function refreshItems(
        int $id,
    ): AssignmentMaterialRequisition {

        $assignment = $this->findById($id);

        $this->itemService
            ->refreshHeader($assignment);

        return $assignment
            ->refresh();
    }


    public function assignSupplier(
        int $itemId,
        ?int $supplierId,
    ): AssignmentMaterialRequisitionItem {

        return $this->db->transaction(function () use (
            $itemId,
            $supplierId,
        ) {

            $item = AssignmentMaterialRequisitionItem::query()
                ->findOrFail($itemId);

            if (! $item->canEdit()) {
                throw new RuntimeException(
                    'Item cannot be modified.'
                );
            }

            $item->update([
                'supplier_id' => $supplierId,
            ]);

            return $item->refresh();

        });

    }

    /**
     * Update Assignment Item.
     *
     * Buyer can update:
     * - Supplier
     * - Assigned Qty
     * - Unit Price
     * - Discount %
     * - Discount Amount
     */
    public function updateAssignmentItem(
        int $itemId,
        ?int $supplierId,
        float $assignedQty,
        float $unitPrice,
        float $discountPercent = 0,
        float $discountAmount = 0,
        ?int $taxId = null,
    ): AssignmentMaterialRequisitionItem {

        return $this->db->transaction(function () 

        use (
            $itemId,
            $supplierId,
            $assignedQty,
            $unitPrice,
            $discountPercent,
            $discountAmount,
            $taxId,
        ) {

            $item = AssignmentMaterialRequisitionItem::query()
                ->findOrFail($itemId);

            $tax = null;

            if ($taxId) {

                $tax = \App\Models\TaxMaster::query()
                    ->find($taxId);

            }

            if (! $item->canEdit()) {
                throw new RuntimeException(
                    'Item cannot be modified.'
                );
            }

            /*
            |--------------------------------------------------------------------------
            | Gross Amount
            |--------------------------------------------------------------------------
            */

            $grossAmount = round(
                $assignedQty * $unitPrice,
                2
            );

            /*
            |--------------------------------------------------------------------------
            | Discount
            |--------------------------------------------------------------------------
            */

            if ($discountPercent > 0) {

                $discountAmount = round(
                    $grossAmount * ($discountPercent / 100),
                    2,
                );

            } elseif ($discountAmount > 0 && $grossAmount > 0) {

                $discountPercent = round(
                    ($discountAmount / $grossAmount) * 100,
                    2,
                );

            }

            /*
            |--------------------------------------------------------------------------
            | Sub Total (Hidden)
            |--------------------------------------------------------------------------
            */

            $subTotal = max(
                0,
                $grossAmount - $discountAmount,
            );

            /*
            |--------------------------------------------------------------------------
            | Tax
            |--------------------------------------------------------------------------
            */

            $taxPercent = (float) ($tax?->tax_rate ?? 0);

            $taxAmount = round(
                $subTotal * ($taxPercent / 100),
                2,
            );

            /*
            |--------------------------------------------------------------------------
            | Grand Total
            |--------------------------------------------------------------------------
            */

            $grandTotal = $subTotal;

            if ($tax) {

                if ($tax->is_withholding) {

                    $grandTotal -= $taxAmount;

                } else {

                    $grandTotal += $taxAmount;

                }
            }

            $grandTotal = round($grandTotal, 2);



            /*
            |--------------------------------------------------------------------------
            | Update
            |--------------------------------------------------------------------------
            */

            $item->update([

                'supplier_id'       => $supplierId,
                'assigned_qty'      => $assignedQty,
                'unit_price'        => $unitPrice,
                'discount_percent'  => $discountPercent,
                'discount_amount'   => $discountAmount,
                'tax_id'            => $tax?->id,
                'tax_name'          => $tax?->tax_name,
                'tax_percent'       => $taxPercent,
                'tax_amount'        => $taxAmount,
                'line_total'        => $subTotal,
                'grand_total'       => $grandTotal,

            ]);

            /*
            |--------------------------------------------------------------------------
            | AMR Header Status
            |--------------------------------------------------------------------------
            */

            $assignment = $item->assignmentMaterialRequisition;

            if (
                $assignment
                && $assignment->status === AssignmentMaterialRequisition::STATUS_DRAFT
            ) {
                $assignment->update([
                    'status' => AssignmentMaterialRequisition::STATUS_UPDATED,
                ]);
            }

            return $item->refresh();

        });
    }


    /**
     * Validate workflow transition.
     */
    protected function validateWorkflowTransition(
        AssignmentMaterialRequisition $assignment,
        string $targetStatus,
    ): void {

        $allowed = $this->getAllowedTransitions();

        $current = $assignment->status;

        if (! in_array(
            $targetStatus,
            $allowed[$current] ?? [],
            true,
        )) {

            throw new RuntimeException(
                "Workflow transition from [{$current}] to [{$targetStatus}] is not allowed."
            );
        }
    }

    /**
     * Allowed AMR workflow transitions.
     *
     * AMR workflow ends at Waiting Approval.
     *
     * Approval is NOT performed on AMR.
     */
    protected function getAllowedTransitions(): array
    {
        return [

            /*
            |--------------------------------------------------------------------------
            | Draft
            |--------------------------------------------------------------------------
            */

            AssignmentMaterialRequisition::STATUS_DRAFT => [
                AssignmentMaterialRequisition::STATUS_UPDATED,
                AssignmentMaterialRequisition::STATUS_CANCELLED,
            ],

            /*
            |--------------------------------------------------------------------------
            | Updated
            |--------------------------------------------------------------------------
            */

            AssignmentMaterialRequisition::STATUS_UPDATED => [
                AssignmentMaterialRequisition::STATUS_WAITING_APPROVAL,
                AssignmentMaterialRequisition::STATUS_CANCELLED,
            ],

            /*
            |--------------------------------------------------------------------------
            | Assigned
            |--------------------------------------------------------------------------
            |
            | Kept for backward compatibility with existing AMR records.
            |
            */

            AssignmentMaterialRequisition::STATUS_ASSIGNED => [
                AssignmentMaterialRequisition::STATUS_WAITING_APPROVAL,
                AssignmentMaterialRequisition::STATUS_CANCELLED,
            ],

            /*
            |--------------------------------------------------------------------------
            | Waiting Approval
            |--------------------------------------------------------------------------
            |
            | FINAL AMR STATE.
            |
            | AMR does not transition to:
            | - Approved
            | - Rejected
            | - Completed
            |
            | Further approval belongs to the original
            | Material Requisition approval transaction.
            |
            */

            AssignmentMaterialRequisition::STATUS_WAITING_APPROVAL => [],

            /*
            |--------------------------------------------------------------------------
            | Legacy States
            |--------------------------------------------------------------------------
            |
            | These constants remain for backward compatibility
            | with historical AMR records.
            |
            | They are no longer part of the active AMR workflow.
            |
            */

            AssignmentMaterialRequisition::STATUS_APPROVED => [],

            AssignmentMaterialRequisition::STATUS_COMPLETED => [],

            AssignmentMaterialRequisition::STATUS_REJECTED => [],

            AssignmentMaterialRequisition::STATUS_CANCELLED => [],
        ];
    }


    /**
     * Copy Purchase Requisition header into Assignment snapshot.
     */
    protected function copyHeaderSnapshot(
        AssignmentMaterialRequisition $assignment,
        PurchaseRequisition $purchaseRequisition,
    ): void {

        $assignment->fill([

            /*
            |--------------------------------------------------------------------------
            | Purchase Requisition Snapshot
            |--------------------------------------------------------------------------
            */

            'pr_number'         => $purchaseRequisition->pr_no,

            'company_id'        => $purchaseRequisition->company_id,
            'business_unit_id'  => $purchaseRequisition->business_unit_id,
            'branch_id'         => $purchaseRequisition->branch_id,
            'department_id'     => $purchaseRequisition->department_id,
            'section_id'        => $purchaseRequisition->section_id,
            'cost_center_id'    => $purchaseRequisition->cost_center_id,
            'warehouse_id'      => $purchaseRequisition->warehouse_id,

            'requester_id'      => $purchaseRequisition->requester_id,

            'request_date'      => $purchaseRequisition->request_date,
            'required_date'     => $purchaseRequisition->required_date,

            'priority'          => $purchaseRequisition->priority,
            'reference_no'      => $purchaseRequisition->reference_no,
            'delivery_location' => $purchaseRequisition->delivery_location,

            'remarks'           => $purchaseRequisition->remarks,

        ]);
        $assignment->save();

    }

}

    
