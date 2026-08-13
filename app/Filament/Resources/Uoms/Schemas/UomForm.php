<?php

declare(strict_types=1);

namespace App\Filament\Resources\Uoms\Schemas;

use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Schema;

class UomForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema->components([

            TextInput::make('uom_code')
                ->label('UOM Code')
                ->required()
                ->unique(ignoreRecord: true)
                ->maxLength(20)
                ->autocomplete(false),

            TextInput::make('uom_name')
                ->label('Unit of Measure')
                ->required()
                ->maxLength(100),

            TextInput::make('symbol')
                ->required()
                ->maxLength(20),

            Select::make('category')
                ->required()
                ->default('Quantity')
                ->options([
                    'Quantity'   => 'Quantity',
                    'Weight'     => 'Weight',
                    'Length'     => 'Length',
                    'Area'       => 'Area',
                    'Volume'     => 'Volume',
                    'Time'       => 'Time',
                    'Pressure'   => 'Pressure',
                    'Temperature'=> 'Temperature',
                    'Energy'     => 'Energy',
                ]),

            TextInput::make('decimal_places')
                ->numeric()
                ->default(0)
                ->minValue(0)
                ->maxValue(6)
                ->required(),

            TextInput::make('sort_order')
                ->numeric()
                ->default(0)
                ->required(),

            Toggle::make('allow_fraction')
                ->label('Allow Fraction')
                ->default(false),

            Toggle::make('is_base')
                ->label('Base UOM')
                ->default(false),

            Toggle::make('is_active')
                ->label('Active')
                ->default(true),

            Textarea::make('remarks')
                ->rows(3)
                ->columnSpanFull(),

        ]);
    }
}