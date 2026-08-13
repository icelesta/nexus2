<?php

namespace App\Filament\Resources\JournalTypes\Pages;

use App\Filament\Resources\JournalTypes\JournalTypeResource;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\CreateRecord;

class CreateJournalType extends CreateRecord
{
    protected static string $resource = JournalTypeResource::class;

    protected function getCreatedNotification(): ?Notification
    {
        return Notification::make()
            ->success()
            ->title('Journal Type Created')
            ->body(
                "Journal Type '{$this->record->display_name}' has been created successfully."
            );
    }

    protected function getRedirectUrl(): string
    {
        return static::getResource()::getUrl('index');
    }
}