<?php

namespace App\Filament\Resources\ExchangeRates\Pages;

use App\Filament\Resources\ExchangeRates\ExchangeRateResource;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\CreateRecord;

class CreateExchangeRate extends CreateRecord
{
    protected static string $resource = ExchangeRateResource::class;

    /*
    |--------------------------------------------------------------------------
    | After Create
    |--------------------------------------------------------------------------
    */

    protected function afterCreate(): void
    {
        Notification::make()

            ->title('Exchange Rate created successfully.')

            ->success()

            ->send();
    }

    /*
    |--------------------------------------------------------------------------
    | Redirect
    |--------------------------------------------------------------------------
    */

    protected function getRedirectUrl(): string
    {
        return static::getResource()::getUrl('index');
    }
}