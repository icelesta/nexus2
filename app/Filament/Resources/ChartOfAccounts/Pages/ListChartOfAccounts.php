<?php

namespace App\Filament\Resources\ChartOfAccounts\Pages;

use App\Filament\Resources\ChartOfAccounts\ChartOfAccountResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListChartOfAccounts extends ListRecords
{
    protected static string $resource =
        ChartOfAccountResource::class;

    /*
    |--------------------------------------------------------------------------
    | Header Actions
    |--------------------------------------------------------------------------
    */

    protected function getHeaderActions(): array
    {
        return [

            CreateAction::make()

                ->label('New Chart of Account')

                ->icon('heroicon-o-plus'),

        ];
    }

    /*
    |--------------------------------------------------------------------------
    | Page Heading
    |--------------------------------------------------------------------------
    */

    public function getTitle(): string
    {
        return 'Chart of Accounts';
    }

    public function getSubheading(): ?string
    {
        return 'Manage company chart of accounts and account hierarchy.';
    }
}