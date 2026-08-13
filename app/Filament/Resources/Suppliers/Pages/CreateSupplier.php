<?php

namespace App\Filament\Resources\Suppliers\Pages;

use App\Filament\Resources\Suppliers\SupplierResource;
use Filament\Resources\Pages\CreateRecord;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

class CreateSupplier extends CreateRecord
{
    protected static string $resource =
        SupplierResource::class;

    protected function mutateFormDataBeforeCreate(
        array $data
    ): array {

        $data['uuid'] = (string) Str::uuid();

        $data['created_by'] = Auth::id();

        $data['updated_by'] = Auth::id();

        return $data;
    }
}