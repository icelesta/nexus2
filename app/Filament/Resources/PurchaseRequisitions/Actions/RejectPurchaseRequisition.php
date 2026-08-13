<?php

declare(strict_types=1);

namespace App\Filament\Resources\PurchaseRequisitions\Actions;

use Filament\Actions\Action;

class RejectPurchaseRequisition
{
    public static function make(): Action
    {
        return Action::make('reject')

            /*
            |--------------------------------------------------------------------------
            | General Configuration
            |--------------------------------------------------------------------------
            */

            ->label('Reject')

            /*
            |--------------------------------------------------------------------------
            | Appearance
            |--------------------------------------------------------------------------
            */

            ->icon('heroicon-o-x-circle')

            ->color('danger')

            /*
            |--------------------------------------------------------------------------
            | Confirmation
            |--------------------------------------------------------------------------
            */

            ->requiresConfirmation()

            ->modalHeading('Reject Material Requisition')

            ->modalDescription(
                'Are you sure you want to reject this Material Requisition?'
            )

            ->modalSubmitActionLabel('Reject')

            /*
            |--------------------------------------------------------------------------
            | Authorization
            |--------------------------------------------------------------------------
            */

            ->authorize(function (): bool {

                return true;

            })

            /*
            |--------------------------------------------------------------------------
            | Visibility
            |--------------------------------------------------------------------------
            */

            ->visible(function (): bool {

                return true;

            })

            /*
            |--------------------------------------------------------------------------
            | Action
            |--------------------------------------------------------------------------
            */

            ->action(function ($record): void {

                //

            })

            /*
            |--------------------------------------------------------------------------
            | Notification
            |--------------------------------------------------------------------------
            */

            ->successNotificationTitle(
                'Material Requisition rejected successfully.'
            );
    }
}