<?php

declare(strict_types=1);

namespace App\Filament\Resources\Items;

use App\Models\Item;

use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables\Table;

use Illuminate\Database\Eloquent\Builder;

use App\Filament\Resources\Items\Schemas\ItemForm;
use App\Filament\Resources\Items\Tables\ItemsTable;

use App\Filament\Resources\Items\Pages\CreateItem;
use App\Filament\Resources\Items\Pages\EditItem;
use App\Filament\Resources\Items\Pages\ListItems;

use App\Filament\Resources\Items\RelationManagers\AttachmentsRelationManager;
use App\Filament\Resources\Items\RelationManagers\BarcodesRelationManager;
use App\Filament\Resources\Items\RelationManagers\BatchesRelationManager;
use App\Filament\Resources\Items\RelationManagers\ImagesRelationManager;
use App\Filament\Resources\Items\RelationManagers\PricesRelationManager;
use App\Filament\Resources\Items\RelationManagers\SerialsRelationManager;
use App\Filament\Resources\Items\RelationManagers\SpecificationsRelationManager;
use App\Filament\Resources\Items\RelationManagers\StocksRelationManager;
use App\Filament\Resources\Items\RelationManagers\SuppliersRelationManager;

class ItemResource extends Resource
{
    protected static ?string $model = Item::class;

    /*
    |--------------------------------------------------------------------------
    | Navigation
    |--------------------------------------------------------------------------
    */

    protected static ?string $navigationLabel = 'Item Master';

    protected static ?string $modelLabel = 'Item';

    protected static ?string $pluralModelLabel = 'Items';

    protected static string|\UnitEnum|null $navigationGroup = 'Inventory Setup';

    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-cube';

    protected static ?int $navigationSort = 20;

    /*
    |--------------------------------------------------------------------------
    | Global Search
    |--------------------------------------------------------------------------
    */

    public static function getGloballySearchableAttributes(): array
    {
        return [

            'item_code',

            'item_name',

            'part_number',

            'drawing_number',

            'barcode',

            'search_name',

            'model_number',

            'hs_code',

        ];
    }

    /*
    |--------------------------------------------------------------------------
    | Query Optimization
    |--------------------------------------------------------------------------
    */

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()

            ->with([

                'category',

                'brand',

                'manufacturer',

                'baseUom',

                'warehouse',

                'primaryImage',

                'creator',

                'updater',

                'purchaseUom',

                'salesUom',

                'warehouseType',

            ])

            ->withCount([
                'suppliers',

                'prices',

                'images',

                'attachments',

                'serials',
                
                'batches',
            ])

            ->withSum(

                'stocks',

                'qty_on_hand'

            );
    }

    /*
    |--------------------------------------------------------------------------
    | Form
    |--------------------------------------------------------------------------
    */

    public static function form(
        Schema $schema,
    ): Schema {

        return ItemForm::configure($schema);
    }

    /*
    |--------------------------------------------------------------------------
    | Table
    |--------------------------------------------------------------------------
    */

    public static function table(
        Table $table,
    ): Table {

        return ItemsTable::configure($table);
    }

    /*
    |--------------------------------------------------------------------------
    | Relation Managers
    |--------------------------------------------------------------------------
    */

    public static function getRelations(): array
    {
        return [

            SuppliersRelationManager::class,

            PricesRelationManager::class,

            StocksRelationManager::class,

            ImagesRelationManager::class,

            BarcodesRelationManager::class,

            AttachmentsRelationManager::class,

            SpecificationsRelationManager::class,

            BatchesRelationManager::class,

            SerialsRelationManager::class,

        ];
    }

    /*
    |--------------------------------------------------------------------------
    | Pages
    |--------------------------------------------------------------------------
    */

    public static function getPages(): array
    {
        return [

            'index'  => ListItems::route('/'),

            'create' => CreateItem::route('/create'),

            'edit'   => EditItem::route('/{record}/edit'),

        ];
    }

    public static function getNavigationBadge(): ?string
    {
        return (string) static::getModel()
            ::where('is_active', true)
            ->count();
    }

    public static function getNavigationBadgeColor(): ?string
    {
        return 'success';
    }
    

}