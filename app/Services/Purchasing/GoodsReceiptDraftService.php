<?php

declare(strict_types=1);

namespace App\Services\Purchasing;

use App\Models\AssignmentDirectMarketItem;
use App\Models\GoodsReceipt;
use App\Models\GoodsReceiptItem;
use App\Models\PurchaseOrderItem;
use Illuminate\Support\Facades\DB;
use RuntimeException;

class GoodsReceiptDraftService
{
    public function __construct(
        protected GoodsReceiptAuditService $auditService,
    ) {
    }

    /**
     * Save item selection and draft receive quantities.
     *
     * Expected payload:
     * [
     *     [
     *         'source_item_id' => 64,
     *         'draft_receive_qty' => 10,
     *     ],
     * ]
     *
     * Save never reserves source quantity and never posts actual receiving.
     */
    public function saveDraft(
        int $goodsReceiptId,
        array $items,
        ?int $userId = null,
    ): GoodsReceipt {
        return DB::transaction(function () use (
            $goodsReceiptId,
            $items,
            $userId
        ): GoodsReceipt {
            $actorId = $userId ?? auth()->id();

            if (! $actorId) {
                throw new RuntimeException(
                    'An authenticated user is required to save this Draft RR.'
                );
            }

            /*
             * Lock the RR header first.
             */
            $goodsReceipt = GoodsReceipt::query()
                ->lockForUpdate()
                ->findOrFail($goodsReceiptId);

            if (
                strtoupper((string) $goodsReceipt->rr_status)
                !== 'DRAFT'
            ) {
                throw new RuntimeException(
                    'Only a DRAFT Receiving Record can be saved.'
                );
            }

            $isPurchaseOrder = (int) $goodsReceipt->purchase_order_id > 0;
            $isAssignment = (int) $goodsReceipt->assignment_direct_market_id > 0;

            if ($isPurchaseOrder === $isAssignment) {
                throw new RuntimeException(
                    'The RR must have exactly one valid source document.'
                );
            }

            /*
             * Normalize and validate payload.
             */
            $normalized = [];
            $sourceIds = [];

            foreach ($items as $row) {
                if (! is_array($row)) {
                    throw new RuntimeException(
                        'Invalid Receiving Record item payload.'
                    );
                }

                $sourceId = filter_var(
                    $row['source_item_id'] ?? null,
                    FILTER_VALIDATE_INT
                );

                if (! $sourceId || $sourceId <= 0) {
                    throw new RuntimeException(
                        'A valid source item is required.'
                    );
                }

                if (isset($sourceIds[$sourceId])) {
                    throw new RuntimeException(
                        "Source item [{$sourceId}] was submitted more than once."
                    );
                }

                $draftReceiveQty = $this->normalizeQuantity(
                    $row['draft_receive_qty'] ?? 0,
                    'Receive Qty',
                );

                $normalized[$sourceId] = [
                    'source_item_id' => $sourceId,
                    'draft_receive_qty' => $draftReceiveQty,
                ];

                $sourceIds[$sourceId] = true;
            }

            /*
             * Existing saved items cannot be removed or replaced.
             * Every existing source item must remain in the payload.
             */
            $foreignKey = $isPurchaseOrder
                ? 'purchase_order_item_id'
                : 'assignment_direct_market_item_id';

            $existingItems = GoodsReceiptItem::query()
                ->where(
                    'goods_receipt_id',
                    $goodsReceipt->getKey()
                )
                ->orderBy('id')
                ->lockForUpdate()
                ->get();

            foreach ($existingItems as $existingItem) {
                $existingSourceId = (int) $existingItem->{$foreignKey};

                if (
                    $existingSourceId <= 0
                    || ! isset($normalized[$existingSourceId])
                ) {
                    throw new RuntimeException(
                        'A previously saved item cannot be removed or replaced from a Draft RR.'
                    );
                }

                /*
                 * Actual receiving must only be recorded during Submit.
                 * Protect legacy rows that may already contain actual qty.
                 */
                if ((float) $existingItem->received_qty > 0) {
                    throw new RuntimeException(
                        "RR item [{$existingItem->getKey()}] already has actual received quantity and cannot be changed through Draft Save."
                    );
                }
            }

            /*
             * Lock all source rows in ascending ID order.
             * This serializes allocation attempts across different RRs.
             */
            $lockedSources = [];

            $sourceIds = array_keys($sourceIds);
            sort($sourceIds, SORT_NUMERIC);

            foreach ($sourceIds as $sourceId) {
                if ($isPurchaseOrder) {
                    $source = PurchaseOrderItem::query()
                        ->where(
                            'purchase_order_id',
                            $goodsReceipt->purchase_order_id
                        )
                        ->lockForUpdate()
                        ->find($sourceId);
                } else {
                    $source = AssignmentDirectMarketItem::query()
                        ->where(
                            'assignment_direct_market_id',
                            $goodsReceipt->assignment_direct_market_id
                        )
                        ->where(
                            'supplier_id',
                            $goodsReceipt->supplier_id
                        )
                        ->lockForUpdate()
                        ->find($sourceId);
                }

                if (! $source) {
                    throw new RuntimeException(
                        "Source item [{$sourceId}] does not belong to this RR source document or supplier."
                    );
                }

                $lockedSources[$sourceId] = $source;
            }


            /*
             * Validate Draft Receive Qty against current source Remaining.
             *
             * Remaining = Source Qty - Actual Received
             *
             * Draft quantities do not reserve source quantity.
             * Only active RECEIVED and CLOSE RRs reduce Remaining.
             */
            foreach ($normalized as $sourceId => $row) {
                $source = $lockedSources[$sourceId];

                $sourceQty = $isPurchaseOrder
                    ? (float) $source->ordered_qty
                    : (float) $source->assigned_qty;

                $actualReceived = $this->sumSourceQuantity(
                    $foreignKey,
                    (int) $sourceId,
                    ['RECEIVED', 'CLOSE'],
                    'received_qty',
                );

                $remainingQty = round(
                    $sourceQty - $actualReceived,
                    4
                );

                $draftReceiveQty = (float) $row['draft_receive_qty'];

                if ($draftReceiveQty > $remainingQty + 0.00005) {
                    throw new RuntimeException(
                        sprintf(
                            'Receive Qty for source item [%d] cannot exceed Remaining Qty. Maximum allowed: %s.',
                            $sourceId,
                            number_format(max(0, $remainingQty), 4, '.', '')
                        )
                    );
                }
            }


            /*
             * Persist items only after all validation has passed.
             */
            foreach ($normalized as $sourceId => $row) {
                $source = $lockedSources[$sourceId];

                $existingItem = $existingItems->first(
                    fn (GoodsReceiptItem $item): bool =>
                        (int) $item->{$foreignKey} === (int) $sourceId
                );

                if ($isPurchaseOrder) {
                    $warehouseId = $source->warehouse_id;
                } else {
                    $warehouseId = $goodsReceipt
                        ->assignmentDirectMarket()
                        ->value('warehouse_id');
                }

                if ($existingItem) {
                    $oldDraftReceive = (float) $existingItem->draft_receive_qty;

                    $existingItem->draft_receive_qty = $row['draft_receive_qty'];
                    $existingItem->updated_by = $actorId;
                    $existingItem->save();

                    if (
                        abs($oldDraftReceive - $row['draft_receive_qty']) > 0.00005
                    ) {
                        $this->auditService->record(
                            goodsReceipt: $goodsReceipt,
                            eventType: GoodsReceiptAuditService::EVENT_SAVE,
                            goodsReceiptItem: $existingItem,
                            fieldName: 'draft_receive_qty',
                            oldValue: $oldDraftReceive,
                            newValue: $row['draft_receive_qty'],
                            metadata: [
                                'action' => 'draft_receive_qty_updated',
                            ],
                            userId: $actorId,
                        );
                    }

                    continue;
                }

                $newItem = GoodsReceiptItem::query()->create([
                    'goods_receipt_id' => $goodsReceipt->getKey(),

                    'purchase_order_item_id' => $isPurchaseOrder
                        ? $sourceId
                        : null,

                    'assignment_direct_market_item_id' => $isAssignment
                        ? $sourceId
                        : null,

                    'item_id' => $source->item_id,

                    'received_qty' => 0,
                    'draft_receive_qty' => $row['draft_receive_qty'],

                    'item_status' => 'DRAFT',

                    'qc_passed' => false,
                    'qc_checked_by' => null,
                    'qc_checked_at' => null,

                    'uom_id' => $source->uom_id,
                    'warehouse_id' => $warehouseId,

                    'accepted_qty' => 0,
                    'rejected_qty' => 0,

                    'remarks' => $source->remarks ?? null,

                    'created_by' => $actorId,
                    'updated_by' => $actorId,
                ]);

                $this->auditService->record(
                    goodsReceipt: $goodsReceipt,
                    eventType: GoodsReceiptAuditService::EVENT_SAVE,
                    goodsReceiptItem: $newItem,
                    fieldName: 'item',
                    oldValue: null,
                    newValue: [
                        'source_item_id' => $sourceId,
                        'draft_receive_qty' => $row['draft_receive_qty'],
                    ],
                    metadata: [
                        'action' => 'draft_item_added',
                    ],
                    userId: $actorId,
                );
            }

            /*
             * SAVE does not change workflow status.
             */
            $goodsReceipt->updated_by = $actorId;
            $goodsReceipt->save();

            $this->auditService->record(
                goodsReceipt: $goodsReceipt,
                eventType: GoodsReceiptAuditService::EVENT_SAVE,
                fieldName: 'draft_items_saved',
                oldValue: null,
                newValue: [
                    'item_count' => count($normalized),
                ],
                metadata: [
                    'action' => 'draft_saved',
                ],
                userId: $actorId,
            );

            return $goodsReceipt->fresh([
                'items',
                'purchaseOrder',
                'assignmentDirectMarket',
            ]);
        }, 3);
    }

