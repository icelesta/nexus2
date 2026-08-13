<?php

namespace App\Filament\Resources\MasterCategories\Pages;

use App\Filament\Resources\MasterCategories\MasterCategoryResource;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

class CreateMasterCategory extends CreateRecord
{
    protected static string $resource =
        MasterCategoryResource::class;

    protected function mutateFormDataBeforeCreate(
        array $data
    ): array {

        $data['uuid'] = (string) Str::uuid();

        $data['created_by'] = Auth::id();
        $data['updated_by'] = Auth::id();

        return $data;
    }
}