<?php

namespace App\Filament\Resources\Customers\Tables;

use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Actions\BulkActionGroup;

use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;

use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Filters\TernaryFilter;

use Filament\Tables\Table;

class CustomersTable
{
    public static function configure(Table $table): Table
    {
        return $table

            ->defaultSort('customer_name')

            ->columns([

                TextColumn::make('customer_code')
                    ->label('Customer Code')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('customer_name')
                    ->label('Customer Name')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('category.category_name')
                    ->label('Category')
                    ->placeholder('-')
                    ->toggleable(),

                TextColumn::make('customer_type')
                    ->label('Type')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {

                        'Corporate'  => 'success',
                        'Government' => 'warning',
                        'Individual' => 'info',
                        'Affiliate'  => 'gray',
                        'Partner'    => 'primary',

                        default => 'gray',
                    })
                    ->sortable(),

                TextColumn::make('currency.currency_code')
                    ->label('Currency')
                    ->badge()
                    ->placeholder('-'),

                TextColumn::make('paymentTerm.term_name')
                    ->label('Payment Term')
                    ->badge()
                    ->placeholder('-'),

                TextColumn::make('credit_limit')
                    ->label('Credit Limit')
                    ->badge()
                    ->formatStateUsing(function ($state, $record) {

                        return ($record->currency?->currency_code ?? 'IDR')
                            . ' '
                            . number_format($state, 2);

                    })
                    ->sortable()
                    ->toggleable(),

                TextColumn::make('credit_days')
                    ->label('Credit Days')
                    ->sortable()
                    ->toggleable(),

                IconColumn::make('is_preferred')
                    ->label('Preferred')
                    ->boolean(),

                IconColumn::make('is_blacklisted')
                    ->label('Blacklisted')
                    ->boolean(),

                IconColumn::make('is_active')
                    ->label('Active')
                    ->boolean()
                    ->sortable(),

                TextColumn::make('creator.name')
                    ->label('Created By')
                    ->placeholder('-')
                    ->toggleable(isToggledHiddenByDefault: true),

                TextColumn::make('created_at')
                    ->label('Created Date')
                    ->dateTime('d M Y H:i')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),

                TextColumn::make('updater.name')
                    ->label('Updated By')
                    ->placeholder('-')
                    ->toggleable(isToggledHiddenByDefault: true),

                TextColumn::make('updated_at')
                    ->label('Updated Date')
                    ->dateTime('d M Y H:i')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),

            ])

            ->filters([

                SelectFilter::make('customer_type')
                    ->label('Customer Type'),

                TernaryFilter::make('is_active')
                    ->label('Active'),

                TernaryFilter::make('is_preferred')
                    ->label('Preferred'),

            ])

            ->recordActions([

                ViewAction::make(),

                EditAction::make(),

                DeleteAction::make()
                    ->requiresConfirmation(),

            ])

            ->toolbarActions([

                BulkActionGroup::make([

                    DeleteBulkAction::make(),

                ]),

            ]);
    }
}