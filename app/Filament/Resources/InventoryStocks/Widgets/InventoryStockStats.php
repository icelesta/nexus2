<?php

declare(strict_types=1);

namespace App\Filament\Resources\InventoryStocks\Widgets;

use App\Filament\Resources\InventoryStocks\Pages\ListInventoryStocks;
use Filament\Widgets\Concerns\InteractsWithPageTable;
use Filament\Widgets\StatsOverviewWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\DB;

class InventoryStockStats extends StatsOverviewWidget
{
    use InteractsWithPageTable;

    protected function getTablePage(): string
    {
        return ListInventoryStocks::class;
    }

    protected function getStats(): array
    {
        $query = $this->getPageTableQuery();

        $totalStockLines = (clone $query)->count();

        $totalItems = (clone $query)
            ->distinct()
            ->count('item_id');

        $totalWarehouses = (clone $query)
            ->whereNotNull('warehouse_id')
            ->distinct()
            ->count('warehouse_id');

        $zeroStockLines = $this->getZeroStockQuery()
            ->where('qty_on_hand', '<=', 0)
            ->count();

        // Total quantity across all filtered Item + Warehouse records.
        $totalQty = (clone $query)->sum('qty_on_hand');

        $totalReserved = (clone $query)->sum('qty_reserved');

        $totalAvailable = (clone $query)->sum('qty_available');

        // Total inventory value using the latest Moving Weighted Average.
        $totalValue = (clone $query)->sum(
            DB::raw("
                item_stocks.qty_on_hand * COALESCE(
                    (
                        SELECT ism.avg_cost_after
                        FROM inventory_stock_movements AS ism
                        WHERE ism.item_id = item_stocks.item_id
                          AND ism.warehouse_id = item_stocks.warehouse_id
                          AND ism.movement_type = 'RECEIPT'
                          AND ism.avg_cost_after IS NOT NULL
                        ORDER BY ism.id DESC
                        LIMIT 1
                    ),
                    0
                )
            ")
        );

        return [
            Stat::make('Total Stock Lines', number_format($totalStockLines))
                ->description('Item and warehouse stock records')
                ->descriptionIcon('heroicon-m-rectangle-stack')
                ->color('primary'),

            Stat::make('Total Items', number_format($totalItems))
                ->description('Unique items with stock records')
                ->descriptionIcon('heroicon-m-cube')
                ->color('success'),

            Stat::make('Total Warehouses', number_format($totalWarehouses))
                ->description('Unique warehouses')
                ->descriptionIcon('heroicon-m-building-office-2')
                ->color('info'),

            Stat::make('Zero Stock Lines', number_format($zeroStockLines))
                ->description('Item and warehouse with zero stock')
                ->descriptionIcon('heroicon-m-exclamation-triangle')
                ->color('warning'),

            Stat::make(
                'Total Reserved',
                rtrim(rtrim(number_format((float) $totalReserved, 4, ',', '.'), '0'), ',')
            )
                ->description('Total quantity reserved')
                ->descriptionIcon('heroicon-m-lock-closed')
                ->color('warning'),

            Stat::make(
                'Total Available',
                rtrim(rtrim(number_format((float) $totalAvailable, 4, ',', '.'), '0'), ',')
            )
                ->description('Total quantity available for use')
                ->descriptionIcon('heroicon-m-check-circle')
                ->color('info'),

            Stat::make(
                'Total Qty',
                rtrim(rtrim(number_format((float) $totalQty, 4, ',', '.'), '0'), ',')
            )
                ->description('Total quantity on hand')
                ->descriptionIcon('heroicon-m-cube-transparent')
                ->color('success'),

            Stat::make(
                'Total Value (IDR)',
                'Rp ' . number_format((float) $totalValue, 2, ',', '.')
            )
                ->description('Total inventory value at moving average cost')
                ->descriptionIcon('heroicon-m-banknotes')
                ->color('success'),
        ];
    }

    protected function getZeroStockQuery(): Builder
    {
        return $this->getTablePageInstance()->getFilteredTableQuery();
    }

    protected function getColumns(): int | array
    {
        return [
            'default' => 1,
            'sm' => 2,
            'md' => 3,
            'xl' => 4,
        ];
    }

}