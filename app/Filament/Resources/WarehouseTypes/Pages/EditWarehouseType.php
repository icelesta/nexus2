<?php

declare(strict_types=1);

namespace App\Filament\Resources\WarehouseTypes\Pages;

use App\Filament\Resources\WarehouseTypes\WarehouseTypeResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;
use Illuminate\Support\Facades\Auth;

class EditWarehouseType extends EditRecord
{
    protected static string $resource = WarehouseTypeResource::class;

    protected function mutateFormDataBeforeSave(array $data): array
    {
        $data['updated_by'] = Auth::id();

        return $data;
    }

    protected function getHeaderActions(): array
    {
        return [

            DeleteAction::make()
                ->tooltip('Delete')
                ->requiresConfirmation(),

        ];
    }

    protected function getSavedNotificationTitle(): ?string
    {
        return 'Warehouse Type updated successfully.';
    }

    protected function getRedirectUrl(): string
    {
        return static::getResource()::getUrl('index');
    }
}