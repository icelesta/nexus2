<?php

namespace App\Filament\Resources\BusinessUnits\Schemas;

use Filament\Schemas\Schema;
use Filament\Schemas\Components\Section;

use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\Toggle;

class BusinessUnitForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema->components([

            Section::make('General Information')
                ->columns(2)
                ->schema([

                    Select::make('company_id')
                        ->label('Company')
                        ->relationship(
                            name: 'company',
                            titleAttribute: 'company_name',
                            modifyQueryUsing: function ($query, $record) {

                                $query->where(function ($q) use ($record) {

                                    $q->where('is_active', true);

                                    if ($record?->company_id) {
                                        $q->orWhere('id', $record->company_id);
                                    }

                                });

                            },
                        )
                        ->searchable()
                        ->preload()
                        ->placeholder('Select Company')
                        ->required(),

                    Select::make('branch_id')
                        ->label('Branch')
                        ->relationship(
                            name: 'branch',
                            titleAttribute: 'branch_name',
                            modifyQueryUsing: function ($query, $record) {

                                $query->where(function ($q) use ($record) {

                                    $q->where('is_active', true);

                                    if ($record?->branch_id) {
                                        $q->orWhere('id', $record->branch_id);
                                    }

                                });

                            },
                        )
                        ->searchable()
                        ->preload()
                        ->placeholder('Select Branch')
                        ->nullable(),

                    TextInput::make('business_unit_code')
                        ->label('Business Unit Code')
                        ->required()
                        ->maxLength(30)
                        ->unique(ignoreRecord: true),

                    TextInput::make('business_unit_name')
                        ->label('Business Unit Name')
                        ->required()
                        ->maxLength(150),

                    TextInput::make('short_name')
                        ->label('Short Name')
                        ->maxLength(50),

                    Select::make('manager_id')
                        ->label('Manager')
                        ->relationship('manager', 'name')
                        ->searchable()
                        ->preload()
                        ->placeholder('Select Manager')
                        ->nullable(),

                ]),

            Section::make('Contact Information')
                ->columns(2)
                ->schema([

                    TextInput::make('phone')
                        ->label('Phone')
                        ->tel()
                        ->maxLength(50),

                    TextInput::make('email')
                        ->label('Email')
                        ->email()
                        ->maxLength(150),

                    Textarea::make('address')
                        ->label('Address')
                        ->rows(3)
                        ->columnSpanFull(),

                ]),

            Section::make('Configuration')
                ->columns(3)
                ->schema([

                    TextInput::make('sort_order')
                        ->label('Sort Order')
                        ->numeric()
                        ->default(0)
                        ->minValue(0)
                        ->required(),

                    Toggle::make('is_default')
                        ->label('Default Business Unit')
                        ->default(false),

                    Toggle::make('is_active')
                        ->label('Active')
                        ->default(true),

                ]),

            Section::make('Remarks')
                ->schema([

                    Textarea::make('remarks')
                        ->label('Remarks')
                        ->rows(4)
                        ->columnSpanFull(),

                ]),

        ]);
    }
}