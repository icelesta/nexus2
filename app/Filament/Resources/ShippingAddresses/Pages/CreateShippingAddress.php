<?php

declare(strict_types=1);

namespace App\Filament\Resources\ShippingAddresses\Pages;

use App\Filament\Resources\ShippingAddresses\ShippingAddressResource;
use App\Models\ShippingAddress;
use App\Services\Shipping\ShippingAddressService;
use Filament\Resources\Pages\CreateRecord;

class CreateShippingAddress extends CreateRecord
{
    protected static string $resource = ShippingAddressResource::class;

    protected function handleRecordCreation(array $data): ShippingAddress
    {
        return app(ShippingAddressService::class)->create($data);
    }

    protected function getRedirectUrl(): string
    {
        return static::getResource()::getUrl('index');
    }
}