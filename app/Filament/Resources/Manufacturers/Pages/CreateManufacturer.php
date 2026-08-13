<?php

declare(strict_types=1);

namespace App\Filament\Resources\Manufacturers\Pages;

use App\Filament\Resources\Manufacturers\ManufacturerResource;
use Filament\Resources\Pages\CreateRecord;

class CreateManufacturer extends CreateRecord
{
    protected static string $resource = ManufacturerResource::class;

    /**
     * Redirect after create.
     */
    protected function getRedirectUrl(): string
    {
        return static::getResource()::getUrl('index');
    }

    /**
     * Notification.
     */
    protected function getCreatedNotificationTitle(): ?string
    {
        return 'Manufacturer created successfully.';
    }
}