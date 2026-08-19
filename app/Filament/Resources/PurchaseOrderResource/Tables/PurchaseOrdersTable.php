<?php

declare(strict_types=1);

namespace App\Filament\Resources\PurchaseOrderResource\Tables;

use Filament\Actions\Action;
use Filament\Actions\ActionGroup;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;

use App\Models\PurchaseOrder;
use Filament\Tables\Table;
use Filament\Tables\Columns\TextColumn;

class PurchaseOrdersTable
{
    public static function configure(
        Table $table,
    ): Table {

        return $table

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
                    ->color(fn (?string $state): string => match ($state) {

                        PurchaseOrder::APPROVAL_APPROVED
                            => 'success',

                        PurchaseOrder::APPROVAL_REJECTED
                            => 'danger',

                        PurchaseOrder::APPROVAL_WAITING,
                        PurchaseOrder::APPROVAL_PENDING
                            => 'warning',

                        default
                            => 'gray',

                    })
                    ->sortable(),

                TextColumn::make('creator.name')
                    ->label('Created By')
                    ->toggleable(isToggledHiddenByDefault: true)
                    ->sortable(),

            ])

            ->filters([

            ])

            ->recordActions([

                ActionGroup::make([

                    EditAction::make(),

                    Action::make('preview')
                        ->label('Preview')
                        ->icon('heroicon-o-eye')
                        ->color('info')
                        ->url(
                            fn ($record): string => route(
                                'purchase-orders.preview',
                                $record,
                            ),
                        )
                        ->openUrlInNewTab(),

                    Action::make('print')
                        ->label('Print')
                        ->icon('heroicon-o-printer')
                        ->color('gray')
                        ->url(
                            fn ($record): string => route(
                                'purchase-orders.print',
                                $record,
                            ),
                        )
                        ->openUrlInNewTab(),

                    Action::make('exportPdf')
                        ->label('Export PDF')
                        ->icon('heroicon-o-document-arrow-down')
                        ->color('danger')
                        ->url(
                            fn ($record): string => route(
                                'purchase-orders.export',
                                $record,
                            ),
                        )
                        ->openUrlInNewTab(),

                    DeleteAction::make(),

                ])
                    ->label('Action')
                    ->icon('heroicon-m-ellipsis-vertical')
                    ->button(),

            ])

            ->toolbarActions([

                BulkActionGroup::make([

                    DeleteBulkAction::make(),

                ]),

            ]);

    }
}