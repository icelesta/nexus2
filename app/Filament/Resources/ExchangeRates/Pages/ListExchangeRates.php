<?php

namespace App\Filament\Resources\ExchangeRates\Pages;

use App\Filament\Resources\ExchangeRates\ExchangeRateResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListExchangeRates extends ListRecords
{
    protected static string $resource = ExchangeRateResource::class;

    /*
    |--------------------------------------------------------------------------
    | Header Actions
    |--------------------------------------------------------------------------
    */

    protected function getHeaderActions(): array
    {
        return [

            CreateAction::make()

                ->label('New Exchange Rate')

                ->icon('heroicon-o-plus'),

        ];
    }

    /*
    |--------------------------------------------------------------------------
    | Heading
    |--------------------------------------------------------------------------
    */

    public function getTitle(): string
    {
        return 'Exchange Rates';
    }

    public function getSubheading(): ?string
    {
        return 'Manage foreign exchange rates for all supported currencies.';
    }
}