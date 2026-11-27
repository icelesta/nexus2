<?php

declare(strict_types=1);

namespace App\Filament\Resources\PurchaseOrderResource\Tables;

use App\Filament\Resources\PurchaseOrderResource\Pages\ListPurchaseOrders;
use App\Models\PurchaseOrder;

use Filament\Actions\Action;
use Filament\Actions\ActionGroup;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;

use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use App\Support\Timezone\UserTimezone;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

use Illuminate\Database\Eloquent\Builder;

class PurchaseOrdersTable
{
    public static function configure(
        Table $table,
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
            | QUERY BINDING — GTF-1.3
            |--------------------------------------------------------------------------
            |
            | PO Global Filter:
            |
            | Branch      -> pending relationship verification
            | Department  -> pending relationship verification
            | Status      -> approval_status
            | From        -> document_date
            | To          -> document_date
            |
            */

            ->modifyQueryUsing(
                function (
                    Builder $query,
                    ListPurchaseOrders $livewire,
                ): Builder {

                    /*
                    |--------------------------------------------------------------------------
                    | GTF ACCESS
                    |--------------------------------------------------------------------------
                    */

                    if (
                        ! $livewire->canUseGlobalTransactionFilters()
                    ) {
                        return $query;
                    }


                    /*
                    |--------------------------------------------------------------------------
                    | PO APPROVAL STATUS
                    |--------------------------------------------------------------------------
                    |
                    | IMPORTANT:
                    |
                    | User requested PO Global Status to represent
                    | Approval Status, NOT document status.
                    |
                    */

                    if (
                        filled($livewire->globalStatusFilter)
                    ) {

                        $query->where(
                            'purchase_orders.approval_status',
                            $livewire->globalStatusFilter
                        );

                    }


                    /*
                    |--------------------------------------------------------------------------
                    | PO DATE FROM
                    |--------------------------------------------------------------------------
                    */

                    if (
                        filled($livewire->globalDateFrom)
                    ) {

                        $from = Carbon::parse(
                            $livewire->globalDateFrom,
                            UserTimezone::timezone()
                        )->startOfDay()->utc();

                        $query->where(
                            'purchase_orders.document_date',
                            '>=',
                            $from
                        );

                    }


                    /*
                    |--------------------------------------------------------------------------
                    | PO DATE TO
                    |--------------------------------------------------------------------------
                    */

                    if (
                        filled($livewire->globalDateTo)
                    ) {

                        $to = Carbon::parse(
                            $livewire->globalDateTo,
                            UserTimezone::timezone()
                        )->endOfDay()->utc();

                        $query->where(
                            'purchase_orders.document_date',
                            '<=',
                            $to
                        );

                    }

                    /*
                    |--------------------------------------------------------------------------
                    | BRANCH
                    |--------------------------------------------------------------------------
                    */

                    if ($livewire->globalBranchFilter !== null) {

                        $query->where(
                            'purchase_orders.branch_id',
                            $livewire->globalBranchFilter
                        );

                    }


                    /*
                    |--------------------------------------------------------------------------
                    | DEPARTMENT
                    |--------------------------------------------------------------------------
                    */

                    if ($livewire->globalDepartmentFilter !== null) {

                        $query->where(
                            'purchase_orders.department_id',
                            $livewire->globalDepartmentFilter
                        );

                    }

                    /*
                    |--------------------------------------------------------------------------
                    | SUPPLIER
                    |--------------------------------------------------------------------------
                    */

                    if ($livewire->globalSupplierFilter !== null) {

                        $query->where(
                            'purchase_orders.supplier_id',
                            $livewire->globalSupplierFilter
                        );

                    }

                    /*
                    |--------------------------------------------------------------------------
                    | BRANCH / DEPARTMENT
                    |--------------------------------------------------------------------------
                    |
                    | DO NOT apply these yet.
                    |
                    | Current PurchaseOrdersTable source does not expose
                    | a Branch or Department relationship/column.
                    |
                    | We will bind these after verifying the actual
                    | PurchaseOrder model relationship.
                    |
                    */

                    return $query;
                }
            )

            /*
            |--------------------------------------------------------------------------
            | COLUMNS
            |--------------------------------------------------------------------------
            */

            ->defaultSort('document_date', 'desc')

            ->columns([

                TextColumn::make('document_no')
                    ->label('PO Number')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('supplier.supplier_name')
                    ->label('Supplier')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('company.company_name')
                    ->label('Company')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('document_date')
                    ->label('PO Date')
                    ->date('d M Y')
                    ->sortable(),

                TextColumn::make('expected_delivery_date')
                    ->label('Expected Delivery')
                    ->date('d M Y')
                    ->sortable(),

                TextColumn::make('currency.currency_code')
                    ->label('Currency')
                    ->badge()
                    ->sortable(),

                TextColumn::make('grand_total')
                    ->label('Grand Total')
                    ->money('IDR')
                    ->alignEnd()
                    ->sortable(),

                TextColumn::make('status')
                    ->label('Status RR')
                    ->state(
                        fn (PurchaseOrder $record): string =>
                            self::resolveStatusRr($record)
                    )
                    ->badge()
                    ->color(fn (?string $state): string => match ($state) {
                        'Received' => 'success',
                        'Partial Received' => 'danger',
                        default => 'warning',
                    })
                    ->sortable(),

                TextColumn::make('approval_status')
                    ->label('Approval')
                    ->badge()
                    ->color(
                        fn (?string $state): string => match ($state) {

                            PurchaseOrder::APPROVAL_APPROVED
                                => 'success',

                            PurchaseOrder::APPROVAL_REJECTED
                                => 'danger',

                            PurchaseOrder::APPROVAL_WAITING,
                            PurchaseOrder::APPROVAL_PENDING
                                => 'warning',

                            default
                                => 'gray',

                        }
                    )
                    ->sortable(),

                TextColumn::make('creator.name')
                    ->label('Created By')
                    ->toggleable(
                        isToggledHiddenByDefault: true
                    )
                    ->sortable(),

            ])

            /*
            |--------------------------------------------------------------------------
            | EXISTING FILTERS
            |--------------------------------------------------------------------------
            */

            ->filters([

            ])

            /*
            |--------------------------------------------------------------------------
            | RECORD ACTIONS
            |--------------------------------------------------------------------------
            |
            | Purchase Order Workflow Action Rules
            |
            | DRAFT + PENDING APPROVAL
            | --------------------------------
            | View        : ACTIVE
            | Edit        : ACTIVE
            | Preview     : ACTIVE
            | Print       : ACTIVE
            | Export PDF  : ACTIVE
            | Delete      : ACTIVE
            |
            | SUBMITTED + WAITING APPROVAL
            | --------------------------------
            | View        : ACTIVE
            | Edit        : LOCKED
            | Preview     : ACTIVE
            | Print       : LOCKED
            | Export PDF  : ACTIVE
            | Delete      : LOCKED
            |
            | APPROVED
            | --------------------------------
            | View        : ACTIVE
            | Edit        : LOCKED
            | Preview     : ACTIVE
            | Print       : LOCKED
            | Export PDF  : ACTIVE
            | Delete      : LOCKED
            |
            |--------------------------------------------------------------------------
            */

            ->recordActions([

                ActionGroup::make([

                    /*
                    |--------------------------------------------------------------------------
                    | VIEW
                    |--------------------------------------------------------------------------
                    |
                    | Always available.
                    |
                    */

                    ViewAction::make()
                        ->label('View')
                        ->icon('heroicon-o-eye')
                        ->color('info'),


                    /*
                    |--------------------------------------------------------------------------
                    | EDIT
                    |--------------------------------------------------------------------------
                    |
                    | Editable only when:
                    |
                    | Draft + Pending Approval
                    |
                    | Otherwise:
                    | Lock icon + disabled.
                    |
                    */

                    EditAction::make()
                        ->label('Edit')
                        ->icon(
                            fn (PurchaseOrder $record): string =>
                                $record->canEdit()
                                    ? 'heroicon-o-pencil-square'
                                    : 'heroicon-o-lock-closed'
                        )
                        ->disabled(
                            fn (PurchaseOrder $record): bool =>
                                ! $record->canEdit()
                        ),


                    /*
                    |--------------------------------------------------------------------------
                    | PREVIEW
                    |--------------------------------------------------------------------------
                    |
                    | Always available.
                    |
                    */

                    Action::make('preview')
                        ->label('Preview')
                        ->icon('heroicon-o-eye')
                        ->color('info')
                        ->url(
                            fn ($record): string => route(
                                'purchase-orders.preview',
                                $record,
                            )
                        )
                        ->openUrlInNewTab(),


                    /*
                    |--------------------------------------------------------------------------
                    | PRINT
                    |--------------------------------------------------------------------------
                    |
                    | Active only when:
                    |
                    | Draft + Pending Approval
                    |
                    | Otherwise:
                    | Lock icon + disabled.
                    |
                    */

                    Action::make('print')
                        ->label('Print')
                        ->icon('heroicon-o-printer')
                        ->color('gray')
                        ->url(
                            fn ($record): string => route(
                                'purchase-orders.print',
                                $record,
                            )
                        )
                        ->openUrlInNewTab(),


                    /*
                    |--------------------------------------------------------------------------
                    | EXPORT PDF
                    |--------------------------------------------------------------------------
                    |
                    | Always available.
                    |
                    */

                    Action::make('exportPdf')
                        ->label('Export PDF')
                        ->icon('heroicon-o-document-arrow-down')
                        ->color('danger')
                        ->url(
                            fn ($record): string => route(
                                'purchase-orders.export',
                                $record,
                            )
                        )
                        ->openUrlInNewTab(),


                    /*
                    |--------------------------------------------------------------------------
                    | DELETE
                    |--------------------------------------------------------------------------
                    |
                    | Active only when:
                    |
                    | Draft + Pending Approval
                    |
                    | Otherwise:
                    | Lock icon + disabled.
                    |
                    */

                    DeleteAction::make()
                        ->label('Delete')
                        ->icon(
                            fn (PurchaseOrder $record): string =>
                                $record->canDelete()
                                    ? 'heroicon-o-trash'
                                    : 'heroicon-o-lock-closed'
                        )
                        ->disabled(
                            fn (PurchaseOrder $record): bool =>
                                ! $record->canDelete()
                        ),

                ])
                    ->label('Action')
                    ->icon('heroicon-m-ellipsis-vertical')
                    ->button(),

            ])


            /*
            |--------------------------------------------------------------------------
            | TOOLBAR ACTIONS
            |--------------------------------------------------------------------------
            */

            ->toolbarActions([

                BulkActionGroup::make([

                    DeleteBulkAction::make(),

                ]),

            ]);

    }

