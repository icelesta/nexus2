<?php

namespace App\Filament\Resources\CostCenters\Schemas;

use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Schema;

class CostCenterForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([

                Select::make('company_id')
                    ->relationship('company', 'company_name')
                    ->searchable()
                    ->preload()
                    ->required(),

                Select::make('branch_id')
                    ->relationship('branch', 'branch_name')
                    ->searchable()
                    ->preload()
                    ->required(),

                Select::make('business_unit_id')
                    ->relationship('businessUnit', 'business_unit_name')
                    ->searchable()
                    ->preload()
                    ->required(),

                Select::make('department_id')
                    ->relationship('department', 'department_name')
                    ->searchable()
                    ->preload()
                    ->required(),

                Select::make('section_id')
                    ->relationship('section', 'section_name')
                    ->searchable()
                    ->preload()
                    ->required(),

                TextInput::make('cost_center_code')
                    ->label('Cost Center Code')
                    ->required()
                    ->unique(ignoreRecord: true)
                    ->maxLength(20),

                TextInput::make('cost_center_name')
                    ->label('Cost Center Name')
                    ->required()
                    ->maxLength(255),

                Textarea::make('description')
                    ->rows(3)
                    ->columnSpanFull(),

                Toggle::make('is_active')
                    ->label('Active')
                    ->default(true),

            ]);
    }
}