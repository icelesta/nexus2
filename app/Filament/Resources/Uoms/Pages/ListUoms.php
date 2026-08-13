<?php

declare(strict_types=1);

namespace App\Filament\Resources\Uoms\Pages;

use App\Filament\Resources\Uoms\UomResource;

use Filament\Actions\CreateAction;

use Filament\Resources\Pages\ListRecords;

class ListUoms extends ListRecords
{
    protected static string $resource = UomResource::class;

    protected function getHeaderActions(): array
    {
        return [

            CreateAction::make()
                ->label('New Unit of Measure')
                ->icon('heroicon-o-plus'),

        ];
    }
}