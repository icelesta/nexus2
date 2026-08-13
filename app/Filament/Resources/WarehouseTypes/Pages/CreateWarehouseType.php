<?php

declare(strict_types=1);

namespace App\Filament\Resources\WarehouseTypes\Pages;

use App\Filament\Resources\WarehouseTypes\WarehouseTypeResource;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

class CreateWarehouseType extends CreateRecord
{
    protected static string $resource = WarehouseTypeResource::class;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $data['uuid'] = (string) Str::uuid();

        $data['created_by'] = Auth::id();
        $data['updated_by'] = Auth::id();

        return $data;
    }

    protected function getCreatedNotificationTitle(): ?string
    {
        return 'Warehouse Type created successfully.';
    }

    protected function getRedirectUrl(): string
    {
        return static::getResource()::getUrl('index');
    }
}