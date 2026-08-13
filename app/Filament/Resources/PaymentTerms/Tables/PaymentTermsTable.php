<?php

namespace App\Filament\Resources\PaymentTerms\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;

use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class PaymentTermsTable
{
    public static function configure(Table $table): Table
    {
        return $table

            ->defaultSort('term_code')

            ->columns([

                TextColumn::make('company.company_name')
                    ->label('Company')
                    ->searchable()
                    ->sortable()
                    ->toggleable(),

                TextColumn::make('term_code')
                    ->label('Code')
                    ->badge()
                    ->color('primary')
                    ->copyable()
                    ->searchable()
                    ->sortable(),

                TextColumn::make('term_name')
                    ->label('Payment Term')
                    ->searchable()
                    ->sortable()
                    ->limit(40)
                    ->wrap(),

                TextColumn::make('due_days')
                    ->label('Due Days')
                    ->badge()
                    ->suffix(' Days')
                    ->alignCenter()
                    ->sortable(),

                TextColumn::make('discount_percent')
                    ->label('Discount')
                    ->numeric(decimalPlaces: 2)
                    ->suffix('%')
                    ->alignCenter()
                    ->sortable(),

                TextColumn::make('discount_days')
                    ->label('Discount Days')
                    ->suffix(' Days')
                    ->alignCenter()
                    ->sortable(),

                TextColumn::make('down_payment_percent')
                    ->label('Down Payment')
                    ->numeric(decimalPlaces: 2)
                    ->suffix('%')
                    ->alignCenter()
                    ->sortable(),

                TextColumn::make('installment_count')
                    ->label('Installment')
                    ->badge()
                    ->alignCenter()
                    ->sortable(),

                TextColumn::make('grace_period_days')
                    ->label('Grace')
                    ->suffix(' Days')
                    ->alignCenter()
                    ->sortable()
                    ->toggleable(
                        isToggledHiddenByDefault: true
                    ),

                TextColumn::make('sort_order')
                    ->label('Sort')
                    ->alignCenter()
                    ->sortable()
                    ->toggleable(
                        isToggledHiddenByDefault: true
                    ),

                IconColumn::make('is_default')
                    ->label('Default')
                    ->boolean(),

                IconColumn::make('is_active')
                    ->label('Active')
                    ->boolean(),

                TextColumn::make('created_at')
                    ->label('Created')
                    ->dateTime('d M Y H:i')
                    ->sortable()
                    ->toggleable(),

                TextColumn::make('creator.name')
                    ->label('Created By')
                    ->placeholder('-')
                    ->toggleable(
                        isToggledHiddenByDefault: true
                    ),

                TextColumn::make('updated_at')
                    ->label('Updated')
                    ->dateTime('d M Y H:i')
                    ->sortable()
                    ->toggleable(),

                TextColumn::make('updater.name')
                    ->label('Updated By')
                    ->placeholder('-')
                    ->toggleable(
                        isToggledHiddenByDefault: true
                    ),

            ])

            ->filters([

                //

            ])

            ->recordActions([

                ViewAction::make()
                    ->icon('heroicon-o-eye')
                    ->label('')
                    ->tooltip('View'),

                EditAction::make()
                    ->icon('heroicon-o-pencil-square')
                    ->label('')
                    ->color('warning')
                    ->tooltip('Edit'),

                DeleteAction::make()
                    ->icon('heroicon-o-trash')
                    ->label('')
                    ->color('danger')
                    ->tooltip('Delete')
                    ->requiresConfirmation(),

            ])

            ->toolbarActions([

                BulkActionGroup::make([

                    DeleteBulkAction::make(),

                ]),

            ]);
    }
}