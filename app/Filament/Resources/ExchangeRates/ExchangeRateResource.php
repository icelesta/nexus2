<?php

namespace App\Filament\Resources\ExchangeRates;

use App\Filament\Resources\ExchangeRates\Pages\CreateExchangeRate;
use App\Filament\Resources\ExchangeRates\Pages\EditExchangeRate;
use App\Filament\Resources\ExchangeRates\Pages\ListExchangeRates;
use App\Filament\Resources\ExchangeRates\Schemas\ExchangeRateForm;
use App\Filament\Resources\ExchangeRates\Tables\ExchangeRatesTable;
use App\Models\ExchangeRate;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

class ExchangeRateResource extends Resource
{
    protected static ?string $model = ExchangeRate::class;

    /*
    |--------------------------------------------------------------------------
    | Navigation
    |--------------------------------------------------------------------------
    */

    protected static string|BackedEnum|null $navigationIcon =
        Heroicon::OutlinedBanknotes;

    protected static string|\UnitEnum|null $navigationGroup =
        'Finance Setup';

    protected static ?string $navigationLabel =
        'Exchange Rates';

    protected static ?string $modelLabel =
        'Exchange Rate';

    protected static ?string $pluralModelLabel =
        'Exchange Rates';

    protected static ?int $navigationSort = 3;

    /*
    |--------------------------------------------------------------------------
    | Record Title
    |--------------------------------------------------------------------------
    */

    protected static ?string $recordTitleAttribute =
        'exchange_date';

    /*
    |--------------------------------------------------------------------------
    | Form
    |--------------------------------------------------------------------------
    */

    public static function form(Schema $schema): Schema
    {
        return ExchangeRateForm::configure($schema);
    }

    /*
    |--------------------------------------------------------------------------
    | Table
    |--------------------------------------------------------------------------
    */

    public static function table(Table $table): Table
    {
        return ExchangeRatesTable::configure($table);
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
            'index'  => ListExchangeRates::route('/'),
            'create' => CreateExchangeRate::route('/create'),
            'edit'   => EditExchangeRate::route('/{record}/edit'),
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
                'fromCurrency',
                'toCurrency',
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
            'exchange_date',
            'rate_type',
            'source',
        ];
    }

    public static function getGlobalSearchResultDetails(
        Model $record
    ): array {
        return [
            'Currency Pair' => $record->currency_pair,
            'Exchange Date' => optional($record->exchange_date)->format('d M Y'),
            'Rate Type'     => $record->rate_type,
        ];
    }

    /*
    |--------------------------------------------------------------------------
    | Navigation Badge
    |--------------------------------------------------------------------------
    */

    public static function getNavigationBadge(): ?string
    {
        return (string) static::getModel()
            ::active()
            ->count();
    }

    public static function getNavigationBadgeColor(): ?string
    {
        $count = static::getModel()
            ::active()
            ->count();

        return match (true) {
            $count === 0 => 'danger',
            $count <= 10 => 'warning',
            default => 'success',
        };
    }

    public static function getNavigationBadgeTooltip(): ?string
    {
        return 'Active Exchange Rates';
    }
}