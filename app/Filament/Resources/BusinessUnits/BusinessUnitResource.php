<?php

namespace App\Filament\Resources\BusinessUnits;

use App\Filament\Resources\BusinessUnits\Pages\CreateBusinessUnit;
use App\Filament\Resources\BusinessUnits\Pages\EditBusinessUnit;
use App\Filament\Resources\BusinessUnits\Pages\ListBusinessUnits;
use App\Filament\Resources\BusinessUnits\Schemas\BusinessUnitForm;
use App\Filament\Resources\BusinessUnits\Tables\BusinessUnitsTable;
use App\Models\BusinessUnit;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;

class BusinessUnitResource extends Resource
{
    protected static ?string $model = BusinessUnit::class;

    protected static string|BackedEnum|null $navigationIcon =
        Heroicon::OutlinedRectangleStack;

    protected static string|\UnitEnum|null $navigationGroup =
        'Setup Master';

    protected static ?string $navigationLabel =
        'Business Units';

    protected static ?string $modelLabel =
        'Business Unit';

    protected static ?string $pluralModelLabel =
        'Business Units';

    protected static ?int $navigationSort = 3;

    protected static ?string $recordTitleAttribute = 'business_unit_name';

    public static function form(Schema $schema): Schema
    {
        return BusinessUnitForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return BusinessUnitsTable::configure($table);
    }

    public static function getRelations(): array
    {
        return [];
    }

    public static function getPages(): array
    {
        return [

            'index'  => ListBusinessUnits::route('/'),

            'create' => CreateBusinessUnit::route('/create'),

            'edit'   => EditBusinessUnit::route('/{record}/edit'),

        ];
    }

    /*
    |--------------------------------------------------------------------------
    | Navigation Badge
    |--------------------------------------------------------------------------
    */

    public static function getNavigationBadge(): ?string
    {
        return (string) static::getModel()::query()
            ->where('is_active', true)
            ->count();
    }

    public static function getNavigationBadgeColor(): ?string
    {
        $count = static::getModel()::query()
            ->where('is_active', true)
            ->count();

        return match (true) {

            $count === 0 => 'danger',

            $count <= 10 => 'warning',

            default => 'success',

        };
    }

    public static function getNavigationBadgeTooltip(): ?string
    {
        return 'Active Business Units';
    }

    /*
    |--------------------------------------------------------------------------
    | Global Search
    |--------------------------------------------------------------------------
    */

    public static function getGloballySearchableAttributes(): array
    {
        return [
            'business_unit_code',
            'business_unit_name',
            'short_name',
            'manager_name',
            'email',
            'phone',
        ];
    }

}