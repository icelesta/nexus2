<?php

declare(strict_types=1);

namespace App\Filament\Resources\DirectMarkets\Actions;

use App\Filament\Resources\DirectMarkets\DirectMarketResource;
use App\Models\ApprovalTransaction;
use App\Models\DirectMarket;
use App\Services\Approval\ApprovalTransactionService;
use Filament\Actions\Action;
use Filament\Notifications\Notification;
use Livewire\Component;
use Throwable;

class ApproveDirectMarket
{
    public static function make(): Action
    {
        return Action::make('approve')

            ->label('Approve')

            ->icon('heroicon-o-check-circle')

            ->color('success')

            ->visible(
                function (
                    DirectMarket $record
                ): bool {

                    if (
                        $record->status
                        !== DirectMarket::STATUS_SUBMITTED
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
                                'DIRECT_MARKET'
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
                'Approve Direct Market'
            )

            ->modalDescription(
                fn (
                    DirectMarket $record
                ): string =>
                    "Approve Direct Market {$record->dm_no}?"
            )

            ->modalSubmitActionLabel(
                'Approve'
            )

            ->action(
                function (
                    DirectMarket $record,
                    Component $livewire
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
                                    'DIRECT_MARKET'
                                )
                                ->where(
                                    'document_id',
                                    $record->getKey()
                                )
                                ->latest('id')
                                ->first();

                        if (! $transaction) {
                            throw new \RuntimeException(
                                "No approval transaction found for Direct Market [{$record->dm_no}]."
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
                                'Approved from Direct Market UI.'
                            );

                        if (
                            $transaction->status
                            === 'APPROVED'
                        ) {

                            Notification::make()
                                ->success()
                                ->title(
                                    'Direct Market Approved'
                                )
                                ->body(
                                    "Direct Market {$record->dm_no} has completed the approval workflow."
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
                                    "Direct Market {$record->dm_no} is now waiting for Level {$nextLevel} — {$nextRole}."
                                )
                                ->send();
                        }

                        /*
                        |--------------------------------------------------------------------------
                        | Redirect to Direct Market List
                        |--------------------------------------------------------------------------
                        */

                        $livewire->redirect(
                            DirectMarketResource::getUrl('index')
                        );

                    } catch (Throwable $exception) {

                        Notification::make()
                            ->danger()
                            ->title(
                                'Unable to approve Direct Market'
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