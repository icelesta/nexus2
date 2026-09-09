<?php

declare(strict_types=1);

namespace App\Filament\Resources\AdmLimitMasters\Pages;

use App\Filament\Resources\AdmLimitMasters\AdmLimitMasterResource;
use Filament\Resources\Pages\EditRecord;
use Illuminate\Support\Facades\Auth;

class EditAdmLimitMaster extends EditRecord
{
    protected static string $resource = AdmLimitMasterResource::class;

    protected function mutateFormDataBeforeSave(
        array $data
    ): array {
        $data['updated_by'] = Auth::id();

        return $data;
    }

    protected function getRedirectUrl(): string
    {
        return AdmLimitMasterResource::getUrl('index');
    }
}