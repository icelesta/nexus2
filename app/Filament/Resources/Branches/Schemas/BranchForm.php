<?php

namespace App\Filament\Resources\Branches\Schemas;

use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Schema;

class BranchForm
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

                TextInput::make('branch_code')
                    ->label('Branch Code')
                    ->placeholder('JKT')
                    ->required()
                    ->unique(ignoreRecord: true)
                    ->maxLength(20),

                TextInput::make('branch_name')
                    ->label('Branch Name')
                    ->placeholder('Jakarta Head Office')
                    ->required()
                    ->maxLength(255),

                TextInput::make('phone')
                    ->label('Phone')
                    ->tel()
                    ->maxLength(50),

                TextInput::make('email')
                    ->label('Email')
                    ->email()
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