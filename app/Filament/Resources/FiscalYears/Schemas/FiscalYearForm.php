<?php

namespace App\Filament\Resources\FiscalYears\Schemas;

use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Schema;
use Illuminate\Validation\Rule;

class FiscalYearForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([

                Select::make('company_id')
                    ->label('Company')
                    ->relationship('company', 'company_name')
                    ->searchable()
                    ->preload()
                    ->required(),

                TextInput::make('fiscal_code')
                    ->label('Fiscal Year Code')
                    ->required()
                    ->maxLength(20)
                    ->unique(
                        table: 'fiscal_years',
                        column: 'fiscal_code',
                        ignoreRecord: true,
                    ),

                TextInput::make('fiscal_name')
                    ->label('Fiscal Year Name')
                    ->required()
                    ->maxLength(100),

                DatePicker::make('start_date')
                    ->required()
                    ->native(false),

                DatePicker::make('end_date')
                    ->required()
                    ->native(false)
                    ->after('start_date'),

                Toggle::make('is_active')
                    ->label('Active')
                    ->default(true),

            ]);
    }
}