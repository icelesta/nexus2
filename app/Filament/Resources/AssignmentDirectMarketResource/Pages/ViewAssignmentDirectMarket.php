<?php

declare(strict_types=1);

namespace App\Filament\Resources\AssignmentDirectMarketResource\Pages;

use App\Filament\Resources\AssignmentDirectMarketResource\AssignmentDirectMarketResource;
use App\Livewire\Purchasing\AssignmentDirectMarketItemsGrid;
use App\Models\ApprovalTransaction;

use Filament\Actions\EditAction;
use Filament\Resources\Pages\ViewRecord;
use Filament\Schemas\Components\Livewire;
use Filament\Schemas\Components\View;
use Filament\Schemas\Schema;

use App\Filament\Resources\AssignmentDirectMarketResource\Actions\ApproveAssignmentDirectMarket;
use App\Filament\Resources\AssignmentDirectMarketResource\Actions\RejectAssignmentDirectMarket;

class ViewAssignmentDirectMarket extends ViewRecord
{
    protected static string $resource =
        AssignmentDirectMarketResource::class;

    /*
    |--------------------------------------------------------------------------
    | PAGE BACKGROUND
    |--------------------------------------------------------------------------
    |
    | Match the View page background with the Edit Assignment Direct Market
    | page without changing any functional behavior.
    |
    */

    public function getExtraBodyAttributes(): array
    {
        return [
            'class' => 'bg-gray-50',
        ];
    }

    protected function getHeaderActions(): array
    {
        return [

            ApproveAssignmentDirectMarket::make(),

            RejectAssignmentDirectMarket::make(),

            EditAction::make()
                ->authorize(
                    fn (): bool =>
                        auth()->user()->can(
                            'update',
                            $this->record
                        )
                ),

        ];
    }

    /**
     * Get the latest Approval Transaction for this Assignment Direct Market.
     */
    protected function getApprovalTransaction(): ?ApprovalTransaction
    {
        return ApprovalTransaction::query()
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
                    | ERP DOCUMENT HEADER
                    |--------------------------------------------------------------------------
                    */

                    View::make(
                        'filament.resources.assignment-direct-markets.pages.partials.document-header'
                    )
                        ->viewData([
                            'record' => $this->record,
                        ])
                        ->columnSpanFull(),


                    /*
                    |--------------------------------------------------------------------------
                    | REJECTION INFORMATION
                    |--------------------------------------------------------------------------
                    |
                    | Re-evaluated whenever the page schema is rendered so the
                    | latest rejection state is immediately reflected.
                    |
                    */

                    View::make(
                        'filament.resources.assignment-direct-markets.pages.partials.rejection-alert'
                    )
                        ->viewData(
                            fn (): array => [
                                'record' =>
                                    $this->record,

                                'rejectionStep' =>
                                    $this->getRejectedApprovalStep(),
                            ]
                        )
                        ->visible(
                            fn (): bool =>
                                $this->record->status
                                === \App\Models\AssignmentDirectMarket::STATUS_REJECTED
                                && $this->getRejectedApprovalStep() !== null
                        )
                        ->columnSpanFull(),


                    /*
                    |--------------------------------------------------------------------------
                    | ADM INFORMATION
                    |--------------------------------------------------------------------------
                    */

                    $this->getInfolistContentComponent(),


                    /*
                    |--------------------------------------------------------------------------
                    | ADM ITEMS
                    |--------------------------------------------------------------------------
                    */

                    Livewire::make(
                        AssignmentDirectMarketItemsGrid::class
                    )
                        ->key(
                            'assignment-direct-market-items-grid'
                        )
                        ->data([
                            'assignment' =>
                                $this->getRecord(),

                            'readonly' =>
                                true,
                        ])
                        ->columnSpanFull(),


                    /*
                    |--------------------------------------------------------------------------
                    | APPROVAL HISTORY
                    |--------------------------------------------------------------------------
                    |
                    | IMPORTANT:
                    | The latest Approval Transaction is queried again whenever
                    | the page schema is rendered.
                    |
                    | This allows Level 1 / Level 2 APPROVED or REJECTED state,
                    | approver, acted_at, and remarks to appear immediately
                    | after the approval action without browser refresh.
                    |
                    */

                    View::make(
                        'filament.resources.assignment-direct-markets.pages.partials.approval-history'
                    )
                        ->viewData(
                            fn (): array => [
                                'transaction' =>
                                    $this->getApprovalTransaction(),
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