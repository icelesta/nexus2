<?php

declare(strict_types=1);

namespace App\Filament\Resources\Brands\Schemas;

use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\Toggle;

use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class BrandForm
{
    public static function configure(
        Schema $schema,
    ): Schema {

        return $schema

            ->components([

                Section::make('Brand Information')

                    ->components([

                        Grid::make(2)

                            ->components([

                                TextInput::make('brand_code')
                                    ->label('Brand Code')
                                    ->required()
                                    ->unique(
                                        table: 'brands',
                                        column: 'brand_code',
                                        ignoreRecord: true,
                                    )
                                    ->maxLength(30)
                                    ->autocomplete(false),

                                TextInput::make('brand_name')
                                    ->label('Brand Name')
                                    ->required()
                                    ->maxLength(150)
                                    ->autocomplete(false),

                                TextInput::make('short_name')
                                    ->label('Short Name')
                                    ->maxLength(50)
                                    ->autocomplete(false),

                                TextInput::make('manufacturer_name')
                                    ->label('Manufacturer')
                                    ->maxLength(150),

                            ]),

                    ]),

                Section::make('Contact Information')

                    ->components([

                        Grid::make(2)

                            ->components([

                                TextInput::make('website')
                                    ->label('Website')
                                    ->url()
                                    ->suffixIcon('heroicon-m-globe-alt')
                                    ->maxLength(255),

                                TextInput::make('email')
                                    ->email()
                                    ->autocomplete(false)
                                    ->maxLength(150),

                                TextInput::make('phone')
                                    ->tel()
                                    ->autocomplete(false)
                                    ->maxLength(50),

                            ]),

                    ]),

                Section::make('Description')

                    ->components([

                        Textarea::make('remarks')
                            ->rows(4)
                            ->maxLength(1000)
                            ->columnSpanFull(),

                    ]),

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