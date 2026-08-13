<?php

namespace App\Filament\Resources\ProfitCenters;

use App\Filament\Resources\ProfitCenters\Pages\CreateProfitCenter;
use App\Filament\Resources\ProfitCenters\Pages\EditProfitCenter;
use App\Filament\Resources\ProfitCenters\Pages\ListProfitCenters;
use App\Filament\Resources\ProfitCenters\Schemas\ProfitCenterForm;
use App\Filament\Resources\ProfitCenters\Tables\ProfitCentersTable;
use App\Models\ProfitCenter;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;

class ProfitCenterResource extends Resource
{
    protected static ?string $model = ProfitCenter::class;

    protected static string|BackedEnum|null $navigationIcon =
        Heroicon::OutlinedChartBar;

    protected static string|\UnitEnum|null $navigationGroup =
    'Setup Master';

    protected static ?int $navigationSort = 7;

    protected static ?string $recordTitleAttribute =
        'profit_center_name';

    public static function form(Schema $schema): Schema
    {
        return ProfitCenterForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return ProfitCentersTable::configure($table);
    }

    public static function getRelations(): array
    {
        return [];
    }

    public static function getPages(): array
    {
        return [
            'index'  => ListProfitCenters::route('/'),
            'create' => CreateProfitCenter::route('/create'),
            'edit'   => EditProfitCenter::route('/{record}/edit'),
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

    public static function shouldRegisterNavigation(): bool
    {
        return false;
    }    

    public static function getGloballySearchableAttributes(): array
    {
        return [
            'profit_center_code',
            'profit_center_name',
            'short_name',
            'manager_name',
        ];
    }    
    
}