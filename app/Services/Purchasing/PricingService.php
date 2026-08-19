<?php

declare(strict_types=1);

namespace App\Services\Purchasing;

use App\Models\PurchaseOrder;
use App\Models\PurchaseOrderItem;

class PricingService
{
    /**
     * Document statuses that are considered valid
     * for purchase history.
     *
     * A Purchase Order is considered a valid purchase
     * history only after approval has been granted.
     */
    private function validStatuses(): array
    {
        return [
            PurchaseOrder::STATUS_ON_PROGRESS,
            PurchaseOrder::STATUS_COMPLETED,
        ];
    }

    /**
     * Get Last Purchase Price.
     *
     * Returns:
     * - Last valid Purchase Price
     * - 0.00 if no purchase history exists
     */
    public function getLastPurchasePrice(
        int $itemId,
    ): float {
        $lastPrice = PurchaseOrderItem::query()
            ->select('purchase_order_items.unit_price')
            ->join(
                'purchase_orders',
                'purchase_orders.id',
                '=',
                'purchase_order_items.purchase_order_id',
            )
            ->where(
                'purchase_order_items.item_id',
                $itemId,
            )
            ->whereIn(
                'purchase_orders.status',
                $this->validStatuses(),
            )
            ->where(
                'purchase_orders.approval_status',
                PurchaseOrder::APPROVAL_APPROVED,
            )
            ->whereNull(
                'purchase_orders.deleted_at',
            )
            ->whereNull(
                'purchase_order_items.deleted_at',
            )
            ->orderByDesc(
                'purchase_orders.document_date',
            )
            ->orderByDesc(
                'purchase_order_items.id',
            )
            ->value(
                'purchase_order_items.unit_price',
            );

        return round(
            (float) ($lastPrice ?? 0),
            2,
        );
    }

    /**
     * Determine whether an Item has valid purchase history.
     */
    public function hasPurchaseHistory(
        int $itemId,
    ): bool {
        return PurchaseOrderItem::query()
            ->join(
                'purchase_orders',
                'purchase_orders.id',
                '=',
                'purchase_order_items.purchase_order_id',
            )
            ->where(
                'purchase_order_items.item_id',
                $itemId,
            )
            ->whereIn(
                'purchase_orders.status',
                $this->validStatuses(),
            )
            ->where(
                'purchase_orders.approval_status',
                PurchaseOrder::APPROVAL_APPROVED,
            )
            ->whereNull(
                'purchase_orders.deleted_at',
            )
            ->whereNull(
                'purchase_order_items.deleted_at',
            )
            ->exists();
    }

    /**
     * Get Last Purchase Order Item.
     *
     * Future usage:
     * - Last Supplier
     * - Last Lead Time
     * - Last Discount
     * - Last Tax
     * - Last Currency
     */
    public function getLastPurchaseOrderItem(
        int $itemId,
    ): ?PurchaseOrderItem {
        return PurchaseOrderItem::query()
            ->with([
                'purchaseOrder',
                'purchaseOrder.supplier',
            ])
            ->join(
                'purchase_orders',
                'purchase_orders.id',
                '=',
                'purchase_order_items.purchase_order_id',
            )
            ->where(
                'purchase_order_items.item_id',
                $itemId,
            )
            ->whereIn(
                'purchase_orders.status',
                $this->validStatuses(),
            )
            ->where(
                'purchase_orders.approval_status',
                PurchaseOrder::APPROVAL_APPROVED,
            )
            ->whereNull(
                'purchase_orders.deleted_at',
            )
            ->whereNull(
                'purchase_order_items.deleted_at',
            )
            ->orderByDesc(
                'purchase_orders.document_date',
            )
            ->orderByDesc(
                'purchase_order_items.id',
            )
            ->select(
                'purchase_order_items.*',
            )
            ->first();
    }
}