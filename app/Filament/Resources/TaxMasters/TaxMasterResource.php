<?php

namespace App\Filament\Resources\TaxMasters;

use App\Filament\Resources\TaxMasters\Pages\CreateTaxMaster;
use App\Filament\Resources\TaxMasters\Pages\EditTaxMaster;
use App\Filament\Resources\TaxMasters\Pages\ListTaxMasters;
use App\Filament\Resources\TaxMasters\Schemas\TaxMasterForm;
use App\Filament\Resources\TaxMasters\Tables\TaxMastersTable;
use App\Models\TaxMaster;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;

class TaxMasterResource extends Resource
{
    protected static ?string $model = TaxMaster::class;

    /*
    |--------------------------------------------------------------------------
    | Navigation
    |--------------------------------------------------------------------------
    */

    protected static string|BackedEnum|null $navigationIcon =
        Heroicon::OutlinedReceiptPercent;

    protected static string|\UnitEnum|null $navigationGroup =
        'Finance Setup';

    protected static ?string $navigationLabel =
        'Tax Masters';

    protected static ?string $modelLabel =
        'Tax Master';

    protected static ?string $pluralModelLabel =
        'Tax Masters';

    protected static ?int $navigationSort = 6;

    /*
    |--------------------------------------------------------------------------
    | Record Title
    |--------------------------------------------------------------------------
    */

    protected static ?string $recordTitleAttribute = 'tax_name';

    /*
    |--------------------------------------------------------------------------
    | Form & Table
    |--------------------------------------------------------------------------
    */

    public static function form(Schema $schema): Schema
    {
        return TaxMasterForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return TaxMastersTable::configure($table);
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

            'index'  => ListTaxMasters::route('/'),

            'create' => CreateTaxMaster::route('/create'),

            'edit'   => EditTaxMaster::route('/{record}/edit'),

        ];
    }

    /*
    |--------------------------------------------------------------------------
    | Navigation Badge
    |--------------------------------------------------------------------------
    */

    public static function getNavigationBadge(): ?string
    {
        return (string) static::getModel()::active()->count();
    }

    public static function getNavigationBadgeColor(): ?string
    {
        $count = static::getModel()::active()->count();

        return match (true) {

            $count === 0 => 'danger',

            $count <= 10 => 'warning',

            default => 'success',

        };
    }

    public static function getNavigationBadgeTooltip(): ?string
    {
        return 'Active Tax Masters';
    }

    public static function getGloballySearchableAttributes(): array
    {
        return [
            'tax_code',
            'tax_name',
            'tax_type',
            'calculation_method',
            'effective_date',
            'expired_date',
        ];
    }
    
}