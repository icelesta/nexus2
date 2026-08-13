<?php

declare(strict_types=1);

namespace App\Filament\Resources\PurchaseRequisitions\Actions;

use Filament\Actions\Action;

class CancelPurchaseRequisition
{
    public static function make(): Action
    {
        return Action::make('cancel')

            /*
            |--------------------------------------------------------------------------
            | General Configuration
            |--------------------------------------------------------------------------
            */

            ->label('Cancel')

            /*
            |--------------------------------------------------------------------------
            | Appearance
            |--------------------------------------------------------------------------
            */

            ->icon('heroicon-o-no-symbol')

            ->color('warning')

            /*
            |--------------------------------------------------------------------------
            | Confirmation
            |--------------------------------------------------------------------------
            */

            ->requiresConfirmation()

            ->modalHeading('Cancel Material Requisition')

            ->modalDescription(
                'Are you sure you want to cancel this Material Requisition?'
            )

            ->modalSubmitActionLabel('Cancel')

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
                'Material Requisition cancelled successfully.'
            );
    }
}