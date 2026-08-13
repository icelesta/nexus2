<?php

namespace App\Filament\Resources\MasterCategories\Pages;

use App\Filament\Resources\MasterCategories\MasterCategoryResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListMasterCategories extends ListRecords
{
    protected static string $resource = MasterCategoryResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
