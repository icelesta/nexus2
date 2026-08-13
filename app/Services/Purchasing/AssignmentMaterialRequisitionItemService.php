<?php

declare(strict_types=1);

namespace App\Services\Purchasing;

use App\Models\AssignmentMaterialRequisition;
use App\Models\AssignmentMaterialRequisitionItem;
use App\Models\Supplier;
use App\Models\PurchaseRequisitionItem;

use Illuminate\Database\DatabaseManager;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;

use RuntimeException;

class AssignmentMaterialRequisitionItemService
{
    /**
     * Constructor.
     */
    public function __construct(
        protected AssignmentMaterialRequisitionItem $model,
        protected DatabaseManager $db,
    ) {
    }



    /*
    |--------------------------------------------------------------------------
    | Synchronization
    |--------------------------------------------------------------------------
    */

    /**
     * Copy Purchase Requisition Items
     * into Assignment Material Requisition Items.
     *
     * Creates transaction snapshot from
     * Purchase Requisition Items.
     *
     * This method is idempotent.
     * Existing snapshot items will be skipped.
     *
     * Future:
     * - Reload Assignment Items
     * - Re-Synchronize
     */

    public function copyFromPurchaseRequisition(
        AssignmentMaterialRequisition $assignment,
    ): void {

        $assignment->loadMissing([
            'purchaseRequisition.items.item',
            'purchaseRequisition.items.uom',
            'purchaseRequisition.items.warehouse',
        ]);

        $purchaseRequisition = $assignment->purchaseRequisition;

        if (! $purchaseRequisition) {
            throw new RuntimeException(
                'Purchase Requisition not found.'
            );
        }

        $this->db->transaction(function () use (
            $assignment,
            $purchaseRequisition,
        ) {

            foreach ($purchaseRequisition->items as $purchaseRequisitionItem) {

                $exists = $this->model
                    ->newQuery()
                    ->where(
                        'assignment_material_requisition_id',
                        $assignment->id,
                    )
                    ->where(
                        'purchase_requisition_item_id',
                        $purchaseRequisitionItem->id,
                    )
                    ->exists();

                if ($exists) {
                    continue;
                }

                $payload = $this->createSnapshot(
                    $purchaseRequisitionItem,
                );

                $payload['assignment_material_requisition_id']
                    = $assignment->id;

                $this->model
                    ->newQuery()
                    ->create($payload);
            }

        });

    }

    /**
     * Build snapshot payload from
     * Purchase Requisition Item.
     *
     * Snapshot values are copied from
     * the current master data to preserve
     * transaction history.
     */
    protected function createSnapshot(
        PurchaseRequisitionItem $item,
    ): array
    {

        /*
        |--------------------------------------------------------------------------
        | Transaction Snapshot
        |--------------------------------------------------------------------------
        |
        | Snapshot values are copied from
        | Purchase Requisition Item.
        |
        | These values are intentionally
        | duplicated to preserve transaction
        | history even if Item Master changes.
        |
        */        

        
        return [

            /*
            |--------------------------------------------------------------------------
            | Source Reference
            |--------------------------------------------------------------------------
            */

            'purchase_requisition_item_id' => $item->id,

            /*
            |--------------------------------------------------------------------------
            | Item Snapshot
            |--------------------------------------------------------------------------
            */

            'item_id' => $item->item_id,

            'item_code' => $item->item?->item_code,

            'item_name' => $item->item?->item_name,

            'item_description' => $item->item?->description,

            'specification' => $item->item?->specification,

            /*
            |--------------------------------------------------------------------------
            | UOM Snapshot
            |--------------------------------------------------------------------------
            */

            'uom_id' => $item->uom_id,

            'uom_code' => $item->uom?->uom_code,

            'uom_name' => $item->uom?->uom_name,

            /*
            |--------------------------------------------------------------------------
            | Warehouse Snapshot
            |--------------------------------------------------------------------------
            */

            'warehouse_id' => $item->warehouse_id,

            'warehouse_code' => $item->warehouse?->warehouse_code,

            'warehouse_name' => $item->warehouse?->warehouse_name,

            /*
            |--------------------------------------------------------------------------
            | Quantity Snapshot
            |--------------------------------------------------------------------------
            */

            'requested_qty' => $item->quantity,

            'approved_qty' => $item->quantity,

            'remaining_qty' => $item->quantity,

            'assigned_qty' => $item->quantity,

            /*
            |--------------------------------------------------------------------------
            | Requirement
            |--------------------------------------------------------------------------
            */

            'required_date' => $item->required_date,

            'delivery_location' => $item->purchaseRequisition?->delivery_location,

            /*
            |--------------------------------------------------------------------------
            | Buyer
            |--------------------------------------------------------------------------
            */

            'supplier_id' => null,

            'unit_price' => 0.00,

            'quotation_number' => null,

            'quotation_date' => null,

            'lead_time_days' => null,

            'delivery_date' => null,

            'discount_percent' => 0.00,

            'discount_amount' => 0.00,

            'tax_percent' => 0.00,

            'tax_amount' => 0.00,

            'line_total' => 0.00,

            'buyer_notes' => null,

            'is_selected_supplier' => false,

            /*
            |--------------------------------------------------------------------------
            | Workflow
            |--------------------------------------------------------------------------
            */

            'status' => AssignmentMaterialRequisitionItem::STATUS_DRAFT,

            'approval_status' => AssignmentMaterialRequisitionItem::APPROVAL_PENDING,

            'remarks' => $item->remarks,

        ];
    }


