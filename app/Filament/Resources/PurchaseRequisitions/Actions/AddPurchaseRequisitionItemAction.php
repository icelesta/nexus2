<?php

declare(strict_types=1);

namespace App\Filament\Resources\PurchaseRequisitions\Actions;

use Filament\Actions\Action;

class AddPurchaseRequisitionItemAction
{
    /**
     * Build Add Item action.
     */
    public static function make(): Action
    {
        return Action::make('addItem')

            ->label('Add Item')

            ->icon('heroicon-m-plus')

            ->color('primary')

            ->modalHeading('Add Material Requisition Item')

            ->modalDescription(
                'Select an item from Item Master and enter the required purchasing information.'
            )

            ->modalWidth('5xl')

            ->modalSubmitActionLabel('Add Item')

            ->modalCancelActionLabel('Cancel')

            ->form([

                //
                // Sprint 2.2.2
                // PurchaseRequisitionItemModalForm::schema()
                //

            ])

            ->action(function (array $data): void {

                //
                // Sprint 2.2.4
                //
                // PurchaseRequisitionItemService::create(...)
                //

            });
    }
}