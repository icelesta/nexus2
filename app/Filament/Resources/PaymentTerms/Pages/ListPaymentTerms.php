<?php

declare(strict_types=1);

namespace App\Filament\Resources\PaymentTerms\Pages;

use App\Filament\Resources\PaymentTerms\PaymentTermResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListPaymentTerms extends ListRecords
{
    protected static string $resource = PaymentTermResource::class;

    protected function getHeaderActions(): array
    {
        return [

            CreateAction::make()
                ->label('Payment Term')
                ->icon('heroicon-o-plus'),

        ];
    }

    public function getTitle(): string
    {
        return 'Payment Terms';
    }

    public function getSubheading(): ?string
    {
        return 'Manage customer and supplier payment terms.';
    }
}