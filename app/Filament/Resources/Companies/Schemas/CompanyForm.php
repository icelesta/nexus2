<?php

namespace App\Filament\Resources\Companies\Schemas;

use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Schema;

class CompanyForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([

                TextInput::make('company_code')
                    ->label('Company Code')
                    ->placeholder('BESM')
                    ->required()
                    ->unique(ignoreRecord: true)
                    ->maxLength(20),

                TextInput::make('company_name')
                    ->label('Company Name')
                    ->placeholder('Besmindo Group')
                    ->required()
                    ->maxLength(255),

                TextInput::make('tax_id')
                    ->label('Tax ID / NPWP')
                    ->maxLength(100),

                TextInput::make('phone')
                    ->label('Phone Number')
                    ->tel()
                    ->maxLength(50),

                TextInput::make('email')
                    ->label('Email Address')
                    ->email()
                    ->maxLength(255),

                TextInput::make('website')
                    ->label('Website')
                    ->url()
                    ->placeholder('https://www.company.com')
                    ->maxLength(255),

                Textarea::make('address')
                    ->label('Address')
                    ->rows(4)
                    ->columnSpanFull(),

                Toggle::make('is_active')
                    ->label('Active')
                    ->default(true),

            ]);
    }
}