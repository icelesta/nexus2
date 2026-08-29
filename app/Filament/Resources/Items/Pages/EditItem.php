<?php

declare(strict_types=1);

namespace App\Filament\Resources\Items\Pages;

use App\Filament\Resources\Items\ItemResource;
use Filament\Actions\DeleteAction;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\EditRecord;
use Filament\Actions\Action;

class EditItem extends EditRecord
{
    protected static string $resource = ItemResource::class;

    protected ?string $heading = 'Edit Item';

    protected ?string $subheading = 'Maintain inventory master information.';

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make()
                ->requiresConfirmation(),

            $this->getSaveFormAction()
                ->formId('form'),

            $this->getCancelFormAction(),
        ];
    }

    protected function afterSave(): void
    {
        Notification::make()
            ->title('Item Updated Successfully')
            ->body(
                sprintf(
                    'Item "%s" has been updated.',
                    $this->record->item_name
                )
            )
            ->success()
            ->duration(5000)
            ->send();
    }

    protected function getRedirectUrl(): string
    {
        return static::getResource()::getUrl('index');
    }

    public function hasUnsavedDataChangesAlert(): bool
    {
        return true;
    }

    protected function getFormActions(): array
    {
        return [];
    }
    
}