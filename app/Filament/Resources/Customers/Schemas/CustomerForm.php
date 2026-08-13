<?php

namespace App\Filament\Resources\Customers\Schemas;

use App\Models\ChartOfAccount;
use App\Models\Currency;
use App\Models\MasterCategory;
use App\Models\PaymentTerm;

use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;

use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class CustomerForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([

                /*
                |--------------------------------------------------------------------------
                | General Information
                |--------------------------------------------------------------------------
                */

                Section::make('General Information')
                    ->schema([

                        TextInput::make('customer_code')
                            ->label('Customer Code')
                            ->required()
                            ->unique(ignoreRecord: true)
                            ->maxLength(50),

                        TextInput::make('customer_name')
                            ->label('Customer Name')
                            ->required()
                            ->maxLength(200),

                        Select::make('category_id')
                            ->label('Category')
                            ->options(
                                MasterCategory::query()
                                    ->orderBy('category_name')
                                    ->pluck(
                                        'category_name',
                                        'id'
                                    )
                            )
                            ->searchable()
                            ->preload(),

                        Select::make('customer_type')
                            ->label('Customer Type')
                            ->options([

                                'Corporate'  => 'Corporate',
                                'Government' => 'Government',
                                'Individual' => 'Individual',
                                'Affiliate'  => 'Affiliate',
                                'Partner'    => 'Partner',

                            ])
                            ->searchable(),

                        TextInput::make('tax_number')
                            ->label('Tax Number / NPWP')
                            ->maxLength(100),

                    ])
                    ->columns(2),

                /*
                |--------------------------------------------------------------------------
                | Contact Information
                |--------------------------------------------------------------------------
                */

                Section::make('Contact Information')
                    ->schema([

                        TextInput::make('email')
                            ->label('Email')
                            ->email(),

                        TextInput::make('phone')
                            ->label('Phone'),

                        TextInput::make('mobile')
                            ->label('Mobile'),

                        TextInput::make('website')
                            ->label('Website'),

                        TextInput::make('pic_name')
                            ->label('PIC Name'),

                        TextInput::make('pic_position')
                            ->label('PIC Position'),

                    ])
                    ->columns(2),

                /*
                |--------------------------------------------------------------------------
                | Address Information
                |--------------------------------------------------------------------------
                */

                Section::make('Address Information')
                    ->schema([

                        Textarea::make('address')
                            ->label('Address')
                            ->rows(3)
                            ->columnSpanFull(),

                        TextInput::make('city')
                            ->label('City'),

                        TextInput::make('province')
                            ->label('Province'),

                        TextInput::make('country')
                            ->label('Country')
                            ->default('Indonesia'),

                        TextInput::make('postal_code')
                            ->label('Postal Code'),

                    ])
                    ->columns(2),

                /*
                |--------------------------------------------------------------------------
                | Finance Information
                |--------------------------------------------------------------------------
                */

                Section::make('Finance Information')
                    ->schema([

                        Select::make('currency_id')
                            ->label('Currency')
                            ->options(
                                Currency::query()
                                    ->orderBy('currency_code')
                                    ->pluck(
                                        'currency_code',
                                        'id'
                                    )
                            )
                            ->searchable()
                            ->preload(),

                        Select::make('ar_account_id')
                            ->label('Account Receivable Account')
                            ->options(

                                ChartOfAccount::query()
                                    ->where('account_type', 'Assets')
                                    ->orderBy('account_code')
                                    ->get()
                                    ->mapWithKeys(fn ($account) => [

                                        $account->id =>
                                            $account->account_code .
                                            ' - ' .
                                            $account->account_name

                                    ])

                            )
                            ->searchable()
                            ->preload(),

                        Select::make('default_payment_term_id')
                            ->label('Default Payment Term')
                            ->options(
                                PaymentTerm::query()
                                    ->orderBy('term_name')
                                    ->pluck(
                                        'term_name',
                                        'id'
                                    )
                            )
                            ->searchable()
                            ->preload(),

                    ])
                    ->columns(3),

                /*
                |--------------------------------------------------------------------------
                | Credit Control
                |--------------------------------------------------------------------------
                */

                Section::make('Credit Control')
                    ->schema([

                        TextInput::make('credit_limit')
                            ->label('Credit Limit')
                            ->numeric()
                            ->default(0),

                        TextInput::make('credit_days')
                            ->label('Credit Days')
                            ->numeric()
                            ->default(0),

                    ])
                    ->columns(2),

                /*
                |--------------------------------------------------------------------------
                | Customer Status
                |--------------------------------------------------------------------------
                */

                Section::make('Customer Status')
                    ->schema([

                        Toggle::make('is_preferred')
                            ->label('Preferred Customer')
                            ->default(false),

                        Toggle::make('is_blacklisted')
                            ->label('Blacklisted')
                            ->default(false),

                        Toggle::make('is_active')
                            ->label('Active')
                            ->default(true),

                    ])
                    ->columns(3),

            ]);
    }
}