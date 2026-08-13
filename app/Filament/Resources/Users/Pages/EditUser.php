<?php

namespace App\Filament\Resources\Users\Pages;

use App\Filament\Resources\Users\UserResource;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\EditRecord;
use Illuminate\Support\Facades\Auth;

class EditUser extends EditRecord
{
    protected static string $resource = UserResource::class;

    /*
    |--------------------------------------------------------------------------
    | Header Actions
    |--------------------------------------------------------------------------
    |
    | Nexus 2 tidak menghapus User.
    | User cukup dinonaktifkan melalui field is_active.
    |
    */

    protected function getHeaderActions(): array
    {
        return [
            //
        ];
    }

    /*
    |--------------------------------------------------------------------------
    | Mutate Data Before Save
    |--------------------------------------------------------------------------
    */

    protected function mutateFormDataBeforeSave(array $data): array
    {
        if (Auth::check()) {
            $data['updated_by'] = Auth::id();
        }

        return $data;
    }

    /*
    |--------------------------------------------------------------------------
    | Before Save
    |--------------------------------------------------------------------------
    */

    protected function beforeSave(): void
    {
        //
    }

    /*
    |--------------------------------------------------------------------------
    | After Save
    |--------------------------------------------------------------------------
    */

    protected function afterSave(): void
    {
        //
    }

    /*
    |--------------------------------------------------------------------------
    | Success Notification
    |--------------------------------------------------------------------------
    */

    protected function getSavedNotification(): ?Notification
    {
        return Notification::make()
            ->success()
            ->title('User Updated')
            ->body("User '{$this->record->name}' has been updated successfully.");
    }

    /*
    |--------------------------------------------------------------------------
    | Redirect
    |--------------------------------------------------------------------------
    */

    protected function getRedirectUrl(): string
    {
        return static::getResource()::getUrl('index');
    }
}