<?php

declare(strict_types=1);

namespace App\Filament\Resources\Warehouses\Pages;

use App\Filament\Resources\Warehouses\WarehouseResource;
use Filament\Resources\Pages\CreateRecord;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

class CreateWarehouse extends CreateRecord
{
    protected static string $resource =
        WarehouseResource::class;

    protected function mutateFormDataBeforeCreate(
        array $data
    ): array {

        $data['uuid'] = (string) Str::uuid();

        $data['created_by'] = Auth::id();

        $data['updated_by'] = Auth::id();

        return $data;
    }

    protected function getCreatedNotificationTitle(): ?string
    {
        return 'Warehouse created successfully.';
    }

    protected function getRedirectUrl(): string
    {
        return static::getResource()::getUrl('index');
    }
}