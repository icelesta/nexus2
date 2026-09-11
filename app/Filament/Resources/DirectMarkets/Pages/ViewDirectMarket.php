<?php

declare(strict_types=1);

namespace App\Filament\Resources\DirectMarkets\Pages;

use App\Filament\Resources\DirectMarkets\Actions\ApproveDirectMarket;
use App\Filament\Resources\DirectMarkets\Actions\RejectDirectMarket;
use App\Filament\Resources\DirectMarkets\DirectMarketResource;
use App\Models\ApprovalTransaction;
use App\Models\AssignmentDirectMarket;
use App\Models\DirectMarket;
use Filament\Actions\EditAction;
use Filament\Resources\Pages\ViewRecord;
use Filament\Schemas\Components\View;
use Filament\Schemas\Schema;

class ViewDirectMarket extends ViewRecord
{
    protected static string $resource =
        DirectMarketResource::class;

    protected function getHeaderActions(): array
    {
        return [

            /*
            |--------------------------------------------------------------------------
            | APPROVE DIRECT MARKET
            |--------------------------------------------------------------------------
            */

            ApproveDirectMarket::make(),

            /*
            |--------------------------------------------------------------------------
            | REJECT DIRECT MARKET
            |--------------------------------------------------------------------------
            */

            RejectDirectMarket::make(),

        ];
    }

    /**
     * Get the latest Approval Transaction relevant to this Direct Market.
     *
     * Once an Assignment Direct Market exists, its approval transaction
     * becomes the authoritative approval history because it contains the
     * inherited Level 1 snapshot and the ADM Level 2 approval step.
     *
     * If no Assignment Direct Market exists yet, fall back to the
     * Direct Market approval transaction.
     */
    protected function getApprovalTransaction(): ?ApprovalTransaction
    {
        $assignment = AssignmentDirectMarket::query()
            ->where(
                'direct_market_id',
                $this->record->getKey()
            )
            ->latest('id')
            ->first();

        if ($assignment) {
            $assignmentTransaction = ApprovalTransaction::query()
                ->with([
                    'steps' => fn ($query) =>
                        $query
                            ->with('approver')
                            ->orderBy('approval_level'),
                ])
                ->where(
                    'document_type',
                    'ASSIGNMENT_DIRECT_MARKET'
                )
                ->where(
                    'document_id',
                    $assignment->getKey()
                )
                ->latest('id')
                ->first();

            if ($assignmentTransaction) {
                return $assignmentTransaction;
            }
        }

        return ApprovalTransaction::query()
            ->with([
                'steps' => fn ($query) =>
                    $query
                        ->with('approver')
                        ->orderBy('approval_level'),
            ])
            ->where(
                'document_type',
                'DIRECT_MARKET'
            )
            ->where(
                'document_id',
                $this->record->getKey()
            )
            ->latest('id')
            ->first();
    }

    /**
     * Get the rejected approval step.
     */
    protected function getRejectedApprovalStep()
    {
        $transaction = $this->getApprovalTransaction();

        if (! $transaction) {
            return null;
        }

        return $transaction->steps
            ->where('status', 'REJECTED')
            ->sortByDesc('acted_at')
            ->first();
    }

    public function content(
        Schema $schema
    ): Schema {

        return $schema

            ->columns(1)

            ->components(
                fn (): array => [

                    /*
                    |--------------------------------------------------------------------------
                    | REJECTION ALERT
                    |--------------------------------------------------------------------------
                    */

                    View::make(
                        'filament.resources.direct-markets.pages.partials.rejection-alert'
                    )
                        ->viewData(
                            fn (): array => [
                                'rejectionStep' =>
                                    $this->getRejectedApprovalStep(),
                            ]
                        )
                        ->visible(
                            fn (): bool =>
                                $this->getRejectedApprovalStep() !== null
                        )
                        ->columnSpanFull(),

                    /*
                    |--------------------------------------------------------------------------
                    | DIRECT MARKET INFORMATION
                    |--------------------------------------------------------------------------
                    */

                    $this->getInfolistContentComponent(),

                    /*
                    |--------------------------------------------------------------------------
                    | APPROVAL HISTORY
                    |--------------------------------------------------------------------------
                    |
                    | Dynamic data source:
                    |
                    | - Before ADM exists:
                    |     DIRECT_MARKET transaction
                    |
                    | - After ADM exists:
                    |     ASSIGNMENT_DIRECT_MARKET transaction
                    |
                    | The ADM transaction contains:
                    |     Level 1 snapshot
                    |     Level 2 PENDING / APPROVED / REJECTED
                    |
                    */

                    View::make(
                        'filament.resources.direct-markets.pages.partials.approval-history'
                    )
                        ->viewData(
                            fn (): array => [
                                'steps' =>
                                    $this
                                        ->getApprovalTransaction()
                                        ?->steps
                                        ?? collect(),
                            ]
                        )
                        ->visible(
                            fn (): bool =>
                                $this->getApprovalTransaction() !== null
                        )
                        ->columnSpanFull(),

                ]
            );
    }
}