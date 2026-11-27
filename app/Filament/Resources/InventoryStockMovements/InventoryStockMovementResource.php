<?php

namespace App\Filament\Resources\InventoryStockMovements;

use App\Filament\Resources\InventoryStockMovements\Pages\ListInventoryStockMovements;
use App\Filament\Resources\InventoryStockMovements\Tables\InventoryStockMovementsTable;
use App\Models\InventoryStockMovement;
use Filament\Resources\Resource;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class InventoryStockMovementResource extends Resource
{
    protected static ?string $model = InventoryStockMovement::class;

    protected static ?string $navigationLabel = 'Stock Movement History';

    protected static ?string $modelLabel = 'Stock Movement';

    protected static ?string $pluralModelLabel = 'Stock Movement History';

    protected static string|\UnitEnum|null $navigationGroup = 'Inventory Control';

    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-clock';

    protected static ?int $navigationSort = 20;

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()
            ->with([
                'item.category',
                'item.brand',
                'item.baseUom',
                'warehouse',
                'goodsReceipt',
                'creator',
            ]);
    }

    public static function table(Table $table): Table
    {
        return InventoryStockMovementsTable::configure($table);
    }

    public static function getPages(): array
    {
        return [
            'index' => ListInventoryStockMovements::route('/'),
        ];
    }

    public static function canCreate(): bool
    {
        return false;
    }
}