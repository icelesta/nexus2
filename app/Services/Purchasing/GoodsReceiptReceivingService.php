<?php

declare(strict_types=1);

namespace App\Services\Purchasing;

use App\Models\AssignmentDirectMarket;
use App\Models\AssignmentDirectMarketItem;
use App\Models\GoodsReceipt;
use App\Models\GoodsReceiptItem;
use App\Models\PurchaseOrder;
use App\Models\PurchaseOrderItem;
use Illuminate\Database\DatabaseManager;
use RuntimeException;
use App\Services\Inventory\InventoryStockPostingService;

class GoodsReceiptReceivingService
{
    /*
    |--------------------------------------------------------------------------
    | Constructor
    |--------------------------------------------------------------------------
    */

    public function __construct(
        protected GoodsReceipt $model,
        protected DatabaseManager $db,
    ) {
    }


    /**
     * Submit Draft Receiving Record.
     *
     * Uses draft_receive_qty only.
     * Does not read or update allocated_qty.
     */
    public function submitDraft(
        int $goodsReceiptId,
        ?int $userId = null,
    ): GoodsReceipt {
        return $this->db->transaction(function () use (
            $goodsReceiptId,
            $userId,
        ): GoodsReceipt {
            $actorId = $userId ?? auth()->id();

            if (! $actorId) {
                throw new RuntimeException(
                    'An authenticated user is required to submit this RR.'
                );
            }

            // 1. Lock RR header.
            $goodsReceipt = GoodsReceipt::query()
                ->lockForUpdate()
                ->findOrFail($goodsReceiptId);

            // Preserve original header values for audit.
            $oldRrStatus = $goodsReceipt->rr_status;
            $oldItemsStatus = $goodsReceipt->items_status;
            $oldStatus = $goodsReceipt->status;

            if (strtoupper((string) $goodsReceipt->rr_status) !== 'DRAFT') {
                throw new RuntimeException(
                    'Only a DRAFT Receiving Record can be submitted.'
                );
            }

            $isPurchaseOrder = (int) $goodsReceipt->purchase_order_id > 0;
            $isAssignment = (int) $goodsReceipt->assignment_direct_market_id > 0;

            if ($isPurchaseOrder === $isAssignment) {
                throw new RuntimeException(
                    'The RR must have exactly one valid source document.'
                );
            }

            // 2. Lock source document.
            $purchaseOrder = null;
            $assignment = null;

            if ($isPurchaseOrder) {
                $purchaseOrder = PurchaseOrder::query()
                    ->lockForUpdate()
                    ->findOrFail($goodsReceipt->purchase_order_id);

                if (
                    $purchaseOrder->approval_status
                    !== PurchaseOrder::APPROVAL_APPROVED
                ) {
                    throw new RuntimeException(
                        'The source Purchase Order must be Approved.'
                    );
                }

                $foreignKey = 'purchase_order_item_id';
                $sourceTable = 'purchase_order_items';
                $sourceParentKey = 'purchase_order_id';
                $sourceParentId = (int) $purchaseOrder->getKey();
                $quantityColumn = 'ordered_qty';
            } else {
                $assignment = AssignmentDirectMarket::query()
                    ->lockForUpdate()
                    ->findOrFail($goodsReceipt->assignment_direct_market_id);

                if (
                    $assignment->status
                    !== AssignmentDirectMarket::STATUS_APPROVED
                ) {
                    throw new RuntimeException(
                        'The source Assignment Direct Market must be Approved.'
                    );
                }

                $foreignKey = 'assignment_direct_market_item_id';
                $sourceTable = 'assignment_direct_market_items';
                $sourceParentKey = 'assignment_direct_market_id';
                $sourceParentId = (int) $assignment->getKey();
                $quantityColumn = 'assigned_qty';
            }

            // 3. Lock RR items.
            $rrItems = GoodsReceiptItem::query()
                ->where('goods_receipt_id', $goodsReceipt->getKey())
                ->orderBy('id')
                ->lockForUpdate()
                ->get();

            if ($rrItems->isEmpty()) {
                throw new RuntimeException(
                    'This Receiving Record has no items.'
                );
            }


            // 4. Validate Draft quantities and QC.
            $positiveItems = [];
            $hasOutstandingRows = false;
            $sourceIds = [];

            foreach ($rrItems as $item) {
                if ((float) $item->received_qty > 0) {
                    throw new RuntimeException(
                        "RR item [{$item->getKey()}] already has actual received quantity."
                    );
                }

                $qty = round((float) $item->draft_receive_qty, 4);

                if ($qty < 0) {
                    throw new RuntimeException(
                        "RR item [{$item->getKey()}] has an invalid negative quantity."
                    );
                }

                // Zero quantity remains Outstanding.
                if ($qty == 0.0) {
                    $hasOutstandingRows = true;
                    continue;
                }

                // Positive quantity without QC Passed remains Outstanding.
                if (! (bool) $item->qc_passed) {
                    $hasOutstandingRows = true;
                    continue;
                }

                $sourceId = (int) $item->{$foreignKey};

                if ($sourceId <= 0) {
                    throw new RuntimeException(
                        "RR item [{$item->getKey()}] has no valid source item."
                    );
                }

                $positiveItems[$item->getKey()] = [
                    'item' => $item,
                    'source_id' => $sourceId,
                    'quantity' => $qty,
                ];

                $sourceIds[$sourceId] = true;
            }

            if ($positiveItems === []) {
                throw new RuntimeException(
                    'No items are ready to submit. At least one item must have a positive Receive Qty and QC Passed.'
                );
            }


            // 5. Lock all source items in deterministic ID order.
            $sourceIds = array_keys($sourceIds);
            sort($sourceIds, SORT_NUMERIC);

            $sourceItems = [];

            foreach ($sourceIds as $sourceId) {
                $sourceQuery = $isPurchaseOrder
                    ? PurchaseOrderItem::query()
                    : AssignmentDirectMarketItem::query();

                $source = $sourceQuery
                    ->where($sourceParentKey, $sourceParentId)
                    ->lockForUpdate()
                    ->find($sourceId);

                if (! $source) {
                    throw new RuntimeException(
                        "Source item [{$sourceId}] does not belong to this RR source."
                    );
                }

                $sourceItems[$sourceId] = $source;
            }

            // 6. Validate source item snapshot and UOM.
            foreach ($positiveItems as $row) {
                /** @var GoodsReceiptItem $item */
                $item = $row['item'];

                $source = $sourceItems[$row['source_id']];

                if ((int) $item->item_id !== (int) $source->item_id) {
                    throw new RuntimeException(
                        "RR item [{$item->getKey()}] does not match its source item."
                    );
                }

                if ((int) $item->uom_id !== (int) $source->uom_id) {
                    throw new RuntimeException(
                        "RR item [{$item->getKey()}] has a mismatched UOM."
                    );
                }
            }

            // 6A. Prepare purchase cost data for inventory valuation.
            if ($isPurchaseOrder) {
                $purchaseOrder->loadMissing('currency');

                $currencyCode = strtoupper(
                    (string) $purchaseOrder->currency?->currency_code
                );

                $exchangeRate = (float) $purchaseOrder->exchange_rate;

                if ($currencyCode === '' || $exchangeRate <= 0) {
                    throw new RuntimeException(
                        'Purchase Order currency or exchange rate is invalid for inventory valuation.'
                    );
                }
            } else {
                $assignment->loadMissing('directMarket.currency');

                $currencyCode = strtoupper(
                    (string) $assignment->directMarket?->currency?->currency_code
                );

                if ($currencyCode !== 'IDR') {
                    throw new RuntimeException(
                        'Inventory valuation for Direct Market currently supports IDR transactions only.'
                    );
                }

                $exchangeRate = 1;
            }

            foreach ($positiveItems as $key => $row) {
                $source = $sourceItems[$row['source_id']];

                $sourceQty = (float) (
                    $isPurchaseOrder
                        ? $source->ordered_qty
                        : $source->assigned_qty
                );

                $netAmount = (float) $source->net_amount;

                if ($sourceQty <= 0 || $netAmount < 0) {
                    throw new RuntimeException(
                        "Invalid purchase cost data for source item [{$row['source_id']}]."
                    );
                }

                $purchaseUnitCost = round(
                    $netAmount / $sourceQty,
                    4
                );

                $unitCostIdr = round(
                    $purchaseUnitCost * $exchangeRate,
                    4
                );

                $positiveItems[$key]['purchase_unit_cost'] = $purchaseUnitCost;
                $positiveItems[$key]['purchase_currency_code'] = $currencyCode;
                $positiveItems[$key]['exchange_rate'] = $exchangeRate;
                $positiveItems[$key]['unit_cost_idr'] = $unitCostIdr;
            }

            // 7. Calculate previous actual receiving from active RRs only.
            $receivedBySource = [];

            foreach ($sourceIds as $sourceId) {
                $receivedBySource[$sourceId] = (float) GoodsReceiptItem::query()
                    ->where($foreignKey, $sourceId)
                    ->whereNull('deleted_at')
                    ->whereHas('goodsReceipt', function ($query) use ($goodsReceipt) {
                        $query
                            ->whereNull('deleted_at')
                            ->where('id', '<>', $goodsReceipt->getKey())
                            ->whereIn('rr_status', ['RECEIVED', 'CLOSE']);
                    })
                    ->sum('received_qty');
            }

            // 8. Validate cumulative quantities before writing anything.
            $newTotals = $receivedBySource;

            foreach ($positiveItems as $row) {
                $sourceId = $row['source_id'];
                $source = $sourceItems[$sourceId];

                $sourceQty = round(
                    (float) (
                        $isPurchaseOrder
                            ? $source->ordered_qty
                            : $source->assigned_qty
                    ),
                    4
                );

                if ($sourceQty <= 0) {
                    throw new RuntimeException(
                        "Source item [{$sourceId}] has an invalid source quantity."
                    );
                }

                $newTotals[$sourceId] = round(
                    $newTotals[$sourceId] + $row['quantity'],
                    4
                );

                if ($newTotals[$sourceId] > ($sourceQty + 0.00005)) {
                    throw new RuntimeException(
                        "Receiving quantity exceeds the remaining source quantity for item [{$sourceId}]."
                    );
                }
            }

            // 9. Persist actual received quantities.
            foreach ($positiveItems as $row) {
                /** @var GoodsReceiptItem $item */
                $item = $row['item'];

                $oldQty = (float) $item->received_qty;
                $newQty = $row['quantity'];

                $item->forceFill([
                    'received_qty' => $newQty,
                    'item_status' => 'RECEIVED',
                    'updated_by' => $actorId,
                ])->save();

                app(GoodsReceiptAuditService::class)->record(
                    goodsReceipt: $goodsReceipt,
                    eventType: GoodsReceiptAuditService::EVENT_SUBMIT,
                    goodsReceiptItem: $item,
                    fieldName: 'received_qty',
                    oldValue: $oldQty,
                    newValue: $newQty,
                    metadata: [
                        'draft_receive_qty' => $newQty,
                        'qc_passed' => true,
                        'action' => 'draft_item_submitted',
                    ],
                    userId: (int) $actorId,
                );
            }

            // 10. Determine cumulative completion across every source item.
            $allSourceItems = $isPurchaseOrder
                ? PurchaseOrderItem::query()
                    ->where('purchase_order_id', $sourceParentId)
                    ->orderBy('id')
                    ->lockForUpdate()
                    ->get()
                : AssignmentDirectMarketItem::query()
                    ->where('assignment_direct_market_id', $sourceParentId)
                    ->orderBy('id')
                    ->lockForUpdate()
                    ->get();

            if ($allSourceItems->isEmpty()) {
                throw new RuntimeException(
                    'The source document has no items.'
                );
            }

            $allSourceComplete = true;

            foreach ($allSourceItems as $source) {
                $sourceId = (int) $source->getKey();

                $sourceQty = round(
                    (float) (
                        $isPurchaseOrder
                            ? $source->ordered_qty
                            : $source->assigned_qty
                    ),
                    4
                );


                $totalReceived = (float) GoodsReceiptItem::query()
                    ->where($foreignKey, $sourceId)
                    ->whereNull('deleted_at')
                    ->whereHas('goodsReceipt', function ($query) use ($goodsReceipt) {
                        $query
                            ->whereNull('deleted_at')
                            ->where(function ($statusQuery) use ($goodsReceipt) {
                                $statusQuery
                                    ->whereIn('rr_status', ['RECEIVED', 'CLOSE'])
                                    ->orWhere('id', $goodsReceipt->getKey());
                            });
                    })
                    ->sum('received_qty');


                if ($totalReceived < ($sourceQty - 0.00005)) {
                    $allSourceComplete = false;
                    break;
                }
            }


            // 11. Determine agreed RR statuses.
            $newRrStatus = (
                $allSourceComplete && ! $hasOutstandingRows
            )
                ? 'CLOSE'
                : 'RECEIVED';

            $newItemsStatus = $allSourceComplete
                ? 'RECEIVED'
                : 'PARTIAL RECEIVED';


            // Keep status = Received for CompletionService compatibility.
            $goodsReceipt->forceFill([
                'rr_status' => $newRrStatus,
                'items_status' => $newItemsStatus,
                'status' => GoodsReceipt::STATUS_RECEIVED,
                'updated_by' => $actorId,
            ])->save();

            // 12. Header audit.
            foreach ([
                'rr_status' => [$oldRrStatus, $newRrStatus],
                'items_status' => [$oldItemsStatus, $newItemsStatus],
                'status' => [$oldStatus, GoodsReceipt::STATUS_RECEIVED],
            ] as $field => [$oldValue, $newValue]) {
                if ((string) $oldValue === (string) $newValue) {
                    continue;
                }

                app(GoodsReceiptAuditService::class)->recordStatusChange(
                    goodsReceipt: $goodsReceipt,
                    fieldName: $field,
                    oldValue: $oldValue,
                    newValue: $newValue,
                    metadata: [
                        'action' => 'receiving_record_submitted',
                    ],
                    userId: (int) $actorId,
                );
            }

            app(GoodsReceiptAuditService::class)->record(
                goodsReceipt: $goodsReceipt,
                eventType: GoodsReceiptAuditService::EVENT_SUBMIT,
                fieldName: 'draft_submitted',
                oldValue: 'DRAFT',
                newValue: $newRrStatus,
                metadata: [
                    'submitted_item_count' => count($positiveItems),
                    'has_outstanding_rows' => $hasOutstandingRows,
                    'all_source_items_complete' => $allSourceComplete,
                ],
                userId: (int) $actorId,
            );

            // 13. Post submitted items to Inventory Stock.
            app(InventoryStockPostingService::class)->postGoodsReceipt(
                goodsReceipt: $goodsReceipt,
                positiveItems: $positiveItems,
                actorId: (int) $actorId,
            );

            return $goodsReceipt->fresh([
                'assignmentDirectMarket',
                'purchaseOrder',
                'items',
                'supplier',
            ]);
        }, 3);
    }



