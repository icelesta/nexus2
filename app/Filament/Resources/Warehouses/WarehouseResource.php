<?php

declare(strict_types=1);

namespace App\Filament\Resources\Warehouses;

use App\Filament\Resources\Warehouses\Pages\CreateWarehouse;
use App\Filament\Resources\Warehouses\Pages\EditWarehouse;
use App\Filament\Resources\Warehouses\Pages\ListWarehouses;
use App\Filament\Resources\Warehouses\Schemas\WarehouseForm;
use App\Filament\Resources\Warehouses\Tables\WarehousesTable;
use App\Models\Warehouse;

use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Schema as DatabaseSchema;
use Throwable;

class WarehouseResource extends Resource
{
    protected static ?string $model = Warehouse::class;

    protected static string|BackedEnum|null $navigationIcon =
        Heroicon::OutlinedRectangleStack;

    protected static string|\UnitEnum|null $navigationGroup =
        'Inventory Setup';

    protected static ?int $navigationSort = 10;

    protected static ?string $navigationLabel =
        'Warehouses';

    protected static ?string $modelLabel =
        'Warehouse';

    protected static ?string $pluralModelLabel =
        'Warehouses';

    protected static ?string $slug =
        'warehouses';

    protected static ?string $recordTitleAttribute = 'warehouse_name';

    public static function form(Schema $schema): Schema
    {
        return WarehouseForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return WarehousesTable::configure($table);
    }

    public static function getRelations(): array
    {
        return [];
    }

    /*
    |--------------------------------------------------------------------------
    | Pages
    |--------------------------------------------------------------------------
    */

    public static function getPages(): array
    {
        return [

            'index'  => ListWarehouses::route('/'),

            'create' => CreateWarehouse::route('/create'),

            'edit'   => EditWarehouse::route('/{record}/edit'),

        ];
    }

    /*
    |--------------------------------------------------------------------------
    | Query
    |--------------------------------------------------------------------------
    */

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()

            ->with([

                'company',

                'branch',

                'warehouseType',

                'creator',

                'updater',

            ]);
    }

    /*
    |--------------------------------------------------------------------------
    | Global Search
    |--------------------------------------------------------------------------
    */

    public static function getGloballySearchableAttributes(): array
    {
        return [
            'warehouse_code',
            'warehouse_name',
            'short_name',
            'city',
            'province',
            'country',
            'contact_person',
            'phone',
            'email',
            'postal_code',
        ];
    }

    public static function getGlobalSearchResultTitle(
        Model $record
    ): string {

        return $record->display_name;
    }

    public static function getGlobalSearchResultDetails(
        Model $record
    ): array {

        return [

            'Company' =>
                $record->company?->company_name,

            'Branch' =>
                $record->branch?->branch_name,

            'Warehouse Type' =>
                $record->warehouseType?->type_name,

        ];
    }

    /*
    |--------------------------------------------------------------------------
    | Navigation Badge
    |--------------------------------------------------------------------------
    */

    public static function getNavigationBadge(): ?string
    {
        try {

            $table = (new (static::getModel()))->getTable();

            if (! DatabaseSchema::hasTable($table)) {
                return null;
            }

            return (string) static::getModel()

                ::active()

                ->count();

        } catch (Throwable) {

            return null;

        }
    }

    public static function getNavigationBadgeColor(): ?string
    {
        try {

            $table = (new (static::getModel()))->getTable();

            if (! DatabaseSchema::hasTable($table)) {
                return 'gray';
            }

            $count = static::getModel()

                ::active()

                ->count();

            return match (true) {

                $count === 0 => 'danger',

                $count <= 10 => 'warning',

                default => 'success',

            };

        } catch (Throwable) {

            return 'gray';

        }
    }

    public static function getNavigationBadgeTooltip(): ?string
    {
        return 'Active Warehouses';
    }
}