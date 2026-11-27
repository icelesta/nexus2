<?php

declare(strict_types=1);

namespace App\Filament\Resources\InventoryStocks;

use App\Filament\Resources\InventoryStocks\Pages\ListInventoryStocks;
use App\Filament\Resources\InventoryStocks\Tables\InventoryStocksTable;
use App\Models\ItemStock;
use Filament\Resources\Resource;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use App\Models\InventoryStockMovement;

class InventoryStockResource extends Resource
{
    protected static ?string $model = ItemStock::class;

    protected static ?string $navigationLabel = 'Inventory Stock';

    protected static ?string $modelLabel = 'Inventory Stock';

    protected static ?string $pluralModelLabel = 'Inventory Stock';

    protected static string|\UnitEnum|null $navigationGroup = 'Inventory Control';

    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-cube';

    protected static ?int $navigationSort = 10;

    public static function getEloquentQuery(): Builder
    {
        $latestValuation = InventoryStockMovement::query()
            ->select('avg_cost_after')
            ->whereColumn(
                'inventory_stock_movements.item_id',
                'item_stocks.item_id'
            )
            ->whereColumn(
                'inventory_stock_movements.warehouse_id',
                'item_stocks.warehouse_id'
            )
            ->where('movement_type', 'RECEIPT')
            ->whereNotNull('avg_cost_after')
            ->orderByDesc('id')
            ->limit(1);

        $latestPurchaseCost = InventoryStockMovement::query()
            ->select('purchase_unit_cost')
            ->whereColumn(
                'inventory_stock_movements.item_id',
                'item_stocks.item_id'
            )
            ->whereColumn(
                'inventory_stock_movements.warehouse_id',
                'item_stocks.warehouse_id'
            )
            ->where('movement_type', 'RECEIPT')
            ->whereNotNull('purchase_unit_cost')
            ->orderByDesc('id')
            ->limit(1);

        $latestPurchaseCurrency = InventoryStockMovement::query()
            ->select('purchase_currency_code')
            ->whereColumn(
                'inventory_stock_movements.item_id',
                'item_stocks.item_id'
            )
            ->whereColumn(
                'inventory_stock_movements.warehouse_id',
                'item_stocks.warehouse_id'
            )
            ->where('movement_type', 'RECEIPT')
            ->whereNotNull('purchase_unit_cost')
            ->orderByDesc('id')
            ->limit(1);

        return parent::getEloquentQuery()
            ->with([
                'item.category',
                'item.brand',
                'item.baseUom',
                'warehouse',
            ])
            ->addSelect([
                'avg_unit_cost' => $latestValuation,
                'last_purchase_unit_cost' => $latestPurchaseCost,
                'last_purchase_currency' => $latestPurchaseCurrency,
            ])
            ->whereHas('item');
    }

    public static function table(Table $table): Table
    {
        return InventoryStocksTable::configure($table);
    }

    public static function getPages(): array
    {
        return [
            'index' => ListInventoryStocks::route('/'),
        ];
    }

    public static function canCreate(): bool
    {
        return false;
    }
}