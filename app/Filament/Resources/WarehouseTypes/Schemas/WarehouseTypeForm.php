<?php

declare(strict_types=1);

namespace App\Filament\Resources\WarehouseTypes\Schemas;

use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class WarehouseTypeForm
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
                            ->label('Company')
                            ->relationship('company', 'company_name')
                            ->required()
                            ->searchable()
                            ->preload(),

                    ])

                    ->columns(1),

                /*
                |--------------------------------------------------------------------------
                | General Information
                |--------------------------------------------------------------------------
                */

                Section::make('General Information')

                    ->schema([

                        TextInput::make('type_code')
                            ->label('Warehouse Type Code')
                            ->required()
                            ->unique(ignoreRecord: true)
                            ->maxLength(30)
                            ->live(onBlur: true)
                            ->afterStateUpdated(fn ($state, $set) =>
                                $set('type_code', strtoupper($state ?? '')))
                            ->placeholder('RAW'),

                        TextInput::make('type_name')
                            ->label('Warehouse Type Name')
                            ->required()
                            ->maxLength(100)
                            ->placeholder('Raw Material'),

                        Textarea::make('description')
                            ->rows(4)
                            ->columnSpanFull(),

                    ])

                    ->columns(2),

                /*
                |--------------------------------------------------------------------------
                | Business Rules
                |--------------------------------------------------------------------------
                */

                Section::make('Business Rules')

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

                    ])

                    ->columns(4),

                /*
                |--------------------------------------------------------------------------
                | Status
                |--------------------------------------------------------------------------
                */

                Section::make('Status')

                    ->schema([

                        TextInput::make('sort_order')
                            ->numeric()
                            ->required()
                            ->default(1)
                            ->minValue(1),

                        Toggle::make('is_default')
                            ->label('Default Warehouse Type')
                            ->default(false),

                        Toggle::make('is_active')
                            ->label('Active')
                            ->default(true),

                    ])

                    ->columns(3),

            ]);
    }
}