<?php

namespace App\Filament\Resources\ExchangeRates\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Actions\ActionGroup;

use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Filters\TernaryFilter;
use Filament\Tables\Table;

class ExchangeRatesTable
{
    public static function configure(Table $table): Table
    {
        return $table

            ->defaultSort('exchange_date', 'desc')

            ->columns([

                TextColumn::make('fromCurrency.currency_code')
                    ->label('From')
                    ->sortable()
                    ->searchable(),

                TextColumn::make('toCurrency.currency_code')
                    ->label('To')
                    ->sortable()
                    ->searchable(),

                TextColumn::make('exchange_date')
                    ->label('Exchange Date')
                    ->date('d M Y')
                    ->sortable(),

                TextColumn::make('rate_type')
                    ->label('Rate Type')
                    ->badge()
                    ->sortable(),

                TextColumn::make('buy_rate')
                    ->label('Buy')
                    ->numeric(
                        decimalPlaces: 2,
                        thousandsSeparator: ',',
                        decimalSeparator: '.'
                    )
                    ->alignEnd()
                    ->sortable(),

                TextColumn::make('sell_rate')
                    ->label('Sell')
                    ->numeric(
                        decimalPlaces: 2,
                        thousandsSeparator: ',',
                        decimalSeparator: '.'
                    )
                    ->alignEnd()
                    ->sortable(),

                TextColumn::make('middle_rate')
                    ->label('Middle')
                    ->numeric(
                        decimalPlaces: 2,
                        thousandsSeparator: ',',
                        decimalSeparator: '.'
                    )
                    ->alignEnd()
                    ->sortable(),

                TextColumn::make('source')
                    ->label('Source')
                    ->toggleable(),

                IconColumn::make('is_active')
                    ->label('Active')
                    ->boolean(),

                TextColumn::make('created_at')
                    ->label('Create Date')
                    ->dateTime('d M Y H:i')
                    ->sortable(),

                TextColumn::make('creator.name')
                    ->label('Created By')
                    ->sortable(),

                TextColumn::make('updated_at')
                    ->label('Update Date')
                    ->dateTime('d M Y H:i')
                    ->sortable(),

                TextColumn::make('updater.name')
                    ->label('Updated By')
                    ->sortable(),

            ])

            ->filters([

                SelectFilter::make('rate_type')
                    ->options([
                        'Spot' => 'Spot',
                        'TT' => 'TT',
                        'Bank Note' => 'Bank Note',
                    ]),

                TernaryFilter::make('is_active')
                    ->label('Active'),

            ])

            ->recordActions([
                ActionGroup::make([
                    ViewAction::make()
                        ->label('View'),

                    EditAction::make()
                        ->label('Edit'),

                    DeleteAction::make()
                        ->label('Delete')
                        ->requiresConfirmation(),
                ])
                    ->label('Actions')
                    ->button()
                    ->color('warning')
                    ->icon('heroicon-m-ellipsis-vertical'),
            ])

            ->toolbarActions([

                BulkActionGroup::make([

                    DeleteBulkAction::make(),

                ]),

            ]);
    }
}