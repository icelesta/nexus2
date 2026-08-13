<?php

namespace App\Filament\Resources\FiscalYears\Pages;

use App\Filament\Resources\FiscalYears\FiscalYearResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListFiscalYears extends ListRecords
{
    protected static string $resource = FiscalYearResource::class;

    /*
    |--------------------------------------------------------------------------
    | Header Actions
    |--------------------------------------------------------------------------
    */

    protected function getHeaderActions(): array
    {
        return [

            CreateAction::make()
                ->label('New Fiscal Year')
                ->icon('heroicon-o-plus'),

        ];
    }

    /*
    |--------------------------------------------------------------------------
    | Heading
    |--------------------------------------------------------------------------
    */

    public function getTitle(): string
    {
        return 'Fiscal Years';
    }

    public function getSubheading(): ?string
    {
        return 'Manage company fiscal years and accounting periods.';
    }
}