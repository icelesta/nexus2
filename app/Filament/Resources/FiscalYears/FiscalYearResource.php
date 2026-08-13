<?php

namespace App\Filament\Resources\FiscalYears;

use App\Filament\Resources\FiscalYears\Pages\CreateFiscalYear;
use App\Filament\Resources\FiscalYears\Pages\EditFiscalYear;
use App\Filament\Resources\FiscalYears\Pages\ListFiscalYears;
use App\Filament\Resources\FiscalYears\Schemas\FiscalYearForm;
use App\Filament\Resources\FiscalYears\Tables\FiscalYearsTable;
use App\Models\FiscalYear;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;

class FiscalYearResource extends Resource
{
    protected static ?string $model = FiscalYear::class;

    protected static string|BackedEnum|null $navigationIcon =
        Heroicon::OutlinedCalendarDays;

    protected static ?string $navigationLabel =
        'Fiscal Years';

    protected static ?string $modelLabel =
        'Fiscal Year';

    protected static ?string $pluralModelLabel =
        'Fiscal Years';

    protected static string|\UnitEnum|null $navigationGroup =
        'Finance Setup';

    protected static ?int $navigationSort = 2;

    protected static ?string $recordTitleAttribute = 'fiscal_name';

    public static function form(Schema $schema): Schema
    {
        return FiscalYearForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return FiscalYearsTable::configure($table);
    }

    public static function getRelations(): array
    {
        return [];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListFiscalYears::route('/'),
            'create' => CreateFiscalYear::route('/create'),
            'edit' => EditFiscalYear::route('/{record}/edit'),
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
            'fiscal_code',
            'fiscal_name',
            'status',
            'description',
        ];
    }
    
    
}