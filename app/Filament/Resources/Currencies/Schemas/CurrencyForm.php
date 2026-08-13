<?php

namespace App\Filament\Resources\Currencies\Schemas;

use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Schema;

class CurrencyForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([

                TextInput::make('currency_code')
                    ->label('Currency Code')
                    ->required()
                    ->maxLength(10),

                TextInput::make('currency_name')
                    ->label('Currency Name')
                    ->required()
                    ->maxLength(100),

                TextInput::make('symbol')
                    ->label('Symbol')
                    ->maxLength(10),

                TextInput::make('decimal_places')
                    ->label('Decimal Places')
                    ->numeric()
                    ->default(2)
                    ->required(),

                Toggle::make('is_base_currency')
                    ->label('Base Currency')
                    ->default(false),

                Toggle::make('is_active')
                    ->label('Active')
                    ->default(true),
            ]);
    }
}