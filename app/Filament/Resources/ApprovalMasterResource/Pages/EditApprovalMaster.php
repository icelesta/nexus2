<?php

declare(strict_types=1);

namespace App\Filament\Resources\ApprovalMasterResource\Pages;

use App\Filament\Resources\ApprovalMasterResource\ApprovalMasterResource;
use App\Services\Approval\ApprovalMasterService;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\EditRecord;

class EditApprovalMaster extends EditRecord
{
    protected static string $resource = ApprovalMasterResource::class;

    /*
    |--------------------------------------------------------------------------
    | Before Save
    |--------------------------------------------------------------------------
    |
    | Validate Approval Master configuration when changing
    | status from INACTIVE to ACTIVE.
    |
    */

    protected function beforeSave(): void
    {
        $isActivating = (bool) ($this->data['is_active'] ?? false);

        $wasInactive = ! (bool) $this->record->is_active;

        /*
        |--------------------------------------------------------------------------
        | Only validate when actually activating.
        |--------------------------------------------------------------------------
        */

        if (! $isActivating || ! $wasInactive) {
            return;
        }

        /*
        |--------------------------------------------------------------------------
        | Validate Approval Master configuration.
        |--------------------------------------------------------------------------
        */

        try {
            app(ApprovalMasterService::class)
                ->validateActivation($this->record);
        } catch (\Throwable $exception) {

            Notification::make()
                ->danger()
                ->title('Approval Master cannot be activated')
                ->body($exception->getMessage())
                ->send();

            $this->halt();
        }
    }

    /*
    |--------------------------------------------------------------------------
    | Redirect
    |--------------------------------------------------------------------------
    */

    protected function getRedirectUrl(): string
    {
        return ApprovalMasterResource::getUrl('index');
    }
}