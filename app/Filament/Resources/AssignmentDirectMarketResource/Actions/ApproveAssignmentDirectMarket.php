<?php

declare(strict_types=1);

namespace App\Filament\Resources\AssignmentDirectMarketResource\Actions;

use App\Models\ApprovalTransaction;
use App\Models\AssignmentDirectMarket;
use App\Services\Approval\ApprovalTransactionService;
use Filament\Actions\Action;
use Filament\Notifications\Notification;
use Throwable;

class ApproveAssignmentDirectMarket
{
    public static function make(): Action
    {
        return Action::make('approve')

            ->label('Approve')

            ->icon('heroicon-o-check-circle')

            ->color('success')

            ->visible(
                function (
                    AssignmentDirectMarket $record
                ): bool {

                    if (
                        $record->status
                        !== AssignmentDirectMarket::STATUS_WAITING_APPROVAL
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
                                'ASSIGNMENT_DIRECT_MARKET'
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

            ->requiresConfirmation()

            ->modalHeading(
                'Approve Assignment Direct Market'
            )

            ->modalDescription(
                fn (
                    AssignmentDirectMarket $record
                ): string =>
                    "Approve Assignment Direct Market {$record->document_no}?"
            )

            ->modalSubmitActionLabel(
                'Approve'
            )

            ->action(
                function (
                    AssignmentDirectMarket $record
                ): void {

                    try {

                        $transaction =
                            ApprovalTransaction::query()
                                ->with([
                                    'approvalMaster',
                                    'steps',
                                ])
                                ->where(
                                    'document_type',
                                    'ASSIGNMENT_DIRECT_MARKET'
                                )
                                ->where(
                                    'document_id',
                                    $record->getKey()
                                )
                                ->latest('id')
                                ->first();

                        if (! $transaction) {
                            throw new \RuntimeException(
                                "No approval transaction found for Assignment Direct Market [{$record->document_no}]."
                            );
                        }

                        $user = auth()->user();

                        if (! $user) {
                            throw new \RuntimeException(
                                'Authenticated user not found.'
                            );
                        }

                        $service = app(
                            ApprovalTransactionService::class
                        );

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

                        $currentLevel =
                            (int) $transaction->current_level;

                        $transaction =
                            $service->approve(
                                $transaction,
                                $user,
                                'Approved from Assignment Direct Market UI.'
                            );

                        if (
                            $transaction->status
                            === 'APPROVED'
                        ) {

                            $record->update([
                                'status' =>
                                    AssignmentDirectMarket::STATUS_APPROVED,
                                'updated_by' =>
                                    $user->getKey(),
                            ]);

                            Notification::make()
                                ->success()
                                ->title(
                                    'Assignment Direct Market Approved'
                                )
                                ->body(
                                    "Assignment Direct Market {$record->document_no} has completed the approval workflow."
                                )
                                ->send();

                        } else {

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
                                    "Assignment Direct Market {$record->document_no} is now waiting for Level {$nextLevel} — {$nextRole}."
                                )
                                ->send();
                        }

                    } catch (Throwable $exception) {

                        Notification::make()
                            ->danger()
                            ->title(
                                'Unable to approve Assignment Direct Market'
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