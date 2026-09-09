<?php

declare(strict_types=1);

namespace App\Services\Purchasing;

use App\Models\AssignmentDirectMarket;
use App\Models\AssignmentDirectMarketItem;
use App\Models\DirectMarket;
use App\Models\TransactionNumbering;
use App\Services\Numbering\NumberingService;
use App\Services\Approval\ApprovalTransactionService;
use Illuminate\Database\DatabaseManager;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use RuntimeException;

class AssignmentDirectMarketService
{
    /**
     * Constructor.
     */
    public function __construct(
        protected AssignmentDirectMarket $model,
        protected NumberingService $numberingService,
        protected DatabaseManager $db,
        protected ApprovalTransactionService $approvalTransactionService,
    ) {
    }

    /*
    |--------------------------------------------------------------------------
    | CREATE FROM APPROVED DIRECT MARKET
    |--------------------------------------------------------------------------
    */

    /**
     * Create Assignment Direct Market from an approved Direct Market.
     *
     * Workflow:
     *
     * Direct Market L1 Approved
     *        ↓
     * Assignment Direct Market Draft
     *
     * Important:
     *
     * - Does NOT create AMR.
     * - Does NOT modify AMR.
     * - Does NOT create PO.
     * - Does NOT generate PO.
     * - Pricing is handled here, not in Direct Market.
     */
    public function createFromApprovedDirectMarket(
        int $directMarketId,
    ): AssignmentDirectMarket {

        return $this->db->transaction(
            function () use ($directMarketId): AssignmentDirectMarket {

                /*
                |--------------------------------------------------------------------------
                | Load Direct Market
                |--------------------------------------------------------------------------
                */

                $directMarket = DirectMarket::query()
                    ->with([
                        'items.item',
                        'items.uom',
                        'company',
                        'businessUnit',
                        'branch',
                        'department',
                        'costCenter',
                    ])
                    ->lockForUpdate()
                    ->findOrFail($directMarketId);

                /*
                |--------------------------------------------------------------------------
                | Validate DM State
                |--------------------------------------------------------------------------
                */

                if (
                    $directMarket->status
                    !== DirectMarket::STATUS_APPROVED
                ) {
                    throw new RuntimeException(
                        "Direct Market [{$directMarket->dm_no}] "
                        . 'must be Approved before Assignment Direct Market creation.'
                    );
                }

                /*
                |--------------------------------------------------------------------------
                | Validate Items
                |--------------------------------------------------------------------------
                */

                if ($directMarket->items->isEmpty()) {
                    throw new RuntimeException(
                        "Direct Market [{$directMarket->dm_no}] "
                        . 'must have at least one item.'
                    );
                }

                /*
                |--------------------------------------------------------------------------
                | Prevent Duplicate Assignment
                |--------------------------------------------------------------------------
                */

                $existing = $this->model
                    ->newQuery()
                    ->where(
                        'direct_market_id',
                        $directMarket->getKey()
                    )
                    ->latest('id')
                    ->first();

                if ($existing) {
                    return $existing->fresh([
                        'items',
                        'directMarket',
                    ]);
                }

                /*
                |--------------------------------------------------------------------------
                | Authenticated User
                |--------------------------------------------------------------------------
                */

                $userId = Auth::id();

                if ($userId === null) {
                    throw new RuntimeException(
                        'Authenticated user is required to create Assignment Direct Market.'
                    );
                }

                /*
                |--------------------------------------------------------------------------
                | Generate ADM Number
                |--------------------------------------------------------------------------
                */

                $documentNumber = $this->numberingService->generate(
                    documentType:
                        TransactionNumbering::DOC_ASSIGNMENT_DIRECT_MARKET,

                    companyId:
                        (int) $directMarket->company_id,

                    businessUnitId:
                        $directMarket->business_unit_id !== null
                            ? (int) $directMarket->business_unit_id
                            : null,

                    branchId:
                        $directMarket->branch_id !== null
                            ? (int) $directMarket->branch_id
                            : null,

                    departmentId:
                        $directMarket->department_id !== null
                            ? (int) $directMarket->department_id
                            : null,
                );

                /*
                |--------------------------------------------------------------------------
                | Create ADM Header
                |--------------------------------------------------------------------------
                */

                $assignment = $this->model->create([

                    'document_no' =>
                        $documentNumber,

                    'document_date' =>
                        today(),

                    /*
                    |--------------------------------------------------------------------------
                    | Source
                    |--------------------------------------------------------------------------
                    */

                    'direct_market_id' =>
                        $directMarket->getKey(),

                    /*
                    |--------------------------------------------------------------------------
                    | Header Snapshot
                    |--------------------------------------------------------------------------
                    */

                    'company_id' =>
                        $directMarket->company_id,

                    'business_unit_id' =>
                        $directMarket->business_unit_id,

                    'branch_id' =>
                        $directMarket->branch_id,

                    'department_id' =>
                        $directMarket->department_id,

                    'cost_center_id' =>
                        $directMarket->cost_center_id,

                    'warehouse_id' =>
                        $directMarket->warehouse_id,                        

                    'delivery_location' =>
                        $directMarket->delivery_location,

                    'reference_no' =>
                        $directMarket->reference_no,

                    'request_date' =>
                        $directMarket->request_date,

                    'required_date' =>
                        $directMarket->required_date,

                    'requester_id' =>
                        $directMarket->requester_id,

                    /*
                    |--------------------------------------------------------------------------
                    | Assignment
                    |--------------------------------------------------------------------------
                    */

                    'assigned_to' =>
                        null,

                    'assigned_by' =>
                        $userId,

                    'assigned_at' =>
                        now(),

                    /*
                    |--------------------------------------------------------------------------
                    | Workflow
                    |--------------------------------------------------------------------------
                    */

                    'status' =>
                        AssignmentDirectMarket::STATUS_DRAFT,

                    /*
                    |--------------------------------------------------------------------------
                    | Remarks
                    |--------------------------------------------------------------------------
                    */

                    'remarks' =>
                        'Automatically generated from Direct Market '
                        . $directMarket->dm_no,

                    /*
                    |--------------------------------------------------------------------------
                    | Audit
                    |--------------------------------------------------------------------------
                    */

                    'created_by' =>
                        $userId,
                ]);

                /*
                |--------------------------------------------------------------------------
                | Copy DM Items
                |--------------------------------------------------------------------------
                */

                foreach ($directMarket->items as $directMarketItem) {

                    $this->createAssignmentItem(
                        $assignment,
                        $directMarketItem,
                        $userId,
                    );
                }

                /*
                |--------------------------------------------------------------------------
                | Return Fresh Assignment
                |--------------------------------------------------------------------------
                */

                return $assignment->fresh([
                    'items',
                    'directMarket',
                ]);
            }
        );
    }

