<?php

namespace App\Filament\Resources\BusinessUnits\Pages;

use App\Filament\Resources\BusinessUnits\BusinessUnitResource;
use Filament\Actions\DeleteAction;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\EditRecord;

class EditBusinessUnit extends EditRecord
{
    protected static string $resource = BusinessUnitResource::class;

    protected static ?string $title = 'Edit Business Unit';

    protected function getHeaderActions(): array
    {
        return [

            DeleteAction::make()

                ->requiresConfirmation()

                ->successNotification(

                    Notification::make()
                        ->success()
                        ->title('Business Unit Deleted')
                        ->body('Business Unit has been deleted successfully.')

                ),

        ];
    }

    protected function getRedirectUrl(): string
    {
        return static::getResource()::getUrl('index');
    }

    protected function getSavedNotification(): ?Notification
    {
        return Notification::make()

            ->success()

            ->title('Business Unit Updated')

            ->body('Business Unit has been updated successfully.');
    }
}