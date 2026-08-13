<?php

namespace App\Filament\Resources\ChartOfAccounts\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Filters\TernaryFilter;
use Filament\Tables\Table;

use App\Filament\Concerns\HasProtectedDeleteAction;

class ChartOfAccountsTable
{
    use HasProtectedDeleteAction;

    public static function configure(Table $table): Table
    {
        return $table

            ->defaultSort('account_code')

            ->columns([

                TextColumn::make('company.company_name')
                    ->label('Company')
                    ->sortable()
                    ->searchable(),

                TextColumn::make('account_code')
                    ->label('Code')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('account_name')
                    ->label('Account Name')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('parent.account_name')
                    ->label('Parent')
                    ->placeholder('-')
                    ->sortable(),

                TextColumn::make('account_type')
                    ->label('Type')
                    ->badge()
                    ->sortable(),

                TextColumn::make('normal_balance')
                    ->label('Normal')
                    ->badge()
                    ->color(fn(string $state): string => match ($state) {
                        'Debit' => 'success',
                        'Credit' => 'danger',
                        default => 'gray',
                    })
                    ->sortable(),

                TextColumn::make('currency.currency_code')
                    ->label('Currency')
                    ->placeholder('-')
                    ->sortable(),

                IconColumn::make('is_control_account')
                    ->label('Control')
                    ->boolean(),

                IconColumn::make('allow_manual_entry')
                    ->label('Manual')
                    ->boolean(),

                IconColumn::make('is_cash_account')
                    ->label('Cash')
                    ->boolean()
                    ->toggleable(),

                IconColumn::make('is_bank_account')
                    ->label('Bank')
                    ->boolean()
                    ->toggleable(),

                IconColumn::make('is_tax_account')
                    ->label('Tax')
                    ->boolean()
                    ->toggleable(),

                IconColumn::make('is_retained_earning')
                    ->label('Retained')
                    ->boolean()
                    ->toggleable(),

                IconColumn::make('is_active')
                    ->label('Active')
                    ->boolean(),

                TextColumn::make('created_at')
                    ->label('Created')
                    ->dateTime('d M Y H:i')
                    ->toggleable(isToggledHiddenByDefault: true),

                TextColumn::make('creator.name')
                    ->label('Created By')
                    ->toggleable(isToggledHiddenByDefault: true),

                TextColumn::make('updated_at')
                    ->label('Updated')
                    ->dateTime('d M Y H:i')
                    ->toggleable(isToggledHiddenByDefault: true),

                TextColumn::make('updater.name')
                    ->label('Updated By')
                    ->toggleable(isToggledHiddenByDefault: true),

            ])

            ->filters([

                SelectFilter::make('account_type')
                    ->options([
                        'Asset' => 'Asset',
                        'Liability' => 'Liability',
                        'Equity' => 'Equity',
                        'Revenue' => 'Revenue',
                        'Expense' => 'Expense',
                    ]),

                TernaryFilter::make('is_active')
                    ->label('Active'),

                TernaryFilter::make('allow_manual_entry')
                    ->label('Manual Entry'),

            ])

            ->recordActions([

                ViewAction::make()
                    ->label(''),

                EditAction::make()
                    ->label(''),

                self::deleteAction()
                    ->label('')
                    ->requiresConfirmation(),

            ])

            ->toolbarActions([

                BulkActionGroup::make([

                    DeleteBulkAction::make(),

                ]),

            ]);
    }
}