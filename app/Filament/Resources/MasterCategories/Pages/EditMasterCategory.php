<?php

namespace App\Filament\Resources\MasterCategories\Pages;

use App\Filament\Resources\MasterCategories\MasterCategoryResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;
use Illuminate\Support\Facades\Auth;

class EditMasterCategory extends EditRecord
{
    protected static string $resource =
        MasterCategoryResource::class;

    protected function mutateFormDataBeforeSave(
        array $data
    ): array {

        $data['updated_by'] = Auth::id();

        return $data;
    }

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }


}