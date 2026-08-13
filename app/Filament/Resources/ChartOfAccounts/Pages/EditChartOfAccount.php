<?php

namespace App\Filament\Resources\ChartOfAccounts\Pages;

use App\Filament\Resources\ChartOfAccounts\ChartOfAccountResource;
use Filament\Actions\DeleteAction;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\EditRecord;

class EditChartOfAccount extends EditRecord
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

            DeleteAction::make()

                ->requiresConfirmation()

                ->successNotificationTitle(
                    'Chart of Account deleted successfully.'
                ),

        ];
    }

    /*
    |--------------------------------------------------------------------------
    | After Save
    |--------------------------------------------------------------------------
    */

    protected function afterSave(): void
    {
        Notification::make()

            ->title('Chart of Account updated successfully.')

            ->success()

            ->send();
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