<?php

declare(strict_types=1);

namespace App\Filament\Resources\DirectMarkets\Actions;

use App\Models\ApprovalTransaction;
use App\Models\DirectMarket;
use App\Services\Approval\ApprovalTransactionService;
use Filament\Actions\Action;
use Filament\Forms\Components\Textarea;
use Filament\Notifications\Notification;
use Throwable;

class RejectDirectMarket
{
    public static function make(): Action
    {
        return Action::make('reject')

            ->label('Reject')

            ->icon('heroicon-o-x-circle')

            ->color('danger')

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
                            ->with('steps')
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

            ->form([

                Textarea::make('remarks')
                    ->label('Rejection Remarks')
                    ->placeholder(
                        'Enter the reason for rejecting this Direct Market...'
                    )
                    ->required()
                    ->minLength(3)
                    ->maxLength(1000)
                    ->rows(5),

            ])

            ->requiresConfirmation()

            ->modalHeading(
                'Reject Direct Market'
            )

            ->modalDescription(
                fn (
                    DirectMarket $record
                ): string =>
                    "Reject Direct Market {$record->dm_no}?"
            )

            ->modalSubmitActionLabel(
                'Reject'
            )

            ->action(
                function (
                    DirectMarket $record,
                    array $data
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
                                'You are not authorized to reject the current approval level.'
                            );
                        }

                        $service->reject(
                            $transaction,
                            $user,
                            $data['remarks'] ?? null
                        );

                        Notification::make()
                            ->success()
                            ->title(
                                'Direct Market Rejected'
                            )
                            ->body(
                                "Direct Market {$record->dm_no} has been rejected."
                            )
                            ->send();

                    } catch (Throwable $exception) {

                        Notification::make()
                            ->danger()
                            ->title(
                                'Unable to reject Direct Market'
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
