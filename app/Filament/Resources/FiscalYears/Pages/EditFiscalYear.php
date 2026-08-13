<?php

namespace App\Filament\Resources\FiscalYears\Pages;

use App\Filament\Resources\FiscalYears\FiscalYearResource;
use App\Models\FiscalYear;
use Filament\Actions\DeleteAction;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\EditRecord;

class EditFiscalYear extends EditRecord
{
    protected static string $resource = FiscalYearResource::class;

    /**
     * Header Actions
     */
    protected function getHeaderActions(): array
    {
        return [

            DeleteAction::make()

                ->requiresConfirmation()

                ->visible(fn () => ! $this->record->is_default)

                ->successNotificationTitle(
                    'Fiscal Year deleted successfully.'
                ),

        ];
    }

    /**
     * After Update
     */
    protected function afterSave(): void
    {
        /** @var FiscalYear $record */
        $record = $this->record;

        /*
        |--------------------------------------------------------------------------
        | Only One Default Fiscal Year
        |--------------------------------------------------------------------------
        */

        if ($record->is_default) {

            FiscalYear::query()

                ->whereKeyNot($record->getKey())

                ->update([

                    'is_default' => false,

                ]);
        }

        Notification::make()

            ->title('Fiscal Year updated successfully.')

            ->success()

            ->send();
    }

    /**
     * Redirect
     */
    protected function getRedirectUrl(): string
    {
        return static::getResource()::getUrl('index');
    }
}