    private static function resolveStatusRr(
        PurchaseOrder $record
    ): string {

        /*
        |--------------------------------------------------------------------------
        | Aggregate actual received quantity per PO item
        |--------------------------------------------------------------------------
        |
        | Only active Receiving Records with rr_status:
        | RECEIVED or CLOSE are included.
        |
        */

        $receivedSubquery = DB::table('goods_receipt_items as gri')
            ->join(
                'goods_receipts as gr',
                'gr.id',
                '=',
                'gri.goods_receipt_id'
            )
            ->whereNull('gri.deleted_at')
            ->whereNull('gr.deleted_at')
            ->whereIn('gr.rr_status', [
                'RECEIVED',
                'CLOSE',
            ])
            ->select('gri.purchase_order_item_id')
            ->selectRaw('SUM(gri.received_qty) as total_received')
            ->groupBy('gri.purchase_order_item_id');

        /*
        |--------------------------------------------------------------------------
        | Evaluate every active PO item individually
        |--------------------------------------------------------------------------
        */

        $result = DB::table('purchase_order_items as poi')
            ->leftJoinSub(
                $receivedSubquery,
                'received',
                function ($join): void {
                    $join->on(
                        'received.purchase_order_item_id',
                        '=',
                        'poi.id'
                    );
                }
            )
            ->where('poi.purchase_order_id', $record->getKey())
            ->whereNull('poi.deleted_at')
            ->selectRaw('COUNT(*) as item_count')
            ->selectRaw(
                'SUM(
                    CASE
                        WHEN COALESCE(received.total_received, 0) > 0
                        THEN 1
                        ELSE 0
                    END
                ) as received_item_count'
            )
            ->selectRaw(
                'SUM(
                    CASE
                        WHEN COALESCE(received.total_received, 0) + 0.00005
                            < poi.ordered_qty
                        THEN 1
                        ELSE 0
                    END
                ) as outstanding_item_count'
            )
            ->first();

        /*
        |--------------------------------------------------------------------------
        | Preserve original PO status when no receipt exists
        |--------------------------------------------------------------------------
        */

        if (
            ! $result
            || (int) $result->item_count === 0
            || (int) $result->received_item_count === 0
        ) {
            return (string) $record->status;
        }

        /*
        |--------------------------------------------------------------------------
        | Additional receiving statuses
        |--------------------------------------------------------------------------
        */

        if ((int) $result->outstanding_item_count === 0) {
            return 'Received';
        }

        return 'Partial Received';
    }

}