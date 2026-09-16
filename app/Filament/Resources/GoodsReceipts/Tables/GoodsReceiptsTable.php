<?php

declare(strict_types=1);

namespace App\Filament\Resources\GoodsReceipts\Tables;

use App\Filament\Resources\GoodsReceipts\Pages\ListGoodsReceipts;
use Filament\Actions\ActionGroup;
use Filament\Actions\ViewAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class GoodsReceiptsTable
{
    public static function configure(
        Table $table
    ): Table {

        return $table

            /*
            |--------------------------------------------------------------------------
            | GLOBAL TRANSACTION FILTER
            |--------------------------------------------------------------------------
            */

            ->header(
                fn ($livewire) => view(
                    'filament.components.global-transaction-filters',
                    [
                        'livewire' => $livewire,
                    ]
                )
            )

            /*
            |--------------------------------------------------------------------------
            | GLOBAL TRANSACTION FILTER QUERY — RR
            |--------------------------------------------------------------------------
            |
            | Visibility:
            | - All authenticated users see all RR by default.
            | - No automatic Department restriction.
            |
            | Manual GTF:
            | - Branch      -> Purchase Order
            | - Department  -> Purchase Order
            | - Supplier    -> Goods Receipt
            | - Status      -> Goods Receipt
            | - From        -> Receipt Date
            | - To          -> Receipt Date
            |
            */

            ->modifyQueryUsing(
                function (
                    Builder $query,
                    ListGoodsReceipts $livewire,
                ): Builder {

                    /*
                    |--------------------------------------------------------------------------
                    | GTF ACCESS
                    |--------------------------------------------------------------------------
                    */

                    if (! $livewire->canUseGlobalTransactionFilters()) {
                        return $query;
                    }

                    /*
                    |--------------------------------------------------------------------------
                    | BRANCH
                    |--------------------------------------------------------------------------
                    |
                    | Goods Receipt does not have branch_id.
                    | Branch is inherited from the related Purchase Order.
                    |
                    */

                    if ($livewire->globalBranchFilter !== null) {
                        $query->whereHas(
                            'purchaseOrder',
                            fn (Builder $q) => $q->where(
                                'branch_id',
                                $livewire->globalBranchFilter
                            )
                        );
                    }

                    /*
                    |--------------------------------------------------------------------------
                    | DEPARTMENT
                    |--------------------------------------------------------------------------
                    |
                    | Goods Receipt does not have department_id.
                    | Department is inherited from the related Purchase Order.
                    |
                    */

                    if ($livewire->globalDepartmentFilter !== null) {
                        $query->whereHas(
                            'purchaseOrder',
                            fn (Builder $q) => $q->where(
                                'department_id',
                                $livewire->globalDepartmentFilter
                            )
                        );
                    }

                    /*
                    |--------------------------------------------------------------------------
                    | SUPPLIER
                    |--------------------------------------------------------------------------
                    */

                    if ($livewire->globalSupplierFilter !== null) {
                        $query->where(
                            'goods_receipts.supplier_id',
                            $livewire->globalSupplierFilter
                        );
                    }

                    /*
                    |--------------------------------------------------------------------------
                    | STATUS
                    |--------------------------------------------------------------------------
                    */

                    if (filled($livewire->globalStatusFilter)) {
                        $query->where(
                            'goods_receipts.status',
                            $livewire->globalStatusFilter
                        );
                    }

                    /*
                    |--------------------------------------------------------------------------
                    | RECEIPT DATE FROM
                    |--------------------------------------------------------------------------
                    |
                    | receipt_date is a DATE column.
                    | No timezone conversion is required.
                    |
                    */

                    if (filled($livewire->globalDateFrom)) {
                        $query->whereDate(
                            'goods_receipts.receipt_date',
                            '>=',
                            $livewire->globalDateFrom
                        );
                    }

                    /*
                    |--------------------------------------------------------------------------
                    | RECEIPT DATE TO
                    |--------------------------------------------------------------------------
                    */

                    if (filled($livewire->globalDateTo)) {
                        $query->whereDate(
                            'goods_receipts.receipt_date',
                            '<=',
                            $livewire->globalDateTo
                        );
                    }

                    return $query;
                }
            )

            /*
            |--------------------------------------------------------------------------
            | COLUMNS
            |--------------------------------------------------------------------------
            */

            ->columns([

                TextColumn::make('grn_no')
                    ->label('GRN No')
                    ->searchable()
                    ->sortable(),

                TextColumn::make(
                    'assignmentDirectMarket.document_no'
                )
                    ->label('ADM No')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('supplier.name')
                    ->label('Supplier')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('receipt_date')
                    ->label('Receipt Date')
                    ->date()
                    ->sortable(),

                TextColumn::make('status')
                    ->label('Status')
                    ->badge(),

            ])

            /*
            |--------------------------------------------------------------------------
            | RECORD ACTIONS
            |--------------------------------------------------------------------------
            */

            ->recordActions([

                ActionGroup::make([

                    ViewAction::make()
                        ->label('View'),

                ])
                    ->label('Actions')
                    ->button(),

            ]);
    }
}