    /*
    |--------------------------------------------------------------------------
    | CREATE ASSIGNMENT ITEM
    |--------------------------------------------------------------------------
    */

    /**
     * Create Assignment DM item from Direct Market item.
     *
     * Pricing starts at zero and is resolved in Assignment DM.
     */
    protected function createAssignmentItem(
        AssignmentDirectMarket $assignment,
        $directMarketItem,
        ?int $userId,
    ): AssignmentDirectMarketItem {

        $item =
            $directMarketItem->item;

        $uom =
            $directMarketItem->uom;

        return $assignment->items()->create([

            /*
            |--------------------------------------------------------------------------
            | Source
            |--------------------------------------------------------------------------
            */

            'direct_market_item_id' =>
                $directMarketItem->getKey(),

            /*
            |--------------------------------------------------------------------------
            | Item Snapshot
            |--------------------------------------------------------------------------
            */

            'item_id' =>
                $directMarketItem->item_id,

            'item_code' =>
                $item?->item_code,

            'item_name' =>
                $item?->item_name,

            /*
            |--------------------------------------------------------------------------
            | Description
            |--------------------------------------------------------------------------
            */

            'item_description' =>
                $directMarketItem->remarks,

            'specification' =>
                null,

            /*
            |--------------------------------------------------------------------------
            | UOM Snapshot
            |--------------------------------------------------------------------------
            */

            'uom_id' =>
                $directMarketItem->uom_id,

            'uom_code' =>
                $uom?->uom_code,

            'uom_name' =>
                $uom?->uom_name,

            /*
            |--------------------------------------------------------------------------
            | Quantity
            |--------------------------------------------------------------------------
            */

            'requested_qty' =>
                $directMarketItem->qty,

            'assigned_qty' =>
                $directMarketItem->qty,

            /*
            |--------------------------------------------------------------------------
            | Delivery
            |--------------------------------------------------------------------------
            */

            'required_date' =>
                $directMarketItem->required_date
                ?? $assignment->required_date,

            'delivery_location' =>
                $directMarketItem->delivery_location
                ?? $assignment->delivery_location,

            /*
            |--------------------------------------------------------------------------
            | Supplier
            |--------------------------------------------------------------------------
            */

            'supplier_id' =>
                null,

            /*
            |--------------------------------------------------------------------------
            | Pricing
            |--------------------------------------------------------------------------
            |
            | Pricing belongs to Assignment DM.
            |
            */

            'unit_price' =>
                0.00,

            'quotation_number' =>
                null,

            'quotation_date' =>
                null,

            'lead_time_days' =>
                null,

            'delivery_date' =>
                null,

            /*
            |--------------------------------------------------------------------------
            | Discount
            |--------------------------------------------------------------------------
            */

            'discount_percent' =>
                0.00,

            'discount_amount' =>
                0.00,

            /*
            |--------------------------------------------------------------------------
            | Tax
            |--------------------------------------------------------------------------
            */

            'tax_id' =>
                null,

            'tax_name' =>
                null,

            'tax_percent' =>
                0.00,

            'tax_amount' =>
                0.00,

            /*
            |--------------------------------------------------------------------------
            | Amount
            |--------------------------------------------------------------------------
            */

            'gross_amount' =>
                0.00,

            'net_amount' =>
                0.00,

            'line_total' =>
                0.00,

            'grand_total' =>
                0.00,

            /*
            |--------------------------------------------------------------------------
            | Supplier Selection
            |--------------------------------------------------------------------------
            */

            'is_selected_supplier' =>
                false,

            /*
            |--------------------------------------------------------------------------
            | Notes
            |--------------------------------------------------------------------------
            */

            'buyer_notes' =>
                null,

            'remarks' =>
                $directMarketItem->remarks,

            /*
            |--------------------------------------------------------------------------
            | Workflow
            |--------------------------------------------------------------------------
            */

            'status' =>
                AssignmentDirectMarketItem::STATUS_DRAFT,

            'approval_status' =>
                AssignmentDirectMarketItem::APPROVAL_PENDING,

            /*
            |--------------------------------------------------------------------------
            | Audit
            |--------------------------------------------------------------------------
            */

            'created_by' =>
                $userId,
        ]);
    }


