<?php

namespace App\Filament\Resources\JournalTypes\Pages;

use App\Filament\Resources\JournalTypes\JournalTypeResource;

use Filament\Actions\DeleteAction;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\EditRecord;

class EditJournalType extends EditRecord
{
    protected static string $resource = JournalTypeResource::class;

    protected function getHeaderActions(): array
    {
        return [

            DeleteAction::make()
                ->requiresConfirmation(),

        ];
    }

    protected function getSavedNotification(): ?Notification
    {
        return Notification::make()
            ->success()
            ->title('Journal Type Updated')
            ->body(
                "Journal Type '{$this->record->display_name}' has been updated successfully."
            );
    }

    protected function getRedirectUrl(): string
    {
        return static::getResource()::getUrl('index');
    }
}