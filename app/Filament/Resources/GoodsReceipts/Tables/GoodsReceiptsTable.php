<?php

declare(strict_types=1);

namespace App\Filament\Resources\GoodsReceipts\Tables;

use App\Filament\Resources\GoodsReceipts\Pages\ListGoodsReceipts;
use App\Models\GoodsReceipt;
use Filament\Actions\ActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\EditAction;
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

            ->defaultSort('created_at', 'desc')

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
            */

            ->modifyQueryUsing(
                function (
                    Builder $query,
                    ListGoodsReceipts $livewire,
                ): Builder {

                    if (! $livewire->canUseGlobalTransactionFilters()) {
                        return $query;
                    }

                    /*
                    |--------------------------------------------------------------------------
                    | BRANCH
                    |--------------------------------------------------------------------------
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

                /*
                |--------------------------------------------------------------------------
                | JOB RR NO.
                |--------------------------------------------------------------------------
                */

                TextColumn::make('grn_no')
                    ->label('RR No.')
                    ->searchable()
                    ->sortable(),

                /*
                |--------------------------------------------------------------------------
                | SOURCE TYPE
                |--------------------------------------------------------------------------
                */

                TextColumn::make('source_type')
                    ->label('Source Type')
                    ->state(
                        function (GoodsReceipt $record): string {
                            if ($record->purchase_order_id !== null) {
                                return 'Purchase Order';
                            }

                            if ($record->assignment_direct_market_id !== null) {
                                return 'Direct Market';
                            }

                            return '-';
                        }
                    )
                    ->color(
                        fn ($state): string => match ($state) {
                            'Purchase Order' => 'info',
                            'Direct Market' => 'primary',
                            default => 'gray',
                        }
                    )
                    ->badge()
                    ->searchable(false)
                    ->sortable(false),

                /*
                |--------------------------------------------------------------------------
                | SOURCE NO.
                |--------------------------------------------------------------------------
                |
                | PO  -> PurchaseOrder.document_no
                | DM  -> AssignmentDirectMarket.document_no
                |
                */

                TextColumn::make('source_no')
                    ->label('Source No.')
                    ->state(
                        function (GoodsReceipt $record): string {
                            if ($record->purchaseOrder) {
                                return $record->purchaseOrder->document_no ?? '-';
                            }

                            if ($record->assignmentDirectMarket) {
                                return $record->assignmentDirectMarket->directMarket?->dm_no ?? '-';
                            }

                            return '-';
                        }
                    )
                    ->searchable(false)
                    ->sortable(false),

                /*
                |--------------------------------------------------------------------------
                | SUPPLIER
                |--------------------------------------------------------------------------
                */

                TextColumn::make('supplier.supplier_name')
                    ->label('Supplier')
                    ->formatStateUsing(
                        function ($state, GoodsReceipt $record): string {
                            if (! $record->supplier) {
                                return '-';
                            }

                            return $record->supplier->supplier_name
                                ? "{$record->supplier->supplier_code} - {$record->supplier->supplier_name}"
                                : $record->supplier->supplier_code;
                        }
                    )
                    ->searchable()
                    ->sortable(),

                /*
                |--------------------------------------------------------------------------
                | RECEIPT DATE
                |--------------------------------------------------------------------------
                */

                TextColumn::make('receipt_date')
                    ->label('Receipt Date')
                    ->date()
                    ->sortable(),

                /*
                |--------------------------------------------------------------------------
                | STATUS
                |--------------------------------------------------------------------------
                */


                /*
                |--------------------------------------------------------------------------
                | ITEMS STATUS
                |--------------------------------------------------------------------------
                */

                TextColumn::make('items_status')
                    ->label('Items Status')
                    ->badge()
                    ->color(fn (?string $state): string => match ($state) {
                        'DRAFT' => 'gray',
                        'PARTIAL RECEIVED' => 'warning',
                        'COMPLETED' => 'success',
                        default => 'gray',
                    }),

                /*
                |--------------------------------------------------------------------------
                | RR STATUS
                |--------------------------------------------------------------------------
                */

                TextColumn::make('rr_status')
                    ->label('RR Status')
                    ->badge()
                    ->color(fn (?string $state): string => match ($state) {
                        'DRAFT' => 'gray',
                        'RECEIVED' => 'warning',
                        'CLOSE' => 'success',
                        default => 'gray',
                    }),

                /*
                |--------------------------------------------------------------------------
                | REMARK
                |--------------------------------------------------------------------------
                */

                TextColumn::make('remarks')
                    ->label('Remark')
                    ->limit(40)
                    ->tooltip(
                        fn (?string $state): ?string => filled($state)
                            ? $state
                            : null
                    )
                    ->placeholder('-'),

            ])

            /*
            |--------------------------------------------------------------------------
            | RECORD ACTIONS
            |--------------------------------------------------------------------------
            */


            ->recordActions([

                ActionGroup::make([

                    /*
                    |--------------------------------------------------------------------------
                    | VIEW
                    |--------------------------------------------------------------------------
                    */

                    ViewAction::make()
                        ->label('View'),

                    /*
                    |--------------------------------------------------------------------------
                    | EDIT
                    |--------------------------------------------------------------------------
                    |
                    | DRAFT + DRAFT              : Editable
                    | PARTIAL RECEIVED + RECEIVED: Editable
                    | RECEIVED + CLOSE           : LOCK
                    |
                    */

                    EditAction::make()
                        ->label(
                            fn (GoodsReceipt $record): string =>
                                (
                                    $record->items_status === 'RECEIVED'
                                    && $record->rr_status === 'CLOSE'
                                )
                                    ? 'LOCK'
                                    : 'Edit'
                        )
                        ->icon(
                            fn (GoodsReceipt $record): string =>
                                (
                                    $record->items_status === 'RECEIVED'
                                    && $record->rr_status === 'CLOSE'
                                )
                                    ? 'heroicon-o-lock-closed'
                                    : 'heroicon-o-pencil-square'
                        )
                        ->disabled(
                            fn (GoodsReceipt $record): bool =>
                                ! in_array(
                                    [$record->items_status, $record->rr_status],
                                    [
                                        ['DRAFT', 'DRAFT'],
                                        ['PARTIAL RECEIVED', 'RECEIVED'],
                                    ],
                                    true
                                )
                        )
                        ->visible(
                            fn (): bool =>
                                auth()->user()?->can('Update:GoodsReceipt') === true
                        ),

                    /*
                    |--------------------------------------------------------------------------
                    | DELETE
                    |--------------------------------------------------------------------------
                    |
                    | DRAFT + DRAFT : Delete allowed
                    | Other statuses: LOCK
                    |
                    */

                    DeleteAction::make()
                        ->label(
                            fn (GoodsReceipt $record): string =>
                                (
                                    $record->items_status === 'DRAFT'
                                    && $record->rr_status === 'DRAFT'
                                )
                                    ? 'Delete'
                                    : 'LOCK'
                        )
                        ->icon(
                            fn (GoodsReceipt $record): string =>
                                (
                                    $record->items_status === 'DRAFT'
                                    && $record->rr_status === 'DRAFT'
                                )
                                    ? 'heroicon-o-trash'
                                    : 'heroicon-o-lock-closed'
                        )
                        ->disabled(
                            fn (GoodsReceipt $record): bool =>
                                ! (
                                    $record->items_status === 'DRAFT'
                                    && $record->rr_status === 'DRAFT'
                                )
                        )
                        ->visible(
                            fn (): bool =>
                                auth()->user()?->can('Delete:GoodsReceipt') === true
                        ),

                ])
                    ->label('Actions')
                    ->button()
                    ->color('warning'),

            ]);

    }
}