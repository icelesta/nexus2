<?php

namespace App\Filament\Resources\PaymentTerms\Schemas;

use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class PaymentTermForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema

            ->components([

                Section::make('General Information')

                    ->schema([

                        Select::make('company_id')
                            ->relationship('company', 'company_name')
                            ->searchable()
                            ->preload()
                            ->required(),

                        TextInput::make('term_code')
                            ->label('Payment Term Code')
                            ->required()
                            ->unique(ignoreRecord: true)
                            ->maxLength(30)
                            ->placeholder('NET30')
                            ->autofocus(),

                        TextInput::make('term_name')
                            ->label('Payment Term Name')
                            ->required()
                            ->maxLength(150)
                            ->placeholder('Net 30 Days'),

                        Textarea::make('description')
                            ->rows(3)
                            ->columnSpanFull(),

                    ])

                    ->columns(2),

                Section::make('Payment Rules')

                    ->schema([

                        TextInput::make('due_days')
                            ->numeric()
                            ->default(0)
                            ->minValue(0)
                            ->required(),

                        TextInput::make('grace_period_days')
                            ->label('Grace Period')
                            ->numeric()
                            ->default(0)
                            ->minValue(0),

                        TextInput::make('installment_count')
                            ->label('Installment')
                            ->numeric()
                            ->default(1)
                            ->minValue(1),

                        TextInput::make('discount_days')
                            ->label('Discount Days')
                            ->numeric()
                            ->default(0)
                            ->minValue(0),

                        TextInput::make('discount_percent')
                            ->label('Discount (%)')
                            ->numeric()
                            ->step(0.01)
                            ->default(0)
                            ->suffix('%')
                            ->minValue(0),

                        TextInput::make('down_payment_percent')
                            ->label('Down Payment (%)')
                            ->numeric()
                            ->step(0.01)
                            ->default(0)
                            ->suffix('%')
                            ->minValue(0),

                    ])

                    ->columns(3),

                Section::make('Settings')

                    ->schema([

                        TextInput::make('sort_order')
                            ->numeric()
                            ->default(1)
                            ->minValue(1)
                            ->required(),

                        Toggle::make('is_default')
                            ->label('Default Payment Term')
                            ->default(false),

                        Toggle::make('is_active')
                            ->label('Active')
                            ->default(true),

                    ])

                    ->columns(3),

                Section::make('Remarks')

                    ->schema([

                        Textarea::make('remarks')
                            ->rows(4)
                            ->columnSpanFull(),

                    ]),

            ]);
    }
}