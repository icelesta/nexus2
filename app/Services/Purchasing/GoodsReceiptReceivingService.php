<?php

declare(strict_types=1);

namespace App\Services\Purchasing;

use App\Models\AssignmentDirectMarket;
use App\Models\AssignmentDirectMarketItem;
use App\Models\GoodsReceipt;
use App\Models\GoodsReceiptItem;
use Illuminate\Database\DatabaseManager;
use RuntimeException;

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
                | Phase 1 is intentionally limited to:
                |
                | Assignment Direct Market → Goods Receipt
                |
                */

                if (
                    empty(
                        $goodsReceipt->assignment_direct_market_id
                    )
                ) {
                    throw new RuntimeException(
                        "Goods Receipt [{$goodsReceipt->grn_no}] "
                        . 'is not linked to an Assignment Direct Market.'
                    );
                }

                /*
                |--------------------------------------------------------------------------
                | PO GUARD
                |--------------------------------------------------------------------------
                |
                | Do not allow this service to accidentally process
                | PO-based Goods Receipt.
                |
                */

                if (
                    ! empty(
                        $goodsReceipt->purchase_order_id
                    )
                ) {
                    throw new RuntimeException(
                        "Goods Receipt [{$goodsReceipt->grn_no}] "
                        . 'is linked to a Purchase Order. '
                        . 'PO receiving is outside this service.'
                    );
                }

                /*
                |--------------------------------------------------------------------------
                | RR STATUS GUARD
                |--------------------------------------------------------------------------
                */

                if (
                    in_array(
                        $goodsReceipt->status,
                        [
                            GoodsReceipt::STATUS_CANCELLED,
                            GoodsReceipt::STATUS_CLOSED,
                            GoodsReceipt::STATUS_COMPLETED,
                        ],
                        true,
                    )
                ) {
                    throw new RuntimeException(
                        "Goods Receipt [{$goodsReceipt->grn_no}] "
                        . "cannot receive while in status "
                        . "[{$goodsReceipt->status}]."
                    );
                }

                /*
                |--------------------------------------------------------------------------
                | LOCK ASSIGNMENT DIRECT MARKET
                |--------------------------------------------------------------------------
                */

                $assignment = AssignmentDirectMarket::query()
                    ->lockForUpdate()
                    ->findOrFail(
                        $goodsReceipt->assignment_direct_market_id
                    );

                /*
                |--------------------------------------------------------------------------
                | ADM STATUS GUARD
                |--------------------------------------------------------------------------
                */

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

                    /*
                    |--------------------------------------------------------------------------
                    | ASSIGNED QUANTITY
                    |--------------------------------------------------------------------------
                    */

                    $assignedQty =
                        round(
                            (float) $assignmentItem->assigned_qty,
                            4
                        );

                    if ($assignedQty <= 0) {
                        throw new RuntimeException(
                            "Assignment Direct Market Item "
                            . "[{$assignmentItem->getKey()}] "
                            . 'has an invalid assigned quantity.'
                        );
                    }

                    /*
                    |--------------------------------------------------------------------------
                    | CURRENT RECEIVED QUANTITY
                    |--------------------------------------------------------------------------
                    */

                    $currentReceivedQty =
                        round(
                            (float) $goodsReceiptItem->received_qty,
                            4
                        );

                    /*
                    |--------------------------------------------------------------------------
                    | REMAINING QUANTITY
                    |--------------------------------------------------------------------------
                    */

                    $remainingQty =
                        round(
                            $assignedQty
                            - $currentReceivedQty,
                            4
                        );

                    /*
                    |--------------------------------------------------------------------------
                    | ALREADY FULLY RECEIVED
                    |--------------------------------------------------------------------------
                    */

                    if ($remainingQty <= 0) {
                        throw new RuntimeException(
                            "Goods Receipt Item [{$goodsReceiptItemId}] "
                            . 'has already been fully received.'
                        );
                    }

                    /*
                    |--------------------------------------------------------------------------
                    | OVER-RECEIVING GUARD
                    |--------------------------------------------------------------------------
                    */

                    if (
                        $receiveQty
                        > ($remainingQty + 0.00005)
                    ) {
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

                    $newReceivedQty =
                        round(
                            $currentReceivedQty
                            + $receiveQty,
                            4
                        );

                    /*
                    |--------------------------------------------------------------------------
                    | FINAL SAFETY GUARD
                    |--------------------------------------------------------------------------
                    */

                    if (
                        $newReceivedQty
                        > ($assignedQty + 0.00005)
                    ) {
                        throw new RuntimeException(
                            "Goods Receipt Item [{$goodsReceiptItemId}] "
                            . 'would exceed its assigned quantity.'
                        );
                    }

                    /*
                    |--------------------------------------------------------------------------
                    | SAVE RECEIVED QUANTITY
                    |--------------------------------------------------------------------------
                    */

                    $goodsReceiptItem->update([
                        'received_qty' =>
                            min(
                                $newReceivedQty,
                                $assignedQty
                            ),

                        'updated_by' =>
                            $actorId,
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

                    $assignedQty =
                        round(
                            (float) $assignmentItem->assigned_qty,
                            4
                        );

                    $receivedQty =
                        round(
                            (float) $goodsReceiptItem->received_qty,
                            4
                        );

                    if ($receivedQty > 0) {
                        $hasReceived = true;
                    }

                    if (
                        $receivedQty
                        < ($assignedQty - 0.00005)
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