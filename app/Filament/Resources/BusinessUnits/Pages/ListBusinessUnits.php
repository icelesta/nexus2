<?php

namespace App\Filament\Resources\BusinessUnits\Pages;

use App\Filament\Resources\BusinessUnits\BusinessUnitResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListBusinessUnits extends ListRecords
{
    protected static string $resource = BusinessUnitResource::class;

    protected static ?string $title = 'Business Units';

    protected function getHeaderActions(): array
    {
        return [

            CreateAction::make()

                ->label('New Business Unit')

                ->icon('heroicon-o-plus')

                ->tooltip('Create New Business Unit'),

        ];
    }
}