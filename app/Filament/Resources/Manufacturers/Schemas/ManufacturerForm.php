<?php

declare(strict_types=1);

namespace App\Filament\Resources\Manufacturers\Schemas;

use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\Toggle;

use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class ManufacturerForm
{
    public static function configure(
        Schema $schema,
    ): Schema {

        return $schema

            ->components([

                /*
                |--------------------------------------------------------------------------
                | Manufacturer Information
                |--------------------------------------------------------------------------
                */

                Section::make('Manufacturer Information')

                    ->components([

                        Grid::make(2)

                            ->components([

                                TextInput::make('manufacturer_code')
                                    ->label('Manufacturer Code')
                                    ->required()
                                    ->unique(
                                        table: 'manufacturers',
                                        column: 'manufacturer_code',
                                        ignoreRecord: true,
                                    )
                                    ->maxLength(30)
                                    ->autocomplete(false),

                                TextInput::make('manufacturer_name')
                                    ->label('Manufacturer Name')
                                    ->required()
                                    ->maxLength(150)
                                    ->autocomplete(false),

                                TextInput::make('short_name')
                                    ->label('Short Name')
                                    ->maxLength(50)
                                    ->autocomplete(false),

                                TextInput::make('website')
                                    ->label('Website')
                                    ->url()
                                    ->maxLength(255),

                            ]),

                    ]),

                /*
                |--------------------------------------------------------------------------
                | Contact Information
                |--------------------------------------------------------------------------
                */

                Section::make('Contact Information')

                    ->components([

                        Grid::make(2)

                            ->components([

                                TextInput::make('email')
                                    ->label('Email')
                                    ->email()
                                    ->maxLength(150),

                                TextInput::make('phone')
                                    ->label('Phone')
                                    ->tel()
                                    ->maxLength(50),

                            ]),

                    ]),

                /*
                |--------------------------------------------------------------------------
                | Address
                |--------------------------------------------------------------------------
                */

                Section::make('Address')

                    ->components([

                        Textarea::make('address')
                            ->rows(3)
                            ->columnSpanFull(),

                        Grid::make(3)

                            ->components([

                                TextInput::make('city')
                                    ->label('City')
                                    ->maxLength(100),

                                TextInput::make('state')
                                    ->label('State / Province')
                                    ->maxLength(100),

                                TextInput::make('postal_code')
                                    ->label('Postal Code')
                                    ->maxLength(20),

                            ]),

                        Grid::make(1)

                            ->components([

                                TextInput::make('country')
                                    ->label('Country')
                                    ->maxLength(100),

                            ]),

                    ]),

                /*
                |--------------------------------------------------------------------------
                | Description
                |--------------------------------------------------------------------------
                */

                Section::make('Description')

                    ->components([

                        Textarea::make('remarks')
                            ->rows(4)
                            ->maxLength(1000)
                            ->columnSpanFull(),

                    ]),

                /*
                |--------------------------------------------------------------------------
                | Configuration
                |--------------------------------------------------------------------------
                */

                Section::make('Configuration')

                    ->components([

                        Grid::make(2)

                            ->components([

                                TextInput::make('sort_order')
                                    ->label('Sort Order')
                                    ->numeric()
                                    ->default(0)
                                    ->minValue(0)
                                    ->step(1)
                                    ->required(),

                                Toggle::make('is_active')
                                    ->label('Active')
                                    ->default(true),

                            ]),

                    ]),

            ]);
    }
}