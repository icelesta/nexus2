<?php

namespace App\Filament\Resources\TaxMasters\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Actions\ActionGroup;

use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class TaxMastersTable
{
    public static function configure(Table $table): Table
    {
        return $table

            ->defaultSort('sort_order')

            ->columns([

                TextColumn::make('tax_code')
                    ->label('Tax Code')
                    ->sortable()
                    ->searchable(),

                TextColumn::make('tax_name')
                    ->label('Tax Name')
                    ->sortable()
                    ->searchable(),

                TextColumn::make('tax_type')
                    ->badge()
                    ->sortable(),

                TextColumn::make('tax_rate')
                    ->label('Rate (%)')
                    ->numeric(
                        decimalPlaces: 2
                    )
                    ->suffix('%')
                    ->sortable(),

                TextColumn::make('calculation_method')
                    ->label('Calculation')
                    ->badge()
                    ->sortable(),

                TextColumn::make('taxAccount.account_code')
                    ->label('Tax Account')
                    ->sortable()
                    ->searchable(),

                TextColumn::make('effective_date')
                    ->label('Effective')
                    ->date('d M Y')
                    ->sortable(),

                TextColumn::make('expired_date')
                    ->label('Expired')
                    ->date('d M Y')
                    ->placeholder('-')
                    ->sortable(),

                IconColumn::make('is_default')
                    ->label('Default')
                    ->boolean(),

                IconColumn::make('is_inclusive')
                    ->label('Inclusive')
                    ->boolean(),

                IconColumn::make('is_withholding')
                    ->label('WHT')
                    ->boolean(),

                IconColumn::make('is_active')
                    ->label('Active')
                    ->boolean(),

                TextColumn::make('sort_order')
                    ->label('Sort')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),

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

                //

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