    /*
    |--------------------------------------------------------------------------
    | Create
    |--------------------------------------------------------------------------
    */

    /**
     * Create Assignment Material Requisition Item.
     */
    public function create(
        array $data,
    ): AssignmentMaterialRequisitionItem {

        $this->validateCreate($data);

        return $this->db->transaction(function () use ($data) {

            return $this->model->create($data);

        });
    }

    /*
    |--------------------------------------------------------------------------
    | Update
    |--------------------------------------------------------------------------
    */

    /**
     * Update Assignment Material Requisition Item.
     */
    public function update(
        int $id,
        array $data,
    ): AssignmentMaterialRequisitionItem {

        $this->validateUpdate($data);

        return $this->db->transaction(function () use (
            $id,
            $data,
        ) {

            $item = $this->findById($id);

            if (! $this->canModify($item)) {

                throw new RuntimeException(
                    'Assignment Material Requisition Item cannot be modified.'
                );
            }

            $item->update($data);

            return $this->refresh($item);

        });
    }

    /*
    |--------------------------------------------------------------------------
    | Delete
    |--------------------------------------------------------------------------
    */

    /**
     * Delete Assignment Material Requisition Item.
     */
    public function delete(
        int $id,
    ): bool {

        return $this->db->transaction(function () use ($id) {

            $item = $this->findById($id);

            if (! $this->canDelete($item)) {

                throw new RuntimeException(
                    'Assignment Material Requisition Item cannot be deleted.'
                );
            }

            return (bool) $item->delete();

        });
    }


    /*
    |--------------------------------------------------------------------------
    | Supplier
    |--------------------------------------------------------------------------
    */

    /**
     * Assign Supplier.
     */
    public function assignSupplier(
        int $id,
        int $supplierId,
    ): AssignmentMaterialRequisitionItem {

        return $this->db->transaction(function () use (
            $id,
            $supplierId,
        ) {

            $item = $this->findById($id);

            if (! $this->canModify($item)) {
                throw new RuntimeException(
                    'Assignment Material Requisition Item cannot be modified.'
                );
            }

            $this->validateSupplier($supplierId);

            // Nothing to do if supplier already assigned.
            if ((int) $item->supplier_id === $supplierId) {
                return $this->refresh($item);
            }

            $item->update([
                'supplier_id'          => $supplierId,
                'is_selected_supplier' => false,
            ]);

            return $this->refresh($item);

        });
    }


    /**
     * Change Supplier.
     */
    public function changeSupplier(
        int $id,
        int $supplierId,
    ): AssignmentMaterialRequisitionItem {

        return $this->assignSupplier(
            $id,
            $supplierId
        );
    }

    /**
     * Remove Supplier.
     */
    public function removeSupplier(
        int $id,
    ): AssignmentMaterialRequisitionItem {

        return $this->db->transaction(function () use ($id) {

            $item = $this->findById($id);

            $this->validateWorkflowTransition(
                $item,
                AssignmentMaterialRequisitionItem::STATUS_ASSIGNED,
            );

            if (! $this->canModify($item)) {

                throw new RuntimeException(
                    'Assignment Material Requisition Item cannot be modified.'
                );
            }            

            $item->update([
                'supplier_id'          => null,
                'is_selected_supplier' => false,
            ]);

            return $this->refresh($item);

        });
    }