    /**
     * Submit Assignment Direct Market.
     *
     * ADM Workflow:
     *
     * Updated
     *   ↓
     * Submit
     *   ↓
     * Waiting Approval
     *   ↓
     * DM-APPROVAL
     *   ├── Level 1 : Dept Head
     *   └── Level 2 : Purchasing SPV
     *
     * ADM does not perform approval itself.
     * Approval is handled by the approval workspace.
     */
    public function submit(
        int $assignmentId,
    ): AssignmentDirectMarket {

        return $this->db->transaction(
            function () use ($assignmentId): AssignmentDirectMarket {

                /*
                |--------------------------------------------------------------------------
                | Load ADM
                |--------------------------------------------------------------------------
                */

                $assignment = $this->model
                    ->newQuery()
                    ->lockForUpdate()
                    ->with([
                        'items',
                        'directMarket',
                    ])
                    ->findOrFail($assignmentId);

                /*
                |--------------------------------------------------------------------------
                | Validate Status
                |--------------------------------------------------------------------------
                |
                | ADM can only be submitted after it has
                | been updated.
                |
                */

                if (
                    $assignment->status
                    !== AssignmentDirectMarket::STATUS_UPDATED
                ) {
                    throw new RuntimeException(
                        "Assignment Direct Market [{$assignment->document_no}] "
                        . 'can only be submitted while in Updated status.'
                    );
                }

                /*
                |--------------------------------------------------------------------------
                | Validate Items
                |--------------------------------------------------------------------------
                */

                if ($assignment->items->isEmpty()) {
                    throw new RuntimeException(
                        "Assignment Direct Market [{$assignment->document_no}] "
                        . 'must have at least one item before submission.'
                    );
                }

                /*
                |--------------------------------------------------------------------------
                | Validate Assigned Quantity
                |--------------------------------------------------------------------------
                */

                foreach ($assignment->items as $item) {

                    if (
                        (float) $item->assigned_qty <= 0
                    ) {
                        throw new RuntimeException(
                            "Assignment Direct Market [{$assignment->document_no}] "
                            . 'contains an item with invalid assigned quantity.'
                        );
                    }

                    if (
                        (float) $item->assigned_qty
                        >
                        (float) $item->requested_qty
                    ) {
                        throw new RuntimeException(
                            "Assignment Direct Market [{$assignment->document_no}] "
                            . 'contains assigned quantity greater than requested quantity.'
                        );
                    }
                }

                /*
                |--------------------------------------------------------------------------
                | Validate Supplier
                |--------------------------------------------------------------------------
                */

                if (blank($item->supplier_id)) {
                    throw new RuntimeException(
                        "Assignment Direct Market [{$assignment->document_no}] "
                        . 'contains an item without a supplier.'
                    );
                }

                /*
                |--------------------------------------------------------------------------
                | Validate Unit Price
                |--------------------------------------------------------------------------
                */

                if ((float) $item->unit_price <= 0) {
                    throw new RuntimeException(
                        "Assignment Direct Market [{$assignment->document_no}] "
                        . 'contains an item without a valid unit price.'
                    );
                }
                

                /*
                |--------------------------------------------------------------------------
                | Authenticated User
                |--------------------------------------------------------------------------
                */

                $userId = Auth::id();

                if ($userId === null) {
                    throw new RuntimeException(
                        'Authenticated user is required to submit Assignment Direct Market.'
                    );
                }

                /*
                |--------------------------------------------------------------------------
                | CREATE ADM APPROVAL TRANSACTION
                |--------------------------------------------------------------------------
                |
                | ADM has its own approval transaction.
                |
                | Transaction identity:
                |
                |   ASSIGNMENT_DIRECT_MARKET : ADM ID
                |
                | Approval master:
                |
                |   DM-APPROVAL / DIRECT_MARKET
                |
                | IMPORTANT:
                |
                | Do NOT reuse the original Direct Market transaction.
                |
                */

                $this->approvalTransactionService->create(
                    'ASSIGNMENT_DIRECT_MARKET',
                    (int) $assignment->getKey(),
                    $assignment->document_no,
                    $userId,
                    2,
                    'DIRECT_MARKET',
                );

                $assignment->update([
                    'status' => AssignmentDirectMarket::STATUS_WAITING_APPROVAL,
                    'updated_by' => $userId,
                ]);                

                /*
                |--------------------------------------------------------------------------
                | Return Fresh ADM
                |--------------------------------------------------------------------------
                */

                return $assignment->fresh([
                    'items',
                    'directMarket',
                ]);
            }
        );
    }

