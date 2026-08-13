<?php

declare(strict_types=1);

namespace App\Filament\Resources\PurchaseRequisitions\Actions;

use App\Models\ApprovalTransaction;
use App\Models\PurchaseRequisition;
use App\Services\Approval\ApprovalTransactionService;
use Filament\Actions\Action;
use Filament\Forms\Components\Textarea;
use Filament\Notifications\Notification;
use Throwable;

class RejectPurchaseRequisition
{
    public static function make(): Action
    {
        return Action::make('reject')

            /*
            |--------------------------------------------------------------------------
            | Label
            |--------------------------------------------------------------------------
            */

            ->label('Reject')

            ->icon('heroicon-o-x-circle')

            ->color('danger')

            /*
            |--------------------------------------------------------------------------
            | Visibility
            |--------------------------------------------------------------------------
            */

            ->visible(
                function (
                    PurchaseRequisition $record
                ): bool {

                    if (
                        $record->status
                        !== PurchaseRequisition::STATUS_WAITING_APPROVAL
                    ) {
                        return false;
                    }

                    $transaction =
                        ApprovalTransaction::query()
                            ->with('steps')
                            ->where(
                                'document_type',
                                'MATERIAL_REQUISITION'
                            )
                            ->where(
                                'document_id',
                                $record->getKey()
                            )
                            ->latest('id')
                            ->first();

                    if (! $transaction) {
                        return false;
                    }

                    if (! $transaction->isPending()) {
                        return false;
                    }

                    $user = auth()->user();

                    if (! $user) {
                        return false;
                    }

                    return app(
                        ApprovalTransactionService::class
                    )->canApprove(
                        $transaction,
                        $user
                    );
                }
            )

            /*
            |--------------------------------------------------------------------------
            | Rejection Remarks
            |--------------------------------------------------------------------------
            */

            ->form([

                Textarea::make('remarks')
                    ->label('Rejection Remarks')
                    ->placeholder(
                        'Enter the reason for rejecting this Material Requisition...'
                    )
                    ->required()
                    ->minLength(3)
                    ->maxLength(1000)
                    ->rows(5),

            ])

            /*
            |--------------------------------------------------------------------------
            | Confirmation
            |--------------------------------------------------------------------------
            */

            ->requiresConfirmation()

            ->modalHeading(
                'Reject Material Requisition'
            )

            ->modalDescription(
                fn (
                    PurchaseRequisition $record
                ): string =>
                    "Reject Material Requisition {$record->pr_no}?"
            )

            ->modalSubmitActionLabel(
                'Reject'
            )

            /*
            |--------------------------------------------------------------------------
            | Execute
            |--------------------------------------------------------------------------
            */

            ->action(
                function (
                    PurchaseRequisition $record,
                    array $data
                ): void {

                    try {

                        /*
                        |--------------------------------------------------------------------------
                        | Resolve Latest Transaction
                        |--------------------------------------------------------------------------
                        */

                        $transaction =
                            ApprovalTransaction::query()
                                ->with([
                                    'approvalMaster',
                                    'steps',
                                ])
                                ->where(
                                    'document_type',
                                    'MATERIAL_REQUISITION'
                                )
                                ->where(
                                    'document_id',
                                    $record->getKey()
                                )
                                ->latest('id')
                                ->first();

                        if (! $transaction) {

                            throw new \RuntimeException(
                                "No approval transaction found for Material Requisition [{$record->pr_no}]."
                            );
                        }

                        /*
                        |--------------------------------------------------------------------------
                        | Authenticated User
                        |--------------------------------------------------------------------------
                        */

                        $user = auth()->user();

                        if (! $user) {

                            throw new \RuntimeException(
                                'Authenticated user not found.'
                            );
                        }

                        /*
                        |--------------------------------------------------------------------------
                        | Approval Service
                        |--------------------------------------------------------------------------
                        */

                        $service = app(
                            ApprovalTransactionService::class
                        );

                        /*
                        |--------------------------------------------------------------------------
                        | Security Validation
                        |--------------------------------------------------------------------------
                        */

                        if (
                            ! $service->canApprove(
                                $transaction,
                                $user
                            )
                        ) {

                            throw new \RuntimeException(
                                'You are not authorized to reject the current approval level.'
                            );
                        }

                        /*
                        |--------------------------------------------------------------------------
                        | Execute Reject
                        |--------------------------------------------------------------------------
                        */

                        $service->reject(
                            $transaction,
                            $user,
                            $data['remarks'] ?? null
                        );

                        /*
                        |--------------------------------------------------------------------------
                        | Success Notification
                        |--------------------------------------------------------------------------
                        */

                        Notification::make()
                            ->success()
                            ->title(
                                'Material Requisition Rejected'
                            )
                            ->body(
                                "Material Requisition {$record->pr_no} has been rejected."
                            )
                            ->send();

                    } catch (Throwable $exception) {

                        Notification::make()
                            ->danger()
                            ->title(
                                'Unable to reject Material Requisition'
                            )
                            ->body(
                                $exception->getMessage()
                            )
                            ->send();
                    }
                }
            );
    }
}