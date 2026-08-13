<?php

namespace App\Filament\Resources\JournalTypes\Pages;

use App\Filament\Resources\JournalTypes\JournalTypeResource;

use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListJournalTypes extends ListRecords
{
    protected static string $resource = JournalTypeResource::class;

    protected function getHeaderActions(): array
    {
        return [

            CreateAction::make(),

        ];
    }
}