    /**
     * Sum actual quantities from active submitted/closed RRs.
     */
    protected function sumSourceQuantity(
        string $foreignKey,
        int $sourceId,
        array $rrStatuses,
        string $quantityColumn,
    ): float {
        return (float) DB::table('goods_receipt_items')
            ->join(
                'goods_receipts',
                'goods_receipts.id',
                '=',
                'goods_receipt_items.goods_receipt_id'
            )
            ->where(
                "goods_receipt_items.{$foreignKey}",
                $sourceId
            )
            ->whereNull('goods_receipt_items.deleted_at')
            ->whereNull('goods_receipts.deleted_at')
            ->whereIn('goods_receipts.rr_status', $rrStatuses)
            ->sum("goods_receipt_items.{$quantityColumn}");
    }

    /**
     * Normalize quantities to four decimal places.
     */
    protected function normalizeQuantity(
        mixed $value,
        string $label,
    ): float {
        if (
            $value === null
            || $value === ''
            || ! is_numeric($value)
        ) {
            throw new RuntimeException(
                "{$label} must be a valid numeric value."
            );
        }

        $stringValue = trim((string) $value);

        if (! preg_match('/^\d+(?:\.\d{1,4})?$/', $stringValue)) {
            throw new RuntimeException(
                "{$label} may contain a maximum of four decimal places and cannot be negative."
            );
        }

        return round((float) $stringValue, 4);
    }
}