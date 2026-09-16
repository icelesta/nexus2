<?php

namespace App\Services\Purchasing;

use App\Models\AssignmentDirectMarket;
use App\Models\AssignmentDirectMarketItem;
use App\Models\GoodsReceipt;
use Illuminate\Database\DatabaseManager;
use RuntimeException;

class GoodsReceiptCompletionService
{
    public function __construct(
        protected DatabaseManager $db,
    ) {}

    /**
     * Complete ADM after its Goods Receipt has been fully received.
     *
     * This service:
     * - only handles ADM-based Goods Receipt
     * - requires Goods Receipt status = Received
     * - verifies all ADM assigned quantities are fully received
     * - completes ADM items
     * - completes ADM
     *
     * It does NOT:
     * - modify Direct Market
     * - modify Purchase Order
     * - modify MR / AMR
     * - modify inventory
     * - modify approval records
     * - modify accepted/rejected quantities
     */
    public function complete(
        int $goodsReceiptId,
        ?int $userId = null,
    ): GoodsReceipt {

        return $this->db->transaction(function () use (
            $goodsReceiptId,
            $userId,
        ) {

            /*
             |--------------------------------------------------------------------------
             | Load Goods Receipt
             |--------------------------------------------------------------------------
             */

            $goodsReceipt = GoodsReceipt::query()
                ->lockForUpdate()
                ->findOrFail($goodsReceiptId);

            /*
             |--------------------------------------------------------------------------
             | Validate Source
             |--------------------------------------------------------------------------
             */

            if ($goodsReceipt->purchase_order_id !== null) {
                throw new RuntimeException(
                    "Goods Receipt [{$goodsReceipt->id}] is PO-based. " .
                    "This completion service only supports ADM-based Goods Receipt."
                );
            }

            if ($goodsReceipt->assignment_direct_market_id === null) {
                throw new RuntimeException(
                    "Goods Receipt [{$goodsReceipt->id}] has no Assignment Direct Market."
                );
            }

            /*
             |--------------------------------------------------------------------------
             | Validate Goods Receipt Status
             |--------------------------------------------------------------------------
             */

            if ($goodsReceipt->status === GoodsReceipt::STATUS_CANCELLED) {
                throw new RuntimeException(
                    "Goods Receipt [{$goodsReceipt->id}] is Cancelled."
                );
            }

            if ($goodsReceipt->status === GoodsReceipt::STATUS_CLOSED) {
                throw new RuntimeException(
                    "Goods Receipt [{$goodsReceipt->id}] is Closed."
                );
            }

            if ($goodsReceipt->status === GoodsReceipt::STATUS_COMPLETED) {
                return $goodsReceipt;
            }

            if ($goodsReceipt->status !== GoodsReceipt::STATUS_RECEIVED) {
                throw new RuntimeException(
                    "Goods Receipt [{$goodsReceipt->id}] must be Received before completion."
                );
            }

            /*
             |--------------------------------------------------------------------------
             | Load ADM
             |--------------------------------------------------------------------------
             */

            $assignment = AssignmentDirectMarket::query()
                ->lockForUpdate()
                ->findOrFail(
                    $goodsReceipt->assignment_direct_market_id
                );

            /*
             |--------------------------------------------------------------------------
             | Validate ADM
             |--------------------------------------------------------------------------
             */

            if ($assignment->status !== AssignmentDirectMarket::STATUS_APPROVED) {
                if ($assignment->status === AssignmentDirectMarket::STATUS_COMPLETED) {
                    return $goodsReceipt;
                }

                throw new RuntimeException(
                    "Assignment Direct Market [{$assignment->document_no}] " .
                    "is not in Approved status."
                );
            }

            /*
             |--------------------------------------------------------------------------
             | Load ADM Items
             |--------------------------------------------------------------------------
             */

            $assignmentItems = AssignmentDirectMarketItem::query()
                ->where(
                    'assignment_direct_market_id',
                    $assignment->id
                )
                ->lockForUpdate()
                ->get();

            if ($assignmentItems->isEmpty()) {
                throw new RuntimeException(
                    "Assignment Direct Market [{$assignment->document_no}] has no items."
                );
            }

            /*
             |--------------------------------------------------------------------------
             | Load RR Items
             |--------------------------------------------------------------------------
             */

            $goodsReceiptItems = $goodsReceipt->items()
                ->lockForUpdate()
                ->get();

            /*
             |--------------------------------------------------------------------------
             | Validate Full Receiving
             |--------------------------------------------------------------------------
             */

            foreach ($assignmentItems as $assignmentItem) {

                $receivedQty = (float) $goodsReceiptItems
                    ->where(
                        'assignment_direct_market_item_id',
                        $assignmentItem->id
                    )
                    ->sum('received_qty');

                $assignedQty = (float) $assignmentItem->assigned_qty;

                if ($receivedQty < $assignedQty) {
                    throw new RuntimeException(
                        "Assignment Direct Market Item [{$assignmentItem->id}] " .
                        "has not been fully received. " .
                        "Assigned: {$assignedQty}, Received: {$receivedQty}."
                    );
                }
            }

            /*
             |--------------------------------------------------------------------------
             | Complete ADM Items
             |--------------------------------------------------------------------------
             */

            foreach ($assignmentItems as $assignmentItem) {

                if (
                    $assignmentItem->status
                    !== AssignmentDirectMarketItem::STATUS_COMPLETED
                ) {
                    $assignmentItem->update([
                        'status' => AssignmentDirectMarketItem::STATUS_COMPLETED,
                        'approval_status' => 'Approved',
                        'updated_by' => $userId,
                    ]);
                }
            }

            /*
             |--------------------------------------------------------------------------
             | Complete ADM
             |--------------------------------------------------------------------------
             */

            $assignment->update([
                'status' => AssignmentDirectMarket::STATUS_COMPLETED,
                'updated_by' => $userId,
            ]);

            return $goodsReceipt->fresh();
        });
    }
}