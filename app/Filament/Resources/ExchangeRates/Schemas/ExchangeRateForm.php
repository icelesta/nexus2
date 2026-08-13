<?php

namespace App\Filament\Resources\ExchangeRates\Schemas;

use App\Models\Currency;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Schema;

class ExchangeRateForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([

                Select::make('from_currency_id')
                    ->label('From Currency')
                    ->relationship('fromCurrency', 'currency_code')
                    ->searchable()
                    ->preload()
                    ->required(),

                Select::make('to_currency_id')
                    ->label('To Currency')
                    ->relationship('toCurrency', 'currency_code')
                    ->searchable()
                    ->preload()
                    ->required()
                    ->different('from_currency_id'),

                DatePicker::make('exchange_date')
                    ->label('Exchange Date')
                    ->native(false)
                    ->default(now())
                    ->required(),

                Select::make('rate_type')
                    ->label('Rate Type')
                    ->options([
                        'Spot' => 'Spot',
                        'TT' => 'TT',
                        'Bank Note' => 'Bank Note',
                    ])
                    ->default('Spot')
                    ->required(),

                TextInput::make('buy_rate')
                    ->label('Buy Rate')
                    ->numeric()
                    ->required()
                    ->step('0.00000001')
                    ->minValue(0),

                TextInput::make('sell_rate')
                    ->label('Sell Rate')
                    ->numeric()
                    ->required()
                    ->step('0.00000001')
                    ->minValue(0),

                TextInput::make('middle_rate')
                    ->label('Middle Rate')
                    ->numeric()
                    ->required()
                    ->step('0.00000001')
                    ->minValue(0),

                TextInput::make('source')
                    ->label('Source')
                    ->maxLength(100)
                    ->placeholder('Bank Indonesia, Bloomberg, Reuters, etc.'),

                Textarea::make('remarks')
                    ->label('Remarks')
                    ->rows(3)
                    ->columnSpanFull(),

                Toggle::make('is_active')
                    ->label('Active')
                    ->default(true),

            ])
            ->columns(2);
    }
}