<?php

namespace App\Filament\Resources\TaxMasters\Pages;

use App\Filament\Resources\TaxMasters\TaxMasterResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListTaxMasters extends ListRecords
{
    protected static string $resource = TaxMasterResource::class;

    protected function getHeaderActions(): array
    {
        return [

            CreateAction::make()
                ->label('New Tax Master'),

        ];
    }

    public function getTitle(): string
    {
        return 'Tax Master';
    }

    public function getSubheading(): ?string
    {
        return 'Manage company tax configuration.';
    }
}