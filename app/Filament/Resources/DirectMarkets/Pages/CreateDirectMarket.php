<?php

declare(strict_types=1);

namespace App\Filament\Resources\DirectMarkets\Pages;

use App\Filament\Resources\DirectMarkets\DirectMarketResource;
use App\Services\Purchasing\DirectMarketService;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Database\Eloquent\Model;

class CreateDirectMarket extends CreateRecord
{
    protected static string $resource =
        DirectMarketResource::class;

    protected function handleRecordCreation(
        array $data
    ): Model {

        return app(DirectMarketService::class)
            ->create($data);
    }

    protected function getRedirectUrl(): string
    {
        return static::getResource()::getUrl('index');
    }
}