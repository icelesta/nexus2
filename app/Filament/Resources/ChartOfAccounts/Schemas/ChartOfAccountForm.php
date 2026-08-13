<?php

namespace App\Filament\Resources\ChartOfAccounts\Schemas;

use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Schema;

class ChartOfAccountForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema

            ->components([

                Select::make('company_id')
                    ->relationship('company', 'company_name')
                    ->searchable()
                    ->preload()
                    ->required(),

                TextInput::make('account_code')
                    ->label('Account Code')
                    ->required()
                    ->unique(ignoreRecord: true)
                    ->maxLength(30),

                TextInput::make('account_name')
                    ->label('Account Name')
                    ->required()
                    ->maxLength(200),

                Select::make('parent_account_id')
                    ->label('Parent Account')
                    ->relationship(
                        'parent',
                        'account_name',
                        fn ($query) => $query->orderBy('account_code')
                    )
                    ->searchable()
                    ->preload()
                    ->getOptionLabelFromRecordUsing(
                        fn (\App\Models\ChartOfAccount $record): string =>
                            "{$record->account_code} - {$record->account_name}"
                    )
                    ->searchable()
                    ->preload(),

                Select::make('account_type')
                    ->label('Account Type')
                    ->options([
                        'Asset'     => 'Asset',
                        'Liability' => 'Liability',
                        'Equity'    => 'Equity',
                        'Revenue'   => 'Revenue',
                        'Expense'   => 'Expense',
                    ])
                    ->required(),

                Select::make('normal_balance')
                    ->label('Normal Balance')
                    ->options([
                        'Debit'  => 'Debit',
                        'Credit' => 'Credit',
                    ])
                    ->required(),

                Select::make('currency_id')
                    ->relationship('currency', 'currency_code')
                    ->searchable()
                    ->preload(),

                Toggle::make('is_control_account')
                    ->label('Control Account'),

                Toggle::make('allow_manual_entry')
                    ->label('Allow Manual Entry')
                    ->default(true),

                Toggle::make('is_cash_account')
                    ->label('Cash Account'),

                Toggle::make('is_bank_account')
                    ->label('Bank Account'),

                Toggle::make('is_tax_account')
                    ->label('Tax Account'),

                Toggle::make('is_retained_earning')
                    ->label('Retained Earnings'),

                Toggle::make('is_active')
                    ->label('Active')
                    ->default(true),

                TextInput::make('sort_order')
                    ->label('Sort Order')
                    ->numeric()
                    ->default(1)
                    ->required(),

                Textarea::make('description')
                    ->label('Description')
                    ->rows(4)
                    ->columnSpanFull(),

            ])

            ->columns(2);
    }
}