    /*
    |--------------------------------------------------------------------------
    | UPDATE
    |--------------------------------------------------------------------------
    */

    /**
     * Update Assignment Direct Market.
     *
     * Kept intentionally small.
     *
     * UI/Form layer can validate individual fields.
     * This service prevents editing submitted/approved records.
     */
    public function update(
        int $assignmentId,
        array $data,
    ): AssignmentDirectMarket {

        return $this->db->transaction(
            function () use (
                $assignmentId,
                $data,
            ): AssignmentDirectMarket {

                $assignment = $this->model
                    ->newQuery()
                    ->lockForUpdate()
                    ->findOrFail($assignmentId);

                    if (
                        ! in_array(
                            $assignment->status,
                            [
                                AssignmentDirectMarket::STATUS_DRAFT,
                                AssignmentDirectMarket::STATUS_UPDATED,
                            ],
                            true
                        )
                    ) {
                    throw new RuntimeException(
                        "Assignment Direct Market [{$assignment->document_no}] "
                        . 'can only be updated while in Draft status.'
                    );
                }

                $userId = Auth::id();

                if ($userId === null) {
                    throw new RuntimeException(
                        'Authenticated user is required to update Assignment Direct Market.'
                    );
                }

                $allowed = [

                    'assigned_to',
                    'remarks',
                ];

                $payload = [];

                foreach ($allowed as $field) {

                    if (
                        array_key_exists(
                            $field,
                            $data
                        )
                    ) {
                        $payload[$field] =
                            $data[$field];
                    }
                }

                if (! empty($payload)) {

                    $payload['updated_by'] =
                        $userId;

                    $assignment->update(
                        $payload
                    );
                }

                return $assignment->fresh([
                    'items',
                    'directMarket',
                ]);
            }
        );
    }

