<?php

declare(strict_types=1);

namespace App\Filament\Resources\PurchaseRequisitions\Actions;

use Filament\Actions\Action;

class ApprovePurchaseRequisition
{
    public static function make(): Action
    {
        return Action::make('approve')

            /*
            |--------------------------------------------------------------------------
            | General Configuration
            |--------------------------------------------------------------------------
            */

            ->label('Approve')

            /*
            |--------------------------------------------------------------------------
            | Appearance
            |--------------------------------------------------------------------------
            */

            ->icon('heroicon-o-check-circle')

            ->color('success')

            /*
            |--------------------------------------------------------------------------
            | Confirmation
            |--------------------------------------------------------------------------
            */

            ->requiresConfirmation()

            ->modalHeading('Approve Material Requisition')

            ->modalDescription(
                'Are you sure you want to approve this Material Requisition?'
            )

            ->modalSubmitActionLabel('Approve')

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
                'Material Requisition approved successfully.'
            );
    }
}