    /*
    |--------------------------------------------------------------------------
    | Quotation
    |--------------------------------------------------------------------------
    */

    /**
     * Update Quotation.
     */
    public function updateQuotation(
        int $id,
        array $data,
    ): AssignmentMaterialRequisitionItem {

        $this->validateQuotation($data);

        return $this->db->transaction(function () use (
            $id,
            $data,
        ) {

            $item = $this->findById($id);

            if (! $this->canModify($item)) {

                throw new RuntimeException(
                    'Assignment Material Requisition Item cannot be modified.'
                );
            }

            $item->update([

                'quotation_number' => $data['quotation_number'] ?? null,

                'quotation_date' => $data['quotation_date'] ?? null,

            ]);

            return $this->refresh($item);

        });
    }


    /**
     * Update Lead Time.
     */
    public function updateLeadTime(
        int $id,
        int $days,
    ): AssignmentMaterialRequisitionItem {

        return $this->db->transaction(function () use (
            $id,
            $days,
        ) {

            $item = $this->findById($id);

            if (! $this->canModify($item)) {

                throw new RuntimeException(
                    'Assignment Material Requisition Item cannot be modified.'
                );
            }            

            $item->update([
                'lead_time_days' => $days,
            ]);

            return $this->refresh($item);

        });
    }

    /**
     * Update Delivery Date.
     */
    public function updateDeliveryDate(
        int $id,
        ?string $date,
    ): AssignmentMaterialRequisitionItem {

        return $this->db->transaction(function () use (
            $id,
            $date,
        ) {

            $item = $this->findById($id);

            if (! $this->canModify($item)) {

                throw new RuntimeException(
                    'Assignment Material Requisition Item cannot be modified.'
                );
            }            

            $item->update([
                'delivery_date' => $date,
            ]);

            return $this->refresh($item);

        });
    }

    /**
     * Validate Supplier.
     *
     * @throws ValidationException
     */
    protected function validateSupplier(
        int $supplierId,
    ): void {

        Validator::make(
            [
                'supplier_id' => $supplierId,
            ],
            [
                'supplier_id' => [
                    'required',
                    'integer',
                    'min:1',
                    'exists:suppliers,id',
                ],
            ]
        )->validate();

        $supplier = Supplier::query()
            ->select([
                'id',
                'is_active',
            ])
            ->find($supplierId);

        if (! $supplier?->is_active) {

            throw ValidationException::withMessages([
                'supplier_id' => [
                    'Selected supplier is inactive.',
                ],
            ]);
        }
    }


    /**
     * Validate Quotation.
     *
     * @throws ValidationException
     */
    protected function validateQuotation(
        array $data,
    ): void {

        Validator::make($data, [

            'quotation_number' => [
                'nullable',
                'string',
                'max:100',
            ],

            'quotation_date' => [
                'nullable',
                'date',
            ],

        ])->validate();
    }



    /*
    |--------------------------------------------------------------------------
    | Validation
    |--------------------------------------------------------------------------
    */

    /**
     * Validate item creation.
     *
     * @throws ValidationException
     */
    protected function validateCreate(array $data): void
    {
        Validator::make($data, [

            'assignment_material_requisition_id' => [
                'required',
                'integer',
                'exists:assignment_material_requisitions,id',
            ],

            'purchase_requisition_item_id' => [
                'required',
                'integer',
                'exists:purchase_requisition_items,id',
            ],

        ])->validate();
    }

    /**
     * Validate item update.
     *
     * @throws ValidationException
     */
    protected function validateUpdate(array $data): void
    {
        Validator::make($data, [

            'supplier_id' => [
                'sometimes',
                'nullable',
                'integer',
                'exists:suppliers,id',
            ],

        ])->validate();
    }


    /**
     * Validate assigned quantity.
     */
    protected function validateAssignedQuantity(
        array $data,
    ): void {

        Validator::make($data, [
            'assigned_qty' => [
                'required',
                'numeric',
                'min:0.0001',
            ],
        ])->validate();
    }


