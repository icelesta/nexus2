<?php

namespace App\Filament\Resources\CostCenters;

use App\Filament\Resources\CostCenters\Pages\CreateCostCenter;
use App\Filament\Resources\CostCenters\Pages\EditCostCenter;
use App\Filament\Resources\CostCenters\Pages\ListCostCenters;
use App\Filament\Resources\CostCenters\Schemas\CostCenterForm;
use App\Filament\Resources\CostCenters\Tables\CostCentersTable;
use App\Models\CostCenter;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;

class CostCenterResource extends Resource
{

    protected static ?string $navigationLabel = 'Cost Centers';

    protected static ?string $modelLabel = 'Cost Center';

    protected static ?string $pluralModelLabel = 'Cost Centers';

    protected static string|\UnitEnum|null $navigationGroup = 'Setup Master';

    protected static ?int $navigationSort = 6;

    protected static string|BackedEnum|null $navigationIcon =
    Heroicon::OutlinedBanknotes;

    protected static ?string $recordTitleAttribute = 'cost_center_name';


    public static function form(Schema $schema): Schema
    {
        return CostCenterForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return CostCentersTable::configure($table);
    }

    public static function getRelations(): array
    {
        return [];
    }

    public static function getPages(): array
    {
        return [
            'index'  => ListCostCenters::route('/'),
            'create' => CreateCostCenter::route('/create'),
            'edit'   => EditCostCenter::route('/{record}/edit'),
        ];
    }

    public static function getNavigationBadge(): ?string
    {
        return static::getModel()::query()
            ->where('is_active', true)
            ->count();
    }

    public static function getNavigationBadgeColor(): ?string
    {
        $count = static::getModel()::count();

        return match (true) {

            $count === 0 => 'danger',

            $count <= 10 => 'warning',

            default => 'success',
        };
    }


    public static function getGloballySearchableAttributes(): array
    {
        return [
            'cost_center_code',
            'cost_center_name',
            'short_name',
            'manager_name',
        ];
    }    

}