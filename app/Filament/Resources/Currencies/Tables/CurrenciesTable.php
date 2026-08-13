<?php

namespace App\Filament\Resources\Currencies\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Enums\RecordActionsPosition;
use Filament\Tables\Table;

class CurrenciesTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->defaultSort('currency_code')

            ->columns([

                TextColumn::make('currency_code')
                    ->label('Code')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('currency_name')
                    ->label('Currency Name')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('symbol')
                    ->label('Symbol')
                    ->searchable(),

                TextColumn::make('decimal_places')
                    ->label('Decimal')
                    ->sortable(),

                IconColumn::make('is_base_currency')
                    ->label('Base')
                    ->boolean(),

                IconColumn::make('is_active')
                    ->label('Active')
                    ->boolean(),

                TextColumn::make('created_at')
                    ->label('Create Date')
                    ->dateTime('d M Y H:i')
                    ->sortable(),

                TextColumn::make('creator.name')
                    ->label('Created By')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('updated_at')
                    ->label('Update Date')
                    ->dateTime('d M Y H:i')
                    ->sortable(),

                TextColumn::make('updater.name')
                    ->label('Updated By')
                    ->searchable()
                    ->sortable(),

            ])

            ->recordActionsPosition(
                RecordActionsPosition::AfterColumns
            )

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