    /*
    |--------------------------------------------------------------------------
    | Pricing
    |--------------------------------------------------------------------------
    */

    /**
     * Update Quoted Price.
     */
    public function updatePrice(
        int $id,
        float $price,
    ): AssignmentMaterialRequisitionItem {

        $this->validatePrice($price);

        return $this->db->transaction(function () use (
            $id,
            $price,
        ) {

            $item = $this->findById($id);

            if (! $this->canModify($item)) {

                throw new RuntimeException(
                    'Assignment Material Requisition Item cannot be modified.'
                );
            }            

            $item->update([
                'unit_price' => $price,
            ]);

            return $this->refreshPricing($item);

        });
    }


    /**
     * Update Discount.
     */
    public function updateDiscount(
        int $id,
        float $percent,
    ): AssignmentMaterialRequisitionItem {

        return $this->db->transaction(function () use (
            $id,
            $percent,
        ) {

            $item = $this->findById($id);

            if (! $this->canModify($item)) {

                throw new RuntimeException(
                    'Assignment Material Requisition Item cannot be modified.'
                );
            }              

            $amount = round(
                $item->unit_price * ($percent / 100),
                2
            );

            $item->update([
                'discount_percent' => $percent,
                'discount_amount'  => $amount,
            ]);

            return $this->refreshPricing($item);

        });
    }

    /**
     * Update Tax.
     */
    public function updateTax(
        int $id,
        float $percent,
    ): AssignmentMaterialRequisitionItem {

        return $this->db->transaction(function () use (
            $id,
            $percent,
        ) {

            $item = $this->findById($id);

            if (! $this->canModify($item)) {

                throw new RuntimeException(
                    'Assignment Material Requisition Item cannot be modified.'
                );
            }              

            $subtotal = $item->unit_price
                - $item->discount_amount;

            $tax = round(
                $subtotal * ($percent / 100),
                2
            );

            $item->update([
                'tax_percent' => $percent,
            ]);

            return $this->refreshPricing($item);

        });
    }

    /**
     * Update Assigned Quantity.
     */
    public function updateAssignedQuantity(
        int $id,
        float $quantity,
    ): AssignmentMaterialRequisitionItem {

        $this->validateAssignedQuantity([
            'assigned_qty' => $quantity,
        ]);

        return $this->db->transaction(function () use (
            $id,
            $quantity,
        ) {

            $item = $this->findById($id);

            if (! $this->canModify($item)) {

                throw new RuntimeException(
                    'Assignment Material Requisition Item cannot be modified.'
                );
            }

            $item->update([
                'assigned_qty' => $quantity,
            ]);

            return $this->refreshPricing($item);

        });
    }    

    /**
     * Calculate Line Total.
     */
    public function calculateLineTotal(
        int $id,
    ): AssignmentMaterialRequisitionItem {

        return $this->db->transaction(function () use ($id) {

            $item = $this->findById($id);

            if (! $this->canModify($item)) {

                throw new RuntimeException(
                    'Assignment Material Requisition Item cannot be modified.'
                );
            }

            return $this->refreshPricing($item);

        });
    }


    /**
     * Calculate Net Price.
     */
    public function calculateNetPrice(
        int $id,
    ): float {

        $item = $this->findById($id);

        return round(

            $item->unit_price
            - $item->discount_amount,

            2

        );
    }

    /**
     * Validate Price.
     *
     * @throws ValidationException
     */
    protected function validatePrice(
        float $price,
    ): void {

        Validator::make(

            [
                'unit_price' => $price,
            ],

            [
                'unit_price' => [
                    'required',
                    'numeric',
                    'min:0',
                ],
            ]

        )->validate();
    }

    /**
     * Validate workflow transition.
     *
     * @throws RuntimeException
     */
    protected function validateWorkflowTransition(
        AssignmentMaterialRequisitionItem $item,
        string $targetStatus,
    ): void {

        $allowed = $this->getAllowedTransitions(
            $item->status
        );

        if (! in_array($targetStatus, $allowed, true)) {

            throw new RuntimeException(
                sprintf(
                    'Workflow transition from [%s] to [%s] is not allowed.',
                    $item->status,
                    $targetStatus,
                )
            );
        }
    }

