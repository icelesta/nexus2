<?php

declare(strict_types=1);

namespace App\Filament\Resources\Warehouses\Schemas;

use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class WarehouseForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema

            ->components([

                /*
                |--------------------------------------------------------------------------
                | Organization
                |--------------------------------------------------------------------------
                */

                Section::make('Organization')

                    ->schema([

                        Select::make('company_id')
                            ->relationship('company', 'company_name')
                            ->required()
                            ->searchable()
                            ->preload(),

                        Select::make('branch_id')
                            ->relationship('branch', 'branch_name')
                            ->searchable()
                            ->preload(),

                        Select::make('warehouse_type_id')
                            ->relationship('warehouseType', 'type_name')
                            ->searchable()
                            ->preload(),

                    ])

                    ->columns(3),

                /*
                |--------------------------------------------------------------------------
                | General Information
                |--------------------------------------------------------------------------
                */

                Section::make('General Information')

                    ->schema([

                        TextInput::make('warehouse_code')
                            ->label('Warehouse Code')
                            ->required()
                            ->unique(ignoreRecord: true)
                            ->maxLength(30)
                            ->placeholder('WH-JKT-001')
                            ->autofocus(),

                        TextInput::make('warehouse_name')
                            ->label('Warehouse Name')
                            ->required()
                            ->maxLength(150),

                        TextInput::make('short_name')
                            ->label('Short Name')
                            ->maxLength(50),

                        Textarea::make('description')
                            ->rows(3)
                            ->columnSpanFull(),

                    ])

                    ->columns(3),

                /*
                |--------------------------------------------------------------------------
                | Location
                |--------------------------------------------------------------------------
                */

                Section::make('Location')

                    ->schema([

                        Textarea::make('address')
                            ->rows(3)
                            ->columnSpanFull(),

                        TextInput::make('city')
                            ->maxLength(100),

                        TextInput::make('province')
                            ->maxLength(100),

                        TextInput::make('postal_code')
                            ->maxLength(20),

                        TextInput::make('country')
                            ->default('Indonesia')
                            ->maxLength(100),

                    ])

                    ->columns(2),

                /*
                |--------------------------------------------------------------------------
                | Contact
                |--------------------------------------------------------------------------
                */

                Section::make('Contact')

                    ->schema([

                        TextInput::make('contact_person')
                            ->label('Contact Person')
                            ->maxLength(150),

                        TextInput::make('phone')
                            ->tel()
                            ->maxLength(50),

                        TextInput::make('email')
                            ->email()
                            ->maxLength(150),

                    ])

                    ->columns(3),

                /*
                |--------------------------------------------------------------------------
                | Warehouse Rules
                |--------------------------------------------------------------------------
                */

                Section::make('Warehouse Rules')

                    ->schema([

                        Toggle::make('allow_purchase')
                            ->label('Allow Purchase')
                            ->default(true),

                        Toggle::make('allow_sales')
                            ->label('Allow Sales')
                            ->default(true),

                        Toggle::make('allow_transfer')
                            ->label('Allow Transfer')
                            ->default(true),

                        Toggle::make('allow_production')
                            ->label('Allow Production')
                            ->default(false),

                        Toggle::make('allow_negative_stock')
                            ->label('Allow Negative Stock')
                            ->default(false),

                    ])

                    ->columns(5),

                /*
                |--------------------------------------------------------------------------
                | Status
                |--------------------------------------------------------------------------
                */

                Section::make('Status')

                    ->schema([

                        TextInput::make('sort_order')
                            ->numeric()
                            ->default(1)
                            ->required()
                            ->minValue(1),

                        Toggle::make('is_default')
                            ->label('Default Warehouse')
                            ->default(false),

                        Toggle::make('is_active')
                            ->label('Active')
                            ->default(true),

                    ])

                    ->columns(3),

            ]);
    }
}