    /*
    |--------------------------------------------------------------------------
    | RECEIVE
    |--------------------------------------------------------------------------
    */

    /**
     * Receive Goods Receipt items generated from Assignment Direct Market.
     *
     * Phase 1 responsibility:
     *
     * - Validate RR source.
     * - Validate ADM source.
     * - Validate receiving quantities.
     * - Prevent over-receiving.
     * - Save received_qty.
     * - Update RR status.
     *
     * This service intentionally DOES NOT:
     *
     * - update Purchase Order
     * - update Purchase Order Items
     * - update AMR
     * - update Direct Market
     * - execute approval workflow
     * - post inventory
     * - update warehouse stock
     * - update accepted_qty
     * - update rejected_qty
     * - modify GoodsReceiptService
     *
     * Expected $quantities format:
     *
     * [
     *     goods_receipt_item_id => receive_qty,
     * ]
     *
     * Example:
     *
     * [
     *     2 => 5,
     *     3 => 3,
     * ]
     */
    public function receive(
        int $goodsReceiptId,
        array $quantities,
        ?int $userId = null,
    ): GoodsReceipt {
        return $this->db->transaction(
            function () use (
                $goodsReceiptId,
                $quantities,
                $userId,
            ): GoodsReceipt {

                // DEBUG: Periksa input yang diterima Service
                logger()->info('RR RECEIVE DEBUG - SERVICE INPUT', [
                    'goods_receipt_id' => $goodsReceiptId,
                    'quantities' => $quantities,
                ]);

                /*
                |--------------------------------------------------------------------------
                | ACTOR
                |--------------------------------------------------------------------------
                */

                $actorId =
                    $userId
                    ?? auth()->id();

                /*
                |--------------------------------------------------------------------------
                | VALIDATE INPUT
                |--------------------------------------------------------------------------
                */

                if (empty($quantities)) {
                    throw new RuntimeException(
                        'At least one receiving quantity is required.'
                    );
                }

                /*
                |--------------------------------------------------------------------------
                | LOCK GOODS RECEIPT
                |--------------------------------------------------------------------------
                |
                | Lock the RR first so two users cannot receive the same
                | document concurrently.
                |
                */

                $goodsReceipt = $this->model
                    ->newQuery()
                    ->lockForUpdate()
                    ->findOrFail($goodsReceiptId);

                /*
                |--------------------------------------------------------------------------
                | RR SOURCE
                |--------------------------------------------------------------------------
                |
                | Phase 1 supports:
                |
                | - Purchase Order → Goods Receipt
                | - Assignment Direct Market → Goods Receipt
                |
                */

                $isPurchaseOrder =
                    ! empty($goodsReceipt->purchase_order_id);

                $isAssignmentDirectMarket =
                    ! empty($goodsReceipt->assignment_direct_market_id);

                if (
                    $isPurchaseOrder === $isAssignmentDirectMarket
                ) {
                    throw new RuntimeException(
                        "Goods Receipt [{$goodsReceipt->grn_no}] "
                        . 'must be linked to exactly one receiving source.'
                    );
                }

                /*
                |--------------------------------------------------------------------------
                | LOCK + VALIDATE SOURCE
                |--------------------------------------------------------------------------
                */

                $purchaseOrder = null;
                $assignment = null;

                if ($isPurchaseOrder) {

                    $purchaseOrder = PurchaseOrder::query()
                        ->lockForUpdate()
                        ->findOrFail(
                            $goodsReceipt->purchase_order_id
                        );

                    if (
                        $purchaseOrder->approval_status
                        !== PurchaseOrder::APPROVAL_APPROVED
                    ) {
                        throw new RuntimeException(
                            "Purchase Order [{$purchaseOrder->document_no}] "
                            . "must be Approved before receiving. "
                            . "Current approval status: "
                            . "[{$purchaseOrder->approval_status}]."
                        );
                    }

                } else {

                    $assignment = AssignmentDirectMarket::query()
                        ->lockForUpdate()
                        ->findOrFail(
                            $goodsReceipt->assignment_direct_market_id
                        );

                    if (
                        $assignment->status
                        !== AssignmentDirectMarket::STATUS_APPROVED
                    ) {
                        throw new RuntimeException(
                            "Assignment Direct Market [{$assignment->document_no}] "
                            . "must be Approved before receiving. "
                            . "Current status: [{$assignment->status}]."
                        );
                    }
                }

                /*
                |--------------------------------------------------------------------------
                | LOAD + LOCK RR ITEMS
                |--------------------------------------------------------------------------
                */

                $goodsReceiptItems = GoodsReceiptItem::query()
                    ->where(
                        'goods_receipt_id',
                        $goodsReceipt->getKey()
                    )
                    ->lockForUpdate()
                    ->get();

                if ($goodsReceiptItems->isEmpty()) {
                    throw new RuntimeException(
                        "Goods Receipt [{$goodsReceipt->grn_no}] "
                        . 'has no receiving items.'
                    );
                }

                /*
                |--------------------------------------------------------------------------
                | NORMALIZE INPUT
                |--------------------------------------------------------------------------
                */

                $normalizedQuantities = [];

                foreach ($quantities as $goodsReceiptItemId => $receiveQty) {

                    $goodsReceiptItemId =
                        (int) $goodsReceiptItemId;

                    if ($goodsReceiptItemId <= 0) {
                        throw new RuntimeException(
                            'Invalid Goods Receipt Item ID.'
                        );
                    }

                    if (
                        ! is_numeric($receiveQty)
                    ) {
                        throw new RuntimeException(
                            "Invalid receiving quantity for Goods Receipt Item "
                            . "[{$goodsReceiptItemId}]."
                        );
                    }

                    $receiveQty =
                        round(
                            (float) $receiveQty,
                            4
                        );

                    if ($receiveQty <= 0) {
                        throw new RuntimeException(
                            "Receiving quantity for Goods Receipt Item "
                            . "[{$goodsReceiptItemId}] "
                            . 'must be greater than zero.'
                        );
                    }

                    $normalizedQuantities[
                        $goodsReceiptItemId
                    ] = $receiveQty;
                }

                /*
                |--------------------------------------------------------------------------
                | RECEIVE EACH ITEM
                |--------------------------------------------------------------------------
                */

                foreach (
                    $normalizedQuantities
                    as $goodsReceiptItemId => $receiveQty
                ) {

                    /*
                    |--------------------------------------------------------------------------
                    | FIND RR ITEM
                    |--------------------------------------------------------------------------
                    */

                    /** @var GoodsReceiptItem|null $goodsReceiptItem */
                    $goodsReceiptItem =
                        $goodsReceiptItems->first(
                            function (
                                GoodsReceiptItem $item
                            ) use (
                                $goodsReceiptItemId
                            ): bool {
                                return
                                    (int) $item->getKey()
                                    === $goodsReceiptItemId;
                            }
                        );

                    if (! $goodsReceiptItem) {
                        throw new RuntimeException(
                            "Goods Receipt Item [{$goodsReceiptItemId}] "
                            . "does not belong to Goods Receipt "
                            . "[{$goodsReceipt->grn_no}]."
                        );
                    }

                /*
                |--------------------------------------------------------------------------
                | SOURCE ITEM
                |--------------------------------------------------------------------------
                */

                $targetQty = 0.0;

                if ($isPurchaseOrder) {

                    if (
                        empty($goodsReceiptItem->purchase_order_item_id)
                    ) {
                        throw new RuntimeException(
                            "Goods Receipt Item [{$goodsReceiptItemId}] "
                            . 'is missing its Purchase Order Item.'
                        );
                    }

                    /** @var PurchaseOrderItem $purchaseOrderItem */
                    $purchaseOrderItem = PurchaseOrderItem::query()
                        ->lockForUpdate()
                        ->findOrFail(
                            $goodsReceiptItem->purchase_order_item_id
                        );

                    /*
                    |--------------------------------------------------------------------------
                    | PO OWNERSHIP GUARD
                    |--------------------------------------------------------------------------
                    */

                    if (
                        (int) $purchaseOrderItem->purchase_order_id
                        !==
                        (int) $purchaseOrder->getKey()
                    ) {
                        throw new RuntimeException(
                            "Purchase Order Item "
                            . "[{$purchaseOrderItem->getKey()}] "
                            . 'does not belong to the source Purchase Order.'
                        );
                    }

                    /*
                    |--------------------------------------------------------------------------
                    | ITEM SNAPSHOT GUARD
                    |--------------------------------------------------------------------------
                    */

                    if (
                        (int) $goodsReceiptItem->item_id
                        !==
                        (int) $purchaseOrderItem->item_id
                    ) {
                        throw new RuntimeException(
                            "Goods Receipt Item [{$goodsReceiptItemId}] "
                            . 'item does not match its Purchase Order Item.'
                        );
                    }

                    /*
                    |--------------------------------------------------------------------------
                    | UOM GUARD
                    |--------------------------------------------------------------------------
                    */

                    if (
                        (int) $goodsReceiptItem->uom_id
                        !==
                        (int) $purchaseOrderItem->uom_id
                    ) {
                        throw new RuntimeException(
                            "Goods Receipt Item [{$goodsReceiptItemId}] "
                            . 'UOM does not match its Purchase Order Item.'
                        );
                    }

                    $targetQty =
                        round(
                            (float) $purchaseOrderItem->ordered_qty,
                            4
                        );

                    if ($targetQty <= 0) {
                        throw new RuntimeException(
                            "Purchase Order Item "
                            . "[{$purchaseOrderItem->getKey()}] "
                            . 'has an invalid ordered quantity.'
                        );
                    }

                } else {

                    /*
                    |--------------------------------------------------------------------------
                    | ADM ITEM LINK GUARD
                    |--------------------------------------------------------------------------
                    */

                    if (
                        empty(
                            $goodsReceiptItem
                                ->assignment_direct_market_item_id
                        )
                    ) {
                        throw new RuntimeException(
                            "Goods Receipt Item [{$goodsReceiptItemId}] "
                            . 'is not linked to an Assignment Direct Market Item.'
                        );
                    }

                    /*
                    |--------------------------------------------------------------------------
                    | LOCK ADM ITEM
                    |--------------------------------------------------------------------------
                    */

                    $assignmentItem =
                        AssignmentDirectMarketItem::query()
                            ->lockForUpdate()
                            ->findOrFail(
                                $goodsReceiptItem
                                    ->assignment_direct_market_item_id
                            );

                    /*
                    |--------------------------------------------------------------------------
                    | ADM OWNERSHIP GUARD
                    |--------------------------------------------------------------------------
                    */

                    if (
                        (int) $assignmentItem
                            ->assignment_direct_market_id
                        !==
                        (int) $assignment->getKey()
                    ) {
                        throw new RuntimeException(
                            "Assignment Direct Market Item "
                            . "[{$assignmentItem->getKey()}] "
                            . 'does not belong to the source Assignment Direct Market.'
                        );
                    }

                    /*
                    |--------------------------------------------------------------------------
                    | ITEM SNAPSHOT GUARD
                    |--------------------------------------------------------------------------
                    */

                    if (
                        (int) $goodsReceiptItem->item_id
                        !==
                        (int) $assignmentItem->item_id
                    ) {
                        throw new RuntimeException(
                            "Goods Receipt Item [{$goodsReceiptItemId}] "
                            . 'item does not match its Assignment Direct Market Item.'
                        );
                    }

                    /*
                    |--------------------------------------------------------------------------
                    | UOM GUARD
                    |--------------------------------------------------------------------------
                    */

                    if (
                        (int) $goodsReceiptItem->uom_id
                        !==
                        (int) $assignmentItem->uom_id
                    ) {
                        throw new RuntimeException(
                            "Goods Receipt Item [{$goodsReceiptItemId}] "
                            . 'UOM does not match its Assignment Direct Market Item.'
                        );
                    }

                    $targetQty =
                        round(
                            (float) $assignmentItem->assigned_qty,
                            4
                        );

                    if ($targetQty <= 0) {
                        throw new RuntimeException(
                            "Assignment Direct Market Item "
                            . "[{$assignmentItem->getKey()}] "
                            . 'has an invalid assigned quantity.'
                        );
                    }
                }

                /*
                |--------------------------------------------------------------------------
                | CURRENT RECEIVED QUANTITY
                |--------------------------------------------------------------------------
                */

                $currentReceivedQty = round(
                    (float) $goodsReceiptItem->received_qty,
                    4
                );

                /*
                |--------------------------------------------------------------------------
                | ALLOCATION SNAPSHOT
                |--------------------------------------------------------------------------
                */

                $allocatedQty = round(
                    (float) $goodsReceiptItem->allocated_qty,
                    4
                );

                if ($allocatedQty <= 0) {
                    throw new RuntimeException(
                        "Goods Receipt Item [{$goodsReceiptItemId}] "
                        . 'has an invalid allocated quantity.'
                    );
                }

                /*
                |--------------------------------------------------------------------------
                | CUMULATIVE RECEIVED QUANTITY
                |--------------------------------------------------------------------------
                |
                | Calculate all received quantities from every RR
                | linked to the same source item.
                |
                */

                if ($isPurchaseOrder) {

                    $totalReceivedQty = (float) GoodsReceiptItem::query()
                        ->where(
                            'purchase_order_item_id',
                            $goodsReceiptItem->purchase_order_item_id
                        )
                        ->sum('received_qty');

                } else {

                    $totalReceivedQty = (float) GoodsReceiptItem::query()
                        ->where(
                            'assignment_direct_market_item_id',
                            $goodsReceiptItem->assignment_direct_market_item_id
                        )
                        ->sum('received_qty');
                }

                $totalReceivedQty = round($totalReceivedQty, 4);

                /*
                |--------------------------------------------------------------------------
                | REMAINING QUANTITY
                |--------------------------------------------------------------------------
                |
                | Remaining is restricted by BOTH:
                |
                | 1. This RR allocation.
                | 2. Remaining source quantity across all RRs.
                |
                */

                $remainingAllocationQty = round(
                    $allocatedQty - $currentReceivedQty,
                    4
                );

                $remainingSourceQty = round(
                    $targetQty - $totalReceivedQty,
                    4
                );

                $remainingQty = max(
                    0,
                    min(
                        $remainingAllocationQty,
                        $remainingSourceQty
                    )
                );

                /*
                |--------------------------------------------------------------------------
                | ALREADY FULLY RECEIVED
                |--------------------------------------------------------------------------
                */

                if ($remainingQty <= 0) {
                    throw new RuntimeException(
                        "Goods Receipt Item [{$goodsReceiptItemId}] "
                        . 'has already reached its remaining receiving limit.'
                    );
                }

                /*
                |--------------------------------------------------------------------------
                | OVER-RECEIVING GUARD
                |--------------------------------------------------------------------------
                */

                if ($receiveQty > ($remainingQty + 0.00005)) {
                    throw new RuntimeException(
                        "Receiving quantity for Goods Receipt Item "
                        . "[{$goodsReceiptItemId}] "
                        . "cannot exceed remaining quantity "
                        . "[{$remainingQty}]."
                    );
                }

                /*
                |--------------------------------------------------------------------------
                | NEW RECEIVED QUANTITY
                |--------------------------------------------------------------------------
                */

                $newReceivedQty = round(
                    $currentReceivedQty + $receiveQty,
                    4
                );

                /*
                |--------------------------------------------------------------------------
                | FINAL SAFETY GUARD
                |--------------------------------------------------------------------------
                */

                if ($newReceivedQty > ($allocatedQty + 0.00005)) {
                    throw new RuntimeException(
                        "Goods Receipt Item [{$goodsReceiptItemId}] "
                        . 'would exceed its allocated quantity.'
                    );
                }

                if (
                    ($totalReceivedQty + $receiveQty)
                    > ($targetQty + 0.00005)
                ) {
                    throw new RuntimeException(
                        "Source item receiving quantity would exceed "
                        . "its total ordered or assigned quantity."
                    );
                }

                /*
                |--------------------------------------------------------------------------
                | SAVE RECEIVED QUANTITY
                |--------------------------------------------------------------------------
                */

                    $goodsReceiptItem->update([
                        'received_qty' => min(
                            $newReceivedQty,
                            $allocatedQty
                        ),

                        'updated_by' => $actorId,
                    ]);
                }

                /*
                |--------------------------------------------------------------------------
                | REFRESH RR ITEMS
                |--------------------------------------------------------------------------
                */

                $goodsReceiptItems =
                    GoodsReceiptItem::query()
                        ->where(
                            'goods_receipt_id',
                            $goodsReceipt->getKey()
                        )
                        ->get();

                /*
                |--------------------------------------------------------------------------
                | DETERMINE RR STATUS
                |--------------------------------------------------------------------------
                |
                | Phase 1:
                |
                | 0 received       → Draft
                | Partially received → Partially Received
                | Fully received   → Received
                |
                | Completed is intentionally NOT used here because
                | acceptance/rejection is outside phase 1.
                |
                */

                $hasReceived =
                    false;

                $allReceived =
                    true;

                foreach (
                    $goodsReceiptItems
                    as $goodsReceiptItem
                ) {

                    $targetQty = 0.0;

                    if ($isPurchaseOrder) {

                        if (
                            empty($goodsReceiptItem->purchase_order_item_id)
                        ) {
                            throw new RuntimeException(
                                "Goods Receipt Item "
                                . "[{$goodsReceiptItem->getKey()}] "
                                . 'is missing its Purchase Order Item.'
                            );
                        }

                        $purchaseOrderItem =
                            PurchaseOrderItem::query()
                                ->findOrFail(
                                    $goodsReceiptItem
                                        ->purchase_order_item_id
                                );

                        if (
                            (int) $purchaseOrderItem->purchase_order_id
                            !==
                            (int) $purchaseOrder->getKey()
                        ) {
                            throw new RuntimeException(
                                "Purchase Order Item "
                                . "[{$purchaseOrderItem->getKey()}] "
                                . 'does not belong to the source Purchase Order.'
                            );
                        }

                        $targetQty =
                            round(
                                (float) $purchaseOrderItem->ordered_qty,
                                4
                            );

                    } else {

                        if (
                            empty(
                                $goodsReceiptItem
                                    ->assignment_direct_market_item_id
                            )
                        ) {
                            throw new RuntimeException(
                                "Goods Receipt Item "
                                . "[{$goodsReceiptItem->getKey()}] "
                                . 'is missing its Assignment Direct Market Item.'
                            );
                        }

                        $assignmentItem =
                            AssignmentDirectMarketItem::query()
                                ->findOrFail(
                                    $goodsReceiptItem
                                        ->assignment_direct_market_item_id
                                );

                        if (
                            (int) $assignmentItem->assignment_direct_market_id
                            !==
                            (int) $assignment->getKey()
                        ) {
                            throw new RuntimeException(
                                "Assignment Direct Market Item "
                                . "[{$assignmentItem->getKey()}] "
                                . 'does not belong to the source Assignment Direct Market.'
                            );
                        }

                        $targetQty =
                            round(
                                (float) $assignmentItem->assigned_qty,
                                4
                            );
                    }

                    $receivedQty =
                        round(
                            (float) $goodsReceiptItem->received_qty,
                            4
                        );

                    if ($receivedQty > 0) {
                        $hasReceived = true;
                    }

                    $allocatedQty = round(
                        (float) $goodsReceiptItem->allocated_qty,
                        4
                    );

                    if ($allocatedQty <= 0) {
                        throw new RuntimeException(
                            "Goods Receipt Item [{$goodsReceiptItem->getKey()}] "
                            . 'has an invalid allocated quantity.'
                        );
                    }

                    if (
                        $receivedQty
                        < ($allocatedQty - 0.00005)
                    ) {
                        $allReceived = false;
                    }
                }

                /*
                |--------------------------------------------------------------------------
                | STATUS
                |--------------------------------------------------------------------------
                */

                if ($allReceived) {

                    $newStatus =
                        GoodsReceipt::STATUS_RECEIVED;

                } elseif ($hasReceived) {

                    $newStatus =
                        GoodsReceipt::STATUS_PARTIALLY_RECEIVED;

                } else {

                    $newStatus =
                        GoodsReceipt::STATUS_DRAFT;
                }

                /*
                |--------------------------------------------------------------------------
                | SAVE RR STATUS
                |--------------------------------------------------------------------------
                */

                $goodsReceipt->update([
                    'status' =>
                        $newStatus,

                    'updated_by' =>
                        $actorId,
                ]);

                /*
                |--------------------------------------------------------------------------
                | RETURN FRESH RR
                |--------------------------------------------------------------------------
                */

                return $goodsReceipt->fresh([
                    'assignmentDirectMarket',
                    'items',
                    'supplier',
                ]);
            }
        );
    }
}