    /**
     * Get allowed workflow transitions.
     */
    protected function getAllowedTransitions(
        string $status,
    ): array {

        return match ($status) {

            AssignmentMaterialRequisitionItem::STATUS_DRAFT => [
                AssignmentMaterialRequisitionItem::STATUS_ASSIGNED,
                AssignmentMaterialRequisitionItem::STATUS_CANCELLED,
            ],

            AssignmentMaterialRequisitionItem::STATUS_ASSIGNED => [
                AssignmentMaterialRequisitionItem::STATUS_WAITING_APPROVAL,
                AssignmentMaterialRequisitionItem::STATUS_CANCELLED,
            ],

            AssignmentMaterialRequisitionItem::STATUS_WAITING_APPROVAL => [
                AssignmentMaterialRequisitionItem::STATUS_APPROVED,
                AssignmentMaterialRequisitionItem::STATUS_REJECTED,
                AssignmentMaterialRequisitionItem::STATUS_CANCELLED,
            ],

            AssignmentMaterialRequisitionItem::STATUS_APPROVED => [],

            AssignmentMaterialRequisitionItem::STATUS_REJECTED => [],

            AssignmentMaterialRequisitionItem::STATUS_CANCELLED => [],

            default => [],
        };
    }


    /*
    |--------------------------------------------------------------------------
    | Workflow
    |--------------------------------------------------------------------------
    */

    /**
     * Select Supplier.
     */
    public function validateSupplierSelection(
        int $id,
    ): AssignmentMaterialRequisitionItem {

        return $this->db->transaction(function () use ($id) {

            $item = $this->findById($id);

            $this->validateWorkflowTransition(
                $item,
                AssignmentMaterialRequisitionItem::STATUS_ASSIGNED,
            );

            if ($item->supplier_id === null) {

                throw new RuntimeException(
                    'Supplier has not been assigned.'
                );
            }

            $this->validateSupplierSelection($item);

            $item->update([
                'is_selected_supplier' => true,
            ]);

            return $this->refresh($item);

        });
    }

    /**
     * Unselect Supplier.
     */
    public function unselectSupplier(
        int $id,
    ): AssignmentMaterialRequisitionItem {

        return $this->db->transaction(function () use ($id) {

            $item = $this->findById($id);

            if (! $this->canModify($item)) {

                throw new RuntimeException(
                    'Assignment Material Requisition Item cannot be modified.'
                );
            }        

            $item->update([
                'is_selected_supplier' => false,
            ]);

            return $this->refresh($item);

        });
    }

    /**
     * Approve Item.
     */
    public function approve(
        int $id,
    ): AssignmentMaterialRequisitionItem {

        return $this->db->transaction(function () use ($id) {

        $item = $this->findById($id);

        $this->validateWorkflowTransition(
            $item,
            AssignmentMaterialRequisitionItem::STATUS_APPROVED,
        );

        if (! $this->canModify($item)) {

            throw new RuntimeException(
                'Assignment Material Requisition Item cannot be modified.'
            );
        }

        $this->validateSupplierSelection($item);

        if (! $item->isSelectedSupplier()) {

            throw ValidationException::withMessages([
                'supplier_id' => [
                    'Selected supplier must be confirmed before approval.',
                ],
            ]);
        }        

        $item->update([
            'status' => AssignmentMaterialRequisitionItem::STATUS_APPROVED,
            'approval_status' => AssignmentMaterialRequisitionItem::APPROVAL_APPROVED,
        ]);
            return $this->refresh($item);

        });
    }

    /**
     * Reject Item.
     */
    public function reject(
        int $id,
    ): AssignmentMaterialRequisitionItem {

        return $this->db->transaction(function () use ($id) {

            $item = $this->findById($id);

            if (! $this->canModify($item)) {

                throw new RuntimeException(
                    'Assignment Material Requisition Item cannot be modified.'
                );
            }


            $this->validateWorkflowTransition(
                $item,
                AssignmentMaterialRequisitionItem::STATUS_REJECTED,
            );

            $item->update([
                'status' => AssignmentMaterialRequisitionItem::STATUS_REJECTED,
                'approval_status' => AssignmentMaterialRequisitionItem::APPROVAL_REJECTED,
            ]);

            return $this->refresh($item);

        });
    }

    /*
    |--------------------------------------------------------------------------
    | Header Integration
    |--------------------------------------------------------------------------
    */

