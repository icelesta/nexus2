<?php

declare(strict_types=1);

namespace App\Filament\Resources\Manufacturers\Pages;

use App\Filament\Resources\Manufacturers\ManufacturerResource;

use Filament\Actions\DeleteAction;
use Filament\Actions\ForceDeleteAction;
use Filament\Actions\RestoreAction;

use Filament\Resources\Pages\EditRecord;

class EditManufacturer extends EditRecord
{
    protected static string $resource = ManufacturerResource::class;

    /**
     * Header Actions.
     */
    protected function getHeaderActions(): array
    {
        return [

            DeleteAction::make(),

            RestoreAction::make(),

            ForceDeleteAction::make(),

        ];
    }

    /**
     * Redirect.
     */
    protected function getRedirectUrl(): string
    {
        return static::getResource()::getUrl('index');
    }

    /**
     * Notification.
     */
    protected function getSavedNotificationTitle(): ?string
    {
        return 'Manufacturer updated successfully.';
    }
}