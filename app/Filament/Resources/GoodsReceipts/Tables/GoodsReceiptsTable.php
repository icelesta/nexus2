<?php

declare(strict_types=1);

namespace App\Filament\Resources\GoodsReceipts\Tables;

use Filament\Actions\ActionGroup;
use Filament\Actions\ViewAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class GoodsReceiptsTable
{
    public static function configure(
        Table $table
    ): Table {

        return $table

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