<?php

namespace App\Filament\Resources\BankAccounts\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

use App\Filament\Concerns\HasProtectedDeleteAction;

class BankAccountsTable
{

    use HasProtectedDeleteAction;
    
    public static function configure(Table $table): Table
    {
        return $table
            ->defaultSort('bank_code')

            ->columns([

                TextColumn::make('bank_code')
                    ->label('Bank Code')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('bank_name')
                    ->label('Bank Name')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('account_no')
                    ->label('Account Number')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('account_name')
                    ->label('Account Name')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('currency.currency_code')
                    ->label('Currency')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('coa.account_code')
                    ->label('GL Account')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('account_type')
                    ->label('Type')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'Cash' => 'success',
                        'Bank' => 'primary',
                        'PettyCash' => 'warning',
                        'CashAdvance' => 'info',
                        'Giro' => 'gray',
                        'Escrow' => 'danger',
                        default => 'gray',
                    })
                    ->sortable(),

                TextColumn::make('payment_method')
                    ->label('Payment Method')
                    ->badge()
                    ->color('primary')
                    ->sortable(),

                IconColumn::make('allow_payment')
                    ->label('Payment')
                    ->boolean()
                    ->alignCenter(),

                IconColumn::make('allow_receipt')
                    ->label('Receipt')
                    ->boolean()
                    ->alignCenter(),

                IconColumn::make('allow_transfer')
                    ->label('Transfer')
                    ->boolean()
                    ->alignCenter(),

                TextColumn::make('opening_balance')
                    ->label('Opening Balance')
                    ->numeric(
                        decimalPlaces: 2,
                        thousandsSeparator: ','
                    )
                    ->sortable(),

                IconColumn::make('is_default')
                    ->label('Default')
                    ->boolean()
                    ->alignCenter(),

                IconColumn::make('is_active')
                    ->label('Active')
                    ->boolean()
                    ->alignCenter(),

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

            ->filters([
                //
            ])

            ->recordActions([

                EditAction::make()
                    ->icon('heroicon-o-pencil-square')
                    ->label('')
                    ->color('warning')
                    ->tooltip('Edit'),

                self::deleteAction()
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