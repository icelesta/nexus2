<?php

declare(strict_types=1);

namespace App\Filament\Resources\Brands\Pages;

use App\Filament\Resources\Brands\BrandResource;

use Filament\Actions\DeleteAction;
use Filament\Actions\ForceDeleteAction;
use Filament\Actions\RestoreAction;

use Filament\Resources\Pages\EditRecord;

class EditBrand extends EditRecord
{
    protected static string $resource = BrandResource::class;

    /**
     * --------------------------------------------------------------------------
     * Header Actions
     * --------------------------------------------------------------------------
     */

    protected function getHeaderActions(): array
    {
        return [

            RestoreAction::make(),

            DeleteAction::make(),

            ForceDeleteAction::make(),

        ];
    }

    /**
     * --------------------------------------------------------------------------
     * Redirect
     * --------------------------------------------------------------------------
     */

    protected function getRedirectUrl(): string
    {
        return static::getResource()::getUrl('index');
    }

    /**
     * --------------------------------------------------------------------------
     * Notification
     * --------------------------------------------------------------------------
     */

    protected function getSavedNotificationTitle(): ?string
    {
        return 'Brand updated successfully.';
    }
}