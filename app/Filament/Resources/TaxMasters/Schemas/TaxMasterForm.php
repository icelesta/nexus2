<?php

namespace App\Filament\Resources\TaxMasters\Schemas;

use App\Models\ChartOfAccount;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Schema;

class TaxMasterForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema

            ->components([

                Select::make('company_id')
                    ->relationship(
                        'company',
                        'company_name'
                    )
                    ->searchable()
                    ->preload()
                    ->required(),

                TextInput::make('tax_code')
                    ->label('Tax Code')
                    ->required()
                    ->unique(ignoreRecord: true)
                    ->maxLength(20),

                TextInput::make('tax_name')
                    ->label('Tax Name')
                    ->required()
                    ->maxLength(150),

                Select::make('tax_type')
                    ->required()
                    ->options([
                        'VAT' => 'VAT',
                        'WHT' => 'Withholding Tax',
                        'SALES' => 'Sales Tax',
                        'SERVICE' => 'Service Tax',
                        'OTHER' => 'Other',
                    ]),

                TextInput::make('tax_rate')
                    ->numeric()
                    ->required()
                    ->default(0)
                    ->step('0.0001')
                    ->suffix('%'),

                Select::make('calculation_method')
                    ->required()
                    ->default('Percentage')
                    ->options([
                        'Percentage' => 'Percentage',
                        'Fixed Amount' => 'Fixed Amount',
                    ]),

                Select::make('tax_account_id')
                    ->label('Tax Account')
                    ->options(

                        ChartOfAccount::query()

                            ->orderBy('account_code')

                            ->get()

                            ->mapWithKeys(fn ($coa) => [

                                $coa->id =>
                                    "{$coa->account_code} - {$coa->account_name}"

                            ])

                    )
                    ->searchable()
                    ->preload(),

                DatePicker::make('effective_date')
                    ->native(false)
                    ->required(),

                DatePicker::make('expired_date')
                    ->native(false),

                Toggle::make('is_default')
                    ->label('Default Tax'),

                Toggle::make('is_inclusive')
                    ->label('Inclusive Tax'),

                Toggle::make('is_withholding')
                    ->label('Withholding Tax'),

                Toggle::make('is_active')
                    ->default(true),

                TextInput::make('sort_order')
                    ->numeric()
                    ->default(1)
                    ->required(),

                Textarea::make('remarks')
                    ->rows(4)
                    ->columnSpanFull(),

            ])

            ->columns(2);
    }
}