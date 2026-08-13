<?php

declare(strict_types=1);

namespace App\Filament\Resources\TransactionNumberings\Pages;

use App\Filament\Resources\TransactionNumberings\TransactionNumberingResource;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\CreateRecord;

class CreateTransactionNumbering extends CreateRecord
{
    protected static string $resource =
        TransactionNumberingResource::class;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        return $data;
    }

    protected function afterCreate(): void
    {
        Notification::make()
            ->success()
            ->title('Transaction Numbering Created')
            ->body('The numbering configuration has been created successfully.')
            ->send();
    }

    protected function getRedirectUrl(): string
    {
        return static::getResource()::getUrl(
            'edit',
            ['record' => $this->record]
        );
    }
}