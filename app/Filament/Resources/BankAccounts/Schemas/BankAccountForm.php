<?php

namespace App\Filament\Resources\BankAccounts\Schemas;

use App\Models\ChartOfAccount;
use App\Models\Currency;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class BankAccountForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([

                /*
                |--------------------------------------------------------------------------
                | MASTER SETUP
                |--------------------------------------------------------------------------
                */
                Section::make('Master Setup')
                    ->description('Bank account master information')
                    ->schema([

                        TextInput::make('bank_code')
                            ->label('Bank Code')
                            ->required()
                            ->maxLength(30),

                        TextInput::make('bank_name')
                            ->label('Bank Name')
                            ->required()
                            ->maxLength(100),

                        TextInput::make('account_no')
                            ->label('Account Number')
                            ->required()
                            ->maxLength(100),

                        TextInput::make('account_name')
                            ->label('Account Name')
                            ->required()
                            ->maxLength(200),

                        Select::make('account_type')
                            ->label('Account Type')
                            ->required()
                            ->default('Bank')
                            ->options([
                                'Cash'        => '💵 Cash',
                                'Bank'        => '🏦 Bank',
/*                                'PettyCash'   => '🧾 Petty Cash',
                                'CashAdvance' => '💳 Cash Advance',
                                'Giro'        => '📄 Giro',
                                'Escrow'      => '🔒 Escrow',*/
                            ]),

                        Select::make('payment_method')
                            ->label('Payment Method')
                            ->required()
                            ->default('Transfer')
                            ->options([
                                'Cash'           => 'Cash',
                                'Transfer'       => 'Bank Transfer',
                                'Cheque'         => 'Cheque',
                                'Giro'           => 'Giro',
                                'VirtualAccount' => 'Virtual Account',
                                'QRIS'           => 'QRIS',
                            ]),

                        Select::make('currency_id')
                            ->label('Currency')
                            ->options(
                                Currency::orderBy('currency_code')
                                    ->pluck('currency_code', 'id')
                            )
                            ->searchable()
                            ->preload(),

                        Select::make('coa_id')
                            ->label('GL Account (COA)')
                            ->options(
                                ChartOfAccount::orderBy('account_code')
                                    ->get()
                                    ->mapWithKeys(fn ($coa) => [
                                        $coa->id => $coa->account_code . ' - ' . $coa->account_name,
                                    ])
                            )
                            ->searchable()
                            ->preload(),

                        TextInput::make('branch_name')
                            ->label('Branch Name')
                            ->maxLength(150),

                        TextInput::make('swift_code')
                            ->label('SWIFT Code')
                            ->maxLength(50),

                    ])
                    ->columns(2)
                    ->columnSpanFull(),

                /*
                |--------------------------------------------------------------------------
                | FINANCIAL INFORMATION
                |--------------------------------------------------------------------------
                */
                Section::make('Financial Information')
                    ->schema([

                        TextInput::make('opening_balance')
                            ->label('Opening Balance')
                            ->prefix('IDR')
                            ->default(0)
                            ->afterStateHydrated(function ($component, $state) {
                                $component->state(
                                    number_format((float) $state, 0, '.', ',')
                                );
                            })
                            ->dehydrateStateUsing(function ($state) {
                                return (float) str_replace(',', '', $state);
                            }),

                    ])
                    ->columnSpanFull(),
                /*
                |--------------------------------------------------------------------------
                | TREASURY CONTROLS
                |--------------------------------------------------------------------------
                */
                Section::make('Treasury Controls')
                    ->description('Treasury operation permissions')
                    ->schema([

                        Toggle::make('allow_payment')
                            ->label('💳 Payment')
                            ->default(true),

                        Toggle::make('allow_receipt')
                            ->label('📥 Receipt')
                            ->default(true),

                        Toggle::make('allow_transfer')
                            ->label('🔄 Transfer')
                            ->default(true),

                        Toggle::make('is_default')
                            ->label('⭐ Default Bank')
                            ->default(false),

                        Toggle::make('is_active')
                            ->label('🟢 Active')
                            ->default(true),

                    ])
                    ->columns(5)
                    ->columnSpanFull(),

            ]);
    }
}