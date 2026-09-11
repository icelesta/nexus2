<?php

declare(strict_types=1);

namespace App\Filament\Resources\AssignmentDirectMarketResource\Actions;

use App\Models\ApprovalTransaction;
use App\Models\AssignmentDirectMarket;
use App\Services\Approval\ApprovalTransactionService;
use Filament\Actions\Action;
use Filament\Forms\Components\Textarea;
use Filament\Notifications\Notification;
use Livewire\Component;
use Throwable;

class RejectAssignmentDirectMarket
{
    public static function make(): Action
    {
        return Action::make('reject')

            ->label('Reject')

            ->icon('heroicon-o-x-circle')

            ->color('danger')

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
                            ->with('steps')
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

            ->form([

                Textarea::make('remarks')
                    ->label('Rejection Remarks')
                    ->placeholder(
                        'Enter the reason for rejecting this Assignment Direct Market...'
                    )
                    ->required()
                    ->minLength(3)
                    ->maxLength(1000)
                    ->rows(5),

            ])

            ->requiresConfirmation()

            ->modalHeading(
                'Reject Assignment Direct Market'
            )

            ->modalDescription(
                fn (
                    AssignmentDirectMarket $record
                ): string =>
                    "Reject Assignment Direct Market {$record->document_no}?"
            )

            ->modalSubmitActionLabel(
                'Reject'
            )

            ->action(
                function (
                    AssignmentDirectMarket $record,
                    array $data,
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
                                'You are not authorized to reject the current approval level.'
                            );
                        }

                        $service->reject(
                            $transaction,
                            $user,
                            $data['remarks'] ?? null
                        );

                        $record->update([
                            'status' =>
                                AssignmentDirectMarket::STATUS_REJECTED,
                            'updated_by' =>
                                $user->getKey(),
                        ]);

                        Notification::make()
                            ->success()
                            ->title(
                                'Assignment Direct Market Rejected'
                            )
                            ->body(
                                "Assignment Direct Market {$record->document_no} has been rejected."
                            )
                            ->send();

                        /*
                        |--------------------------------------------------------------------------
                        | AUTO REFRESH CURRENT PAGE
                        |--------------------------------------------------------------------------
                        |
                        | Refresh the record owned by the current
                        | Assignment Direct Market View page after
                        | a successful rejection.
                        |
                        | Rejection business logic remains unchanged.
                        |
                        |--------------------------------------------------------------------------
                        */

                        $livewire->getRecord()->refresh();

                    } catch (Throwable $exception) {

                        Notification::make()
                            ->danger()
                            ->title(
                                'Unable to reject Assignment Direct Market'
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