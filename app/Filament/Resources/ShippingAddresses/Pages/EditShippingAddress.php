<?php

declare(strict_types=1);

namespace App\Filament\Resources\ShippingAddresses\Pages;

use App\Filament\Resources\ShippingAddresses\ShippingAddressResource;
use App\Models\ShippingAddress;
use App\Services\Shipping\ShippingAddressService;
use Filament\Actions\DeleteAction;
use Filament\Actions\ForceDeleteAction;
use Filament\Actions\RestoreAction;
use Filament\Resources\Pages\EditRecord;

class EditShippingAddress extends EditRecord
{
    protected static string $resource = ShippingAddressResource::class;

    protected function handleRecordUpdate(
        \Illuminate\Database\Eloquent\Model $record,
        array $data,
    ): \Illuminate\Database\Eloquent\Model {

        /** @var ShippingAddress $record */

        return app(ShippingAddressService::class)
            ->update($record, $data);
    }

    protected function getHeaderActions(): array
    {
        return [

            DeleteAction::make(),

            RestoreAction::make(),

            ForceDeleteAction::make(),

        ];
    }

    protected function getRedirectUrl(): string
    {
        return static::getResource()::getUrl('index');
    }
}