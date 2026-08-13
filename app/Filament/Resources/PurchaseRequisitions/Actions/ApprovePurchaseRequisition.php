<?php

declare(strict_types=1);

namespace App\Filament\Resources\PurchaseRequisitions\Actions;

use App\Models\ApprovalTransaction;
use App\Models\PurchaseRequisition;
use App\Services\Approval\ApprovalTransactionService;
use Filament\Actions\Action;
use Filament\Notifications\Notification;
use Throwable;

class ApprovePurchaseRequisition
{
    public static function make(): Action
    {
        return Action::make('approve')

            /*
            |--------------------------------------------------------------------------
            | Label
            |--------------------------------------------------------------------------
            */

            ->label('Approve')

            ->icon('heroicon-o-check-circle')

            ->color('success')

            /*
            |--------------------------------------------------------------------------
            | Visibility
            |--------------------------------------------------------------------------
            |
            | Approval button is visible only when:
            |
            | 1. MR is Pending Approval
            | 2. Approval Transaction exists
            | 3. Transaction is PENDING
            | 4. Current user is authorized for current level
            |
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
            | Confirmation
            |--------------------------------------------------------------------------
            */

            ->requiresConfirmation()

            ->modalHeading(
                'Approve Material Requisition'
            )

            ->modalDescription(
                fn (
                    PurchaseRequisition $record
                ): string =>
                    "Approve Material Requisition {$record->pr_no}?"
            )

            ->modalSubmitActionLabel(
                'Approve'
            )

            /*
            |--------------------------------------------------------------------------
            | Execute Approval
            |--------------------------------------------------------------------------
            */

            ->action(
                function (
                    PurchaseRequisition $record
                ): void {

                    try {

                        /*
                        |--------------------------------------------------------------------------
                        | Resolve Latest Approval Transaction
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
                        |
                        | Visibility is NOT the security boundary.
                        |
                        | Always validate again immediately before
                        | executing the approval.
                        |
                        */

                        if (
                            ! $service->canApprove(
                                $transaction,
                                $user
                            )
                        ) {

                            throw new \RuntimeException(
                                'You are not authorized to approve the current approval level.'
                            );
                        }

                        /*
                        |--------------------------------------------------------------------------
                        | Capture Current Level
                        |--------------------------------------------------------------------------
                        */

                        $currentLevel =
                            (int) $transaction->current_level;

                        /*
                        |--------------------------------------------------------------------------
                        | Execute Approval
                        |--------------------------------------------------------------------------
                        */

                        $transaction =
                            $service->approve(
                                $transaction,
                                $user,
                                'Approved from Material Requisition UI.'
                            );

                        /*
                        |--------------------------------------------------------------------------
                        | Determine Result
                        |--------------------------------------------------------------------------
                        */

                        if (
                            $transaction->status
                            === 'APPROVED'
                        ) {

                            /*
                            |--------------------------------------------------------------------------
                            | Final Approval
                            |--------------------------------------------------------------------------
                            */

                            Notification::make()
                                ->success()
                                ->title(
                                    'Material Requisition Approved'
                                )
                                ->body(
                                    "Material Requisition {$record->pr_no} has completed the approval workflow."
                                )
                                ->send();

                        } else {

                            /*
                            |--------------------------------------------------------------------------
                            | Intermediate Approval
                            |--------------------------------------------------------------------------
                            */

                            $nextLevel =
                                (int) $transaction->current_level;

                            $nextStep =
                                $transaction
                                    ->steps
                                    ->firstWhere(
                                        'approval_level',
                                        $nextLevel
                                    );

                            $nextRole =
                                $nextStep?->role_name
                                ?? 'Next Approver';

                            Notification::make()
                                ->success()
                                ->title(
                                    "Level {$currentLevel} Approved"
                                )
                                ->body(
                                    "Material Requisition {$record->pr_no} is now waiting for Level {$nextLevel} — {$nextRole}."
                                )
                                ->send();
                        }

                    } catch (Throwable $exception) {

                        Notification::make()
                            ->danger()
                            ->title(
                                'Unable to approve Material Requisition'
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