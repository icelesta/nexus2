<?php

namespace App\Filament\Resources\TaxMasters\Pages;

use App\Filament\Resources\TaxMasters\TaxMasterResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;
use Illuminate\Support\Facades\Auth;

class EditTaxMaster extends EditRecord
{
    protected static string $resource = TaxMasterResource::class;

    protected function mutateFormDataBeforeSave(array $data): array
    {
        $data['updated_by'] = Auth::id();

        return $data;
    }

    protected function getSavedNotificationTitle(): ?string
    {
        return 'Tax Master updated successfully.';
    }

    protected function getRedirectUrl(): string
    {
        return static::getResource()::getUrl('index');
    }

    protected function getHeaderActions(): array
    {
        return [

            DeleteAction::make()
                ->requiresConfirmation(),

        ];
    }
}