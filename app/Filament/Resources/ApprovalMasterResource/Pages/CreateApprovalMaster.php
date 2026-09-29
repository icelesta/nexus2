<?php

declare(strict_types=1);

namespace App\Filament\Resources\ApprovalMasterResource\Pages;

use App\Filament\Resources\ApprovalMasterResource\ApprovalMasterResource;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Support\Facades\Auth;

class CreateApprovalMaster extends CreateRecord
{
    protected static string $resource = ApprovalMasterResource::class;

    protected function getRedirectUrl(): string
    {
        return ApprovalMasterResource::getUrl('index');
    }

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $userId = Auth::id();

        $data['created_by'] = $userId;
        $data['updated_by'] = $userId;

        return $data;
    }
    
}