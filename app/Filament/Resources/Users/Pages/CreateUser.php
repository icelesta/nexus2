<?php

namespace App\Filament\Resources\Users\Pages;

use App\Filament\Resources\Users\UserResource;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Support\Facades\Auth;

class CreateUser extends CreateRecord
{
    protected static string $resource = UserResource::class;

    /**
     * Mutate form data before saving.
     * Observer sebenarnya sudah dapat mengisi created_by dan updated_by,
     * namun ini menjadi fallback apabila observer belum berjalan.
     */
    protected function mutateFormDataBeforeCreate(array $data): array
    {
        if (Auth::check()) {
            $data['created_by'] ??= Auth::id();
            $data['updated_by'] ??= Auth::id();
        }

        return $data;
    }

    /**
     * Before create hook.
     */
    protected function beforeCreate(): void
    {
        //
    }

    /**
     * After create hook.
     */
    protected function afterCreate(): void
    {
        //
    }

    /**
     * Success notification.
     */
    protected function getCreatedNotification(): ?Notification
    {
        return Notification::make()
            ->success()
            ->title('User Created')
            ->body("User '{$this->record->name}' has been created successfully.");
    }

    /**
     * Redirect after successful creation.
     */
    protected function getRedirectUrl(): string
    {
        return static::getResource()::getUrl('index');
    }
}