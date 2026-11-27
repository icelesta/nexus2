<?php

declare(strict_types=1);

namespace App\Services\Inventory;

use App\Models\GoodsReceipt;
use App\Models\InventoryStockMovement;
use App\Models\ItemStock;
use Illuminate\Support\Facades\DB;
use RuntimeException;

class InventoryStockPostingService
{
    /**
     * Post successfully submitted Receiving Record items.
     *
     * Must be called inside the submitDraft database transaction.
     */
    public function postGoodsReceipt(
        GoodsReceipt $goodsReceipt,
        array $positiveItems,
        int $actorId,
    ): void {
        foreach ($positiveItems as $row) {
            $item = $row['item'];
            $quantity = round((float) $row['quantity'], 4);

            if ($quantity <= 0) {
                continue;
            }

            $warehouseId = (int) $item->warehouse_id;

            if ($warehouseId <= 0) {
                continue;
            }

            $itemId = (int) $item->item_id;
            $goodsReceiptItemId = (int) $item->getKey();

            if ($itemId <= 0 || $goodsReceiptItemId <= 0) {
                throw new RuntimeException(
                    'Invalid item data while posting inventory stock.'
                );
            }

            // Prevent duplicate posting of the same RR item.
            $existingMovement = InventoryStockMovement::query()
                ->where('goods_receipt_item_id', $goodsReceiptItemId)
                ->lockForUpdate()
                ->exists();

            if ($existingMovement) {
                throw new RuntimeException(
                    "RR item [{$goodsReceiptItemId}] has already been posted to inventory."
                );
            }

            // Ensure the Item + Warehouse balance row exists.
            DB::table('item_stocks')->insertOrIgnore([
                'item_id' => $itemId,
                'warehouse_id' => $warehouseId,
                'qty_on_hand' => 0,
                'qty_reserved' => 0,
                'qty_available' => 0,
            ]);

            // Lock the current balance before calculating values.
            $stock = ItemStock::query()
                ->where('item_id', $itemId)
                ->where('warehouse_id', $warehouseId)
                ->lockForUpdate()
                ->first();

            if (! $stock) {
                throw new RuntimeException(
                    "Unable to initialize stock balance for item [{$itemId}] and warehouse [{$warehouseId}]."
                );
            }

            $balanceBefore = round((float) $stock->qty_on_hand, 4);
            $balanceAfter = round($balanceBefore + $quantity, 4);
            $reservedQty = round((float) $stock->qty_reserved, 4);
            $availableQty = round($balanceAfter - $reservedQty, 4);

            // Validate incoming purchase cost.
            $purchaseUnitCost = round(
                (float) ($row['purchase_unit_cost'] ?? -1),
                4
            );

            $currencyCode = strtoupper(
                (string) ($row['purchase_currency_code'] ?? '')
            );

            $exchangeRate = round(
                (float) ($row['exchange_rate'] ?? 0),
                6
            );

            $unitCostIdr = round(
                (float) ($row['unit_cost_idr'] ?? -1),
                4
            );

            if (
                $purchaseUnitCost < 0
                || $currencyCode === ''
                || $exchangeRate <= 0
                || $unitCostIdr < 0
            ) {
                throw new RuntimeException(
                    "Invalid purchase valuation data for RR item [{$goodsReceiptItemId}]."
                );
            }

            // DM valuation is currently IDR only.
            if ($currencyCode !== 'IDR' && ! $goodsReceipt->purchase_order_id) {
                throw new RuntimeException(
                    'Direct Market inventory valuation currently supports IDR only.'
                );
            }

            // Get the previous Moving Weighted Average for this Item + Warehouse.
            $previousValuation = InventoryStockMovement::query()
                ->where('item_id', $itemId)
                ->where('warehouse_id', $warehouseId)
                ->where('movement_type', 'RECEIPT')
                ->whereNotNull('avg_cost_after')
                ->orderByDesc('id')
                ->lockForUpdate()
                ->first();

            if ($balanceBefore > 0 && ! $previousValuation) {
                throw new RuntimeException(
                    "Existing stock for item [{$itemId}] and warehouse [{$warehouseId}] has no previous valuation history."
                );
            }

            $averageCostBefore = $balanceBefore > 0
                ? (float) $previousValuation->avg_cost_after
                : 0;

            $averageCostAfter = round(
                (
                    ($balanceBefore * $averageCostBefore)
                    + ($quantity * $unitCostIdr)
                ) / $balanceAfter,
                4
            );

            // Update current stock balance without changing reservations.
            $stock->forceFill([
                'qty_on_hand' => $balanceAfter,
                'qty_available' => $availableQty,
            ])->save();

            // Record the permanent inventory ledger entry and valuation.
            InventoryStockMovement::query()->create([
                'item_id' => $itemId,
                'warehouse_id' => $warehouseId,
                'goods_receipt_id' => $goodsReceipt->getKey(),
                'goods_receipt_item_id' => $goodsReceiptItemId,
                'movement_type' => 'RECEIPT',
                'quantity' => $quantity,
                'balance_before' => $balanceBefore,
                'balance_after' => $balanceAfter,
                'purchase_unit_cost' => $purchaseUnitCost,
                'purchase_currency_code' => $currencyCode,
                'exchange_rate' => $exchangeRate,
                'unit_cost_idr' => $unitCostIdr,
                'avg_cost_before' => $averageCostBefore,
                'avg_cost_after' => $averageCostAfter,
                'reference_type' => 'GOODS_RECEIPT',
                'reference_id' => $goodsReceipt->getKey(),
                'remarks' => 'Stock received through Receiving Record.',
                'created_by' => $actorId,
            ]);
        }
    }
}