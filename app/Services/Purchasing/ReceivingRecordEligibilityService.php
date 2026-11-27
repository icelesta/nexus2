<?php

namespace App\Services\Purchasing;

use App\Models\AssignmentDirectMarket;
use App\Models\AssignmentDirectMarketItem;
use App\Models\GoodsReceiptItem;
use App\Models\PurchaseOrder;
use App\Models\PurchaseOrderItem;
use App\Models\ApprovalTransaction;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Collection;

class ReceivingRecordEligibilityService
{
    public function getEligiblePurchaseOrders(): Collection
    {
        return PurchaseOrder::query()
            ->where('approval_status', 'Approved')
            ->whereHas('items', function (Builder $query): void {
                $query->whereRaw(
                    '(ordered_qty - COALESCE((
                        SELECT SUM(gri.received_qty)
                        FROM goods_receipt_items gri
                        WHERE gri.purchase_order_item_id = purchase_order_items.id
                    ), 0)) > 0'
                );
            })
            ->whereExists(function ($query): void {
                $query
                    ->selectRaw('1')
                    ->from('approval_transactions')
                    ->whereColumn(
                        'approval_transactions.document_id',
                        'purchase_orders.id'
                    )
                    ->where(
                        'approval_transactions.document_type',
                        'PURCHASE_ORDER'
                    )
                    ->where(
                        'approval_transactions.status',
                        'APPROVED'
                    )
                    ->where(
                        'approval_transactions.current_level',
                        1
                    );
            })
            ->orderBy('document_no')
            ->get();
    }

    public function getEligibleAssignmentDirectMarkets(): Collection
    {
        return AssignmentDirectMarket::query()
            ->where('status', AssignmentDirectMarket::STATUS_APPROVED)
            ->whereHas('items', function (Builder $query): void {
                $query->whereRaw(
                    '(assigned_qty - COALESCE((
                        SELECT SUM(gri.received_qty)
                        FROM goods_receipt_items gri
                        WHERE gri.assignment_direct_market_item_id = assignment_direct_market_items.id
                    ), 0)) > 0'
                );
            })
            ->whereExists(function ($query): void {
                $query
                    ->selectRaw('1')
                    ->from('approval_transactions')
                    ->whereColumn(
                        'approval_transactions.document_id',
                        'assignment_direct_markets.id'
                    )
                    ->where(
                        'approval_transactions.document_type',
                        'ASSIGNMENT_DIRECT_MARKET'
                    )
                    ->where(
                        'approval_transactions.status',
                        'APPROVED'
                    )
                    ->where(
                        'approval_transactions.current_level',
                        2
                    );
            })
            ->orderBy('document_no')
            ->get();
    }

    public function getPurchaseOrderOutstanding(PurchaseOrder $purchaseOrder): float
    {
        return (float) $purchaseOrder->items->sum(
            fn (PurchaseOrderItem $item): float =>
                max(
                    0,
                    (float) $item->ordered_qty
                    - (float) GoodsReceiptItem::query()
                        ->where('purchase_order_item_id', $item->getKey())
                        ->sum('received_qty')
                )
        );
    }

    public function getAssignmentDirectMarketOutstanding(
        AssignmentDirectMarket $assignmentDirectMarket
    ): float {
        return (float) $assignmentDirectMarket->items->sum(
            fn (AssignmentDirectMarketItem $item): float =>
                max(
                    0,
                    (float) $item->assigned_qty
                    - (float) GoodsReceiptItem::query()
                        ->where(
                            'assignment_direct_market_item_id',
                            $item->getKey()
                        )
                        ->sum('received_qty')
                )
        );
    }
}