<?php

declare(strict_types=1);

namespace App\Filament\Resources\PurchaseRequisitions\Actions;

use Filament\Actions\Action;

class SubmitPurchaseRequisition
{
    public static function make(): Action
    {
        return Action::make('submit')

            /*
            |--------------------------------------------------------------------------
            | General Configuration
            |--------------------------------------------------------------------------
            */

            ->label('Submit')

            /*
            |--------------------------------------------------------------------------
            | Appearance
            |--------------------------------------------------------------------------
            */

            ->icon('heroicon-o-paper-airplane')

            ->color('primary')

            /*
            |--------------------------------------------------------------------------
            | Confirmation
            |--------------------------------------------------------------------------
            */

            ->requiresConfirmation()

            ->modalHeading('Submit Material Requisition')

            ->modalDescription(
                'Are you sure you want to submit this Material Requisition for approval?'
            )

            ->modalSubmitActionLabel('Submit')

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
                'Material Requisition submitted successfully.'
            );
    }
}