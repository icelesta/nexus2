<?php

namespace App\Filament\Resources\BusinessUnits\Pages;

use App\Filament\Resources\BusinessUnits\BusinessUnitResource;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

class CreateBusinessUnit extends CreateRecord
{
    protected static string $resource = BusinessUnitResource::class;

    protected static ?string $title = 'Create Business Unit';

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        /*
        |--------------------------------------------------------------------------
        | UUID
        |--------------------------------------------------------------------------
        */

        $data['uuid'] = (string) Str::uuid();

        /*
        |--------------------------------------------------------------------------
        | Audit Trail
        |--------------------------------------------------------------------------
        */

        $data['created_by'] = Auth::id();
        $data['updated_by'] = Auth::id();

        /*
        |--------------------------------------------------------------------------
        | Default Values
        |--------------------------------------------------------------------------
        */

        $data['is_active'] = (bool) ($data['is_active'] ?? true);

        $data['is_default'] = (bool) ($data['is_default'] ?? false);

        $data['sort_order'] = (int) ($data['sort_order'] ?? 1);

        /*
        |--------------------------------------------------------------------------
        | Company
        |--------------------------------------------------------------------------
        */

        if (empty($data['company_id'])) {
            $data['company_id'] = 1;
        }

        return $data;
    }

    protected function getRedirectUrl(): string
    {
        return static::getResource()::getUrl('index');
    }

    protected function getCreatedNotification(): ?Notification
    {
        return Notification::make()
            ->success()
            ->title('Business Unit Created')
            ->body('Business Unit has been created successfully.');
    }
}