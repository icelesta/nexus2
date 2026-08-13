<?php

declare(strict_types=1);

namespace App\Filament\Resources\Manufacturers;

use App\Filament\Resources\Manufacturers\Pages\CreateManufacturer;
use App\Filament\Resources\Manufacturers\Pages\EditManufacturer;
use App\Filament\Resources\Manufacturers\Pages\ListManufacturers;

use App\Filament\Resources\Manufacturers\Schemas\ManufacturerForm;
use App\Filament\Resources\Manufacturers\Tables\ManufacturersTable;

use App\Models\Manufacturer;

use BackedEnum;
use UnitEnum;

use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class ManufacturerResource extends Resource
{
    protected static ?string $model = Manufacturer::class;

    /*
    |--------------------------------------------------------------------------
    | Navigation
    |--------------------------------------------------------------------------
    */

    protected static ?string $navigationLabel = 'Manufacturers';

    protected static ?string $modelLabel = 'Manufacturer';

    protected static ?string $pluralModelLabel = 'Manufacturers';

    protected static string|UnitEnum|null $navigationGroup =
        'Inventory Setup';

    protected static ?int $navigationSort = 4;

    protected static string|BackedEnum|null $navigationIcon =
        Heroicon::OutlinedBuildingOffice2;

    /*
    |--------------------------------------------------------------------------
    | Record
    |--------------------------------------------------------------------------
    */

    protected static ?string $recordTitleAttribute =
        'manufacturer_name';

    /*
    |--------------------------------------------------------------------------
    | Form
    |--------------------------------------------------------------------------
    */

    public static function form(
        Schema $schema,
    ): Schema {

        return ManufacturerForm::configure($schema);
    }

    /*
    |--------------------------------------------------------------------------
    | Table
    |--------------------------------------------------------------------------
    */

    public static function table(
        Table $table,
    ): Table {

        return ManufacturersTable::configure($table);
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

            'index' =>
                ListManufacturers::route('/'),

            'create' =>
                CreateManufacturer::route('/create'),

            'edit' =>
                EditManufacturer::route('/{record}/edit'),

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
            ->withoutGlobalScopes([
                SoftDeletingScope::class,
            ]);
    }

    public static function getRecordRouteBindingEloquentQuery(): Builder
    {
        return parent::getRecordRouteBindingEloquentQuery()
            ->withoutGlobalScopes([
                SoftDeletingScope::class,
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

            'manufacturer_code',

            'manufacturer_name',

            'short_name',

            'country',

            'city',

        ];
    }

    public static function getGlobalSearchResultTitle(
        Model $record,
    ): string {

        return "{$record->manufacturer_code} - {$record->manufacturer_name}";
    }

    /*
    |--------------------------------------------------------------------------
    | Navigation Badge
    |--------------------------------------------------------------------------
    */

    public static function getNavigationBadge(): ?string
    {
        return (string) Manufacturer::query()
            ->active()
            ->count();
    }

    public static function getNavigationBadgeColor(): ?string
    {
        return 'primary';
    }
}