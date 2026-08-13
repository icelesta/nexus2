<?php

declare(strict_types=1);

namespace App\Filament\Resources\ShippingAddresses\Schemas;

use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Grid;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Schema;

class ShippingAddressForm
{
    public static function configure(
        Schema $schema,
    ): Schema {

        return $schema
            ->components([

                Section::make('General Information')
                    ->columns(2)
                    ->schema([

                        TextInput::make('shipping_code')
                            ->label('Shipping Code')
                            ->disabled()
                            ->dehydrated(false)
                            ->helperText('Generated automatically.'),

                        TextInput::make('shipping_name')
                            ->label('Shipping Name')
                            ->required()
                            ->maxLength(150),

                        Toggle::make('is_default')
                            ->label('Default Shipping')
                            ->inline(false),

                        Toggle::make('is_active')
                            ->label('Active')
                            ->default(true)
                            ->inline(false),

                    ]),

                Section::make('Organization')
                    ->columns(2)
                    ->schema([

                        Select::make('company_id')
                            ->relationship('company', 'company_name')
                            ->searchable()
                            ->preload()
                            ->required(),

                        Select::make('business_unit_id')
                            ->relationship('businessUnit', 'business_unit_name')
                            ->searchable()
                            ->preload(),

                        Select::make('branch_id')
                            ->relationship('branch', 'branch_name')
                            ->searchable()
                            ->preload()
                            ->required(),

                        Select::make('warehouse_id')
                            ->relationship('warehouse', 'warehouse_name')
                            ->searchable()
                            ->preload(),

                    ]),

                Section::make('Address Information')
                    ->columns(2)
                    ->schema([

                        Textarea::make('address')
                            ->columnSpanFull()
                            ->rows(3)
                            ->required(),

                        TextInput::make('city')
                            ->maxLength(100),

                        TextInput::make('province')
                            ->maxLength(100),

                        TextInput::make('postal_code')
                            ->maxLength(20),

                        TextInput::make('country')
                            ->default('Indonesia')
                            ->maxLength(100),

                    ]),

                Section::make('Contact Information')
                    ->columns(2)
                    ->schema([

                        TextInput::make('attention')
                            ->maxLength(100),

                        TextInput::make('contact_person')
                            ->maxLength(100),

                        TextInput::make('phone')
                            ->tel()
                            ->maxLength(50),

                        TextInput::make('email')
                            ->email()
                            ->maxLength(150),

                    ]),

                Section::make('Additional Information')
                    ->schema([

                        Textarea::make('remarks')
                            ->rows(4)
                            ->columnSpanFull(),

                    ]),

            ]);

    }
}