    /**
     * Refresh Header.
     */
    public function refreshHeader(
        AssignmentMaterialRequisition $header,
    ): void {

        $header->refresh();
    }


    /**
     * Count Selected Supplier.
     */
    public function countSelectedSupplier(
        int $headerId,
    ): int {

        return $this->model
            ->newQuery()
            ->where(
                'assignment_material_requisition_id',
                $headerId
            )
            ->where(
                'is_selected_supplier',
                true
            )
            ->count();
    }


    /**
     * Determine whether header has selected supplier.
     */
    public function hasSelectedSupplier(
        int $headerId,
    ): bool {

        return $this->countSelectedSupplier(
            $headerId
        ) > 0;
    }


    /**
     * Recalculate Item Pricing.
     */
    public function recalculate(
        int $id,
    ): AssignmentMaterialRequisitionItem {

        return $this->db->transaction(function () use ($id) {

            $item = $this->findById($id);

            $this->validateWorkflowTransition(
                $item,
                AssignmentMaterialRequisitionItem::STATUS_ASSIGNED,
            );            

            if (! $this->canModify($item)) {
                throw new RuntimeException(
                    'Assignment Material Requisition Item cannot be modified.'
                );
            }

            return $this->refreshPricing($item);

        });
    }


    /*
    |--------------------------------------------------------------------------
    | Helper
    |--------------------------------------------------------------------------
    */

    /**
     * Find Assignment Material Requisition Item.
     *
     * @throws ModelNotFoundException
     */
    protected function findById(
        int $id,
    ): AssignmentMaterialRequisitionItem {

        return $this->model
            ->newQuery()
            ->findOrFail($id);
    }

    /**
     * Determine whether item can be modified.
     */
    protected function canModify(
        AssignmentMaterialRequisitionItem $item,
    ): bool {

        return $item->canEdit();
    }

    /**
     * Determine whether item can be deleted.
     */
    protected function canDelete(
        AssignmentMaterialRequisitionItem $item,
    ): bool {

        return $item->canDelete();
    }

    /**
     * Refresh model instance.
     */
    protected function refresh(
        AssignmentMaterialRequisitionItem $item,
    ): AssignmentMaterialRequisitionItem {

        return $item->refresh();
    }

    /**
     * Refresh Pricing.
     */
    protected function refreshPricing(
        AssignmentMaterialRequisitionItem $item,
    ): AssignmentMaterialRequisitionItem {

    $subtotal = $this->calculateSubtotal($item);

    $tax = $this->calculateTax(
        $subtotal,
        $item->tax_percent,
    );

    $lineTotal = round(
        $subtotal + $tax,
        2);

    $item->update([
        'tax_amount' => $tax,
        'line_total' => $lineTotal,
    ]);

    $this->refreshHeaderTotals($item);

    return $item->refresh();
    }

    /**
     * Refresh Header Total.
     */
    protected function refreshHeaderTotals(
        AssignmentMaterialRequisitionItem $item,
    ): void {

        $header = $item->assignmentMaterialRequisition;

        if (! $header) {
            return;
        }

        $items = $header->items;

        $subtotal = $items->sum(function ($item) {
            return (
                ($item->unit_price - $item->discount_amount)
                * $item->assigned_qty
            );
        });

        $tax = $items->sum('tax_amount');

        $grandTotal = $items->sum('line_total');

        $header->update([
            'subtotal'     => round($subtotal, 2),
            'tax_amount'   => round($tax, 2),
            'grand_total'  => round($grandTotal, 2),
        ]);
    }

    /**
     * Calculate Subtotal.
     */
    protected function calculateSubtotal(
        AssignmentMaterialRequisitionItem $item,
    ): float {

        return round(
            $this->calculateNetUnitPrice($item)
            * $item->assigned_qty,
            2
        );
    }

    /**
     * Calculate Tax.
     */
    protected function calculateTax(
        float $subtotal,
        float $taxPercent,
    ): float {

        return round(
            $subtotal * ($taxPercent / 100),
            2
        );
    }

    /**
     * Calculate Net Unit Price.
     */
    protected function calculateNetUnitPrice(
        AssignmentMaterialRequisitionItem $item,
    ): float {

        return round(
            max(
                0,
                $item->unit_price - $item->discount_amount
            ),
            2
        );
    }

    
}