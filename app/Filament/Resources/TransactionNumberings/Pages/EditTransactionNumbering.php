<?php

declare(strict_types=1);

namespace App\Filament\Resources\TransactionNumberings\Pages;

use App\Filament\Resources\TransactionNumberings\TransactionNumberingResource;
use Filament\Actions\DeleteAction;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\EditRecord;

class EditTransactionNumbering extends EditRecord
{
    protected static string $resource =
        TransactionNumberingResource::class;

    protected function mutateFormDataBeforeSave(array $data): array
    {
        return $data;
    }

    protected function afterSave(): void
    {
        Notification::make()
            ->success()
            ->title('Transaction Numbering Updated')
            ->body('Changes have been saved successfully.')
            ->send();
    }

    protected function getHeaderActions(): array
    {
        return [

            DeleteAction::make()
                ->requiresConfirmation()
                ->modalHeading('Delete Transaction Numbering')
                ->modalDescription(
                    'Are you sure you want to permanently delete this numbering configuration?'
                )
                ->successNotificationTitle(
                    'Transaction Numbering deleted successfully.'
                ),

        ];
    }
}