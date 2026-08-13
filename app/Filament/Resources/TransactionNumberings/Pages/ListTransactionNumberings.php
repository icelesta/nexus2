<?php

declare(strict_types=1);

namespace App\Filament\Resources\TransactionNumberings\Pages;

use App\Filament\Resources\TransactionNumberings\TransactionNumberingResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListTransactionNumberings extends ListRecords
{
    protected static string $resource =
        TransactionNumberingResource::class;

    protected function getHeaderActions(): array
    {
        return [

            CreateAction::make()
                ->label('Create Transaction Numbering')
                ->icon('heroicon-o-plus'),

        ];
    }

    public function getTitle(): string
    {
        return 'Transaction Numbering';
    }

    public function getSubheading(): ?string
    {
        return 'Manage document numbering configurations for all ERP transactions.';
    }
}