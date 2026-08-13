<?php

declare(strict_types=1);

namespace App\Filament\Resources\Warehouses\Pages;

use App\Filament\Resources\Warehouses\WarehouseResource;

use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

use Illuminate\Support\Facades\Auth;

class EditWarehouse extends EditRecord
{
    protected static string $resource =
        WarehouseResource::class;

    protected function mutateFormDataBeforeSave(
        array $data
    ): array {

        $data['updated_by'] = Auth::id();

        return $data;
    }

    protected function getHeaderActions(): array
    {
        return [

            DeleteAction::make()
                ->requiresConfirmation(),

        ];
    }

    protected function getSavedNotificationTitle(): ?string
    {
        return 'Warehouse updated successfully.';
    }

    protected function getRedirectUrl(): string
    {
        return static::getResource()::getUrl('index');
    }
}