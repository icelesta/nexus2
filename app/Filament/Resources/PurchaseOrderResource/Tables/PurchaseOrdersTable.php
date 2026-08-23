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

                        $query->whereDate(
                            'purchase_orders.document_date',
                            '>=',
                            $livewire->globalDateFrom
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

                        $query->whereDate(
                            'purchase_orders.document_date',
                            '<=',
                            $livewire->globalDateTo
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
                    ->label('Status')
                    ->badge()
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
}