    /*
    |--------------------------------------------------------------------------
    | ITEM PRICING
    |--------------------------------------------------------------------------
    */

    /**
     * Update Assignment Direct Market Item pricing.
     *
     * ADM pricing is calculated independently from AMR.
     *
     * Formula:
     * Gross      = Unit Price × Assigned Qty
     * Discount   = Unit Price × Discount %
     * Net        = (Unit Price - Discount Amount) × Assigned Qty
     * Tax        = Net × Tax %
     * Line Total = Net + Tax
     */
    public function updateItemPricing(
        int $itemId,
        array $data,
    ): AssignmentDirectMarketItem {

        return $this->db->transaction(
            function () use (
                $itemId,
                $data,
            ): AssignmentDirectMarketItem {

                $item = AssignmentDirectMarketItem::query()
                    ->lockForUpdate()
                    ->findOrFail($itemId);

                $assignment = $item->assignmentDirectMarket;

                if (! $assignment) {
                    throw new RuntimeException(
                        "Assignment Direct Market Item [{$itemId}] "
                        . 'is not linked to an Assignment Direct Market.'
                    );
                }

                if (
                    ! in_array(
                        $assignment->status,
                        [
                            AssignmentDirectMarket::STATUS_DRAFT,
                            AssignmentDirectMarket::STATUS_UPDATED,
                        ],
                        true
                    )
                ) {
                    throw new RuntimeException(
                        "Assignment Direct Market [{$assignment->document_no}] "
                        . 'must be in Draft or Updated status before submission.'
                    );
                }

                $userId = Auth::id();

                if ($userId === null) {
                    throw new RuntimeException(
                        'Authenticated user is required to update Assignment Direct Market Item.'
                    );
                }

                /*
                |--------------------------------------------------------------------------
                | INPUT VALUES
                |--------------------------------------------------------------------------
                */

                $assignedQty =
                    array_key_exists(
                        'assigned_qty',
                        $data
                    )
                    ? (float) $data['assigned_qty']
                    : (float) $item->assigned_qty;

                $unitPrice =
                    array_key_exists(
                        'unit_price',
                        $data
                    )
                    ? (float) $data['unit_price']
                    : (float) $item->unit_price;

                $discountPercent =
                    array_key_exists(
                        'discount_percent',
                        $data
                    )
                    ? (float) $data['discount_percent']
                    : (float) $item->discount_percent;

                $discountAmount =
                    array_key_exists(
                        'discount_amount',
                        $data
                    )
                    ? (float) $data['discount_amount']
                    : (float) $item->discount_amount;                    

                $taxPercent =
                    array_key_exists(
                        'tax_percent',
                        $data
                    )
                    ? (float) $data['tax_percent']
                    : (float) $item->tax_percent;

                /*
                |--------------------------------------------------------------------------
                | VALIDATION
                |--------------------------------------------------------------------------
                */

                if ($assignedQty < 0) {
                    throw new RuntimeException(
                        'Assigned quantity cannot be negative.'
                    );
                }

                if ($assignedQty > (float) $item->requested_qty) {
                    throw new RuntimeException(
                        'Assigned quantity cannot exceed requested quantity.'
                    );
                }

                if ($unitPrice < 0) {
                    throw new RuntimeException(
                        'Unit price cannot be negative.'
                    );
                }

                if (
                    $discountPercent < 0
                    || $discountPercent > 100
                ) {
                    throw new RuntimeException(
                        'Discount percent must be between 0 and 100.'
                    );
                }

                if (
                    $taxPercent < 0
                    || $taxPercent > 100
                ) {
                    throw new RuntimeException(
                        'Tax percent must be between 0 and 100.'
                    );
                }

                /*
                |--------------------------------------------------------------------------
                | PRICING CALCULATION
                |--------------------------------------------------------------------------
                */

                $grossAmount = round(
                    $assignedQty * $unitPrice,
                    2
                );

                if ($discountPercent > 0) {

                    $discountAmount = round(
                        $grossAmount * ($discountPercent / 100),
                        2
                    );

                } elseif ($discountAmount > 0 && $grossAmount > 0) {

                    $discountPercent = round(
                        ($discountAmount / $grossAmount) * 100,
                        2
                    );
                }

                $netAmount = round(
                    max(0, $grossAmount - $discountAmount),
                    2
                );

                $taxAmount = round(
                    $netAmount
                    * ($taxPercent / 100),
                    2
                );

                $lineTotal = round(
                    $netAmount
                    + $taxAmount,
                    2
                );

                /*
                |--------------------------------------------------------------------------
                | UPDATE ITEM
                |--------------------------------------------------------------------------
                */

                $payload = [

                    'assigned_qty' =>
                        $assignedQty,

                    'unit_price' =>
                        $unitPrice,

                    'discount_percent' =>
                        $discountPercent,

                    'discount_amount' =>
                        $discountAmount,

                    'tax_percent' =>
                        $taxPercent,

                    'tax_amount' =>
                        $taxAmount,

                    'gross_amount' =>
                        $grossAmount,

                    'net_amount' =>
                        $netAmount,

                    'line_total' =>
                        $lineTotal,

                    'grand_total' =>
                        $lineTotal,

                    'updated_by' =>
                        $userId,
                ];

                $item->update($payload);


                /*
                |--------------------------------------------------------------------------
                | MARK ADM AS UPDATED
                |--------------------------------------------------------------------------
                |
                | A successful Assignment Item update means that the
                | Assignment Direct Market has been updated.
                |
                | Draft → Updated
                |
                | Pricing completeness is NOT used as the status gate.
                | Submit remains the business gate before approval.
                |
                |--------------------------------------------------------------------------
                */

                $assignment->refresh();

                if (
                    $assignment->status ===
                    AssignmentDirectMarket::STATUS_DRAFT
                ) {
                    $assignment->update([
                        'status' =>
                            AssignmentDirectMarket::STATUS_UPDATED,

                        'updated_by' =>
                            $userId,
                    ]);
                }       

                /*
                |--------------------------------------------------------------------------
                | OPTIONAL NON-PRICING FIELDS
                |--------------------------------------------------------------------------
                */

                $allowed = [

                    'supplier_id',
                    'quotation_number',
                    'quotation_date',
                    'lead_time_days',
                    'delivery_date',
                    'tax_id',
                    'tax_name',
                    'buyer_notes',
                    'remarks',
                ];

                foreach ($allowed as $field) {

                    if (
                        array_key_exists(
                            $field,
                            $data
                        )
                    ) {
                        $item->update([
                            $field =>
                                $data[$field],
                        ]);
                    }
                }

                /*
                |--------------------------------------------------------------------------
                | REFRESH ADM TOTALS
                |--------------------------------------------------------------------------
                */

                $this->refreshTotals(
                    $assignment
                );

                return $item->fresh([
                    'assignmentDirectMarket',
                    'directMarketItem',
                    'supplier',
                ]);
            }
        );
    }

    /**
     * Refresh Assignment Direct Market totals.
     */
    protected function refreshTotals(
        AssignmentDirectMarket $assignment,
    ): void {

        $items = $assignment->items()
            ->lockForUpdate()
            ->get();

        /*
        |--------------------------------------------------------------------------
        | ADM Header Has No Pricing Columns
        |--------------------------------------------------------------------------
        |
        | Pricing is stored at Assignment Direct Market Item level.
        |
        | Therefore this method does not update the ADM header.
        |
        | Ensure each item has a synchronized grand_total.
        |--------------------------------------------------------------------------
        */

        foreach ($items as $item) {

            $grandTotal = round(
                (float) $item->line_total,
                2
            );

            if (
                (float) $item->grand_total
                !== $grandTotal
            ) {

                $item->update([
                    'grand_total' =>
                        $grandTotal,
                ]);
            }
        }
    }

}