<?php

declare(strict_types=1);

namespace App\Filament\Resources\WarehouseTypes;

use App\Filament\Resources\WarehouseTypes\Pages\CreateWarehouseType;
use App\Filament\Resources\WarehouseTypes\Pages\EditWarehouseType;
use App\Filament\Resources\WarehouseTypes\Pages\ListWarehouseTypes;
use App\Filament\Resources\WarehouseTypes\Schemas\WarehouseTypeForm;
use App\Filament\Resources\WarehouseTypes\Tables\WarehouseTypesTable;
use App\Models\WarehouseType;

use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Schema as DatabaseSchema;
use Throwable;

class WarehouseTypeResource extends Resource
{
    protected static ?string $model = WarehouseType::class;

    protected static string|BackedEnum|null $navigationIcon =
        Heroicon::OutlinedBuildingStorefront;

    protected static string|\UnitEnum|null $navigationGroup =
        'Inventory Setup';

    protected static ?int $navigationSort = 1;

    protected static ?string $navigationLabel =
        'Warehouse Type';

    protected static ?string $modelLabel =
        'Warehouse Type';

    protected static ?string $pluralModelLabel =
        'Warehouse Types';

    protected static ?string $slug =
        'warehouse-types';

    protected static ?string $recordTitleAttribute =
        'type_name';

    /*
    |--------------------------------------------------------------------------
    | Form
    |--------------------------------------------------------------------------
    */

    public static function form(Schema $schema): Schema
    {
        return WarehouseTypeForm::configure($schema);
    }

    /*
    |--------------------------------------------------------------------------
    | Table
    |--------------------------------------------------------------------------
    */

    public static function table(Table $table): Table
    {
        return WarehouseTypesTable::configure($table);
    }

    /*
    |--------------------------------------------------------------------------
    | Relations
    |--------------------------------------------------------------------------
    */

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

            'index'  => ListWarehouseTypes::route('/'),

            'create' => CreateWarehouseType::route('/create'),

            'edit'   => EditWarehouseType::route('/{record}/edit'),

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
            'type_code',
            'type_name',
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

            'Type Code' =>
                $record->type_code,

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
        return 'Active Warehouse Types';
    }
}