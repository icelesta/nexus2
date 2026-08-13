<?php

declare(strict_types=1);

namespace App\Filament\Resources\TransactionNumberings\Schemas;

use App\Models\TransactionNumbering;
use Filament\Forms\Components\Placeholder;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class TransactionNumberingForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema->components([

            /*
            |--------------------------------------------------------------------------
            | Organization
            |--------------------------------------------------------------------------
            */

            Section::make('Organization')
                ->columns(3)
                ->schema([

                    Select::make('company_id')
                        ->label('Company')
                        ->relationship('company', 'company_name')
                        ->searchable()
                        ->preload()
                        ->required(),

                    Select::make('business_unit_id')
                        ->label('Business Unit')
                        ->relationship('businessUnit', 'business_unit_name')
                        ->searchable()
                        ->preload()
                        ->nullable(),

                    Select::make('branch_id')
                        ->label('Branch')
                        ->relationship('branch', 'branch_name')
                        ->searchable()
                        ->preload()
                        ->nullable(),

                ]),

            /*
            |--------------------------------------------------------------------------
            | Document
            |--------------------------------------------------------------------------
            */

            Section::make('Document')
                ->columns(2)
                ->schema([

                    Select::make('module')
                        ->label('Module')
                        ->required()
                        ->native(false)
                        ->searchable()
                        ->options([

                            TransactionNumbering::MODULE_PROCUREMENT
                                => 'Procurement',

                            TransactionNumbering::MODULE_INVENTORY
                                => 'Inventory',

                            TransactionNumbering::MODULE_FINANCE
                                => 'Finance',

                        ]),

                    TextInput::make('document_type')
                        ->label('Document Type')
                        ->required()
                        ->maxLength(100),

                    TextInput::make('document_name')
                        ->label('Document Name')
                        ->required()
                        ->maxLength(150),

                    TextInput::make('prefix')
                        ->label('Prefix')
                        ->required()
                        ->maxLength(30),

                    TextInput::make('suffix')
                        ->label('Suffix')
                        ->maxLength(30),

                ]),

            /*
            |--------------------------------------------------------------------------
            | Number Configuration
            |--------------------------------------------------------------------------
            */

            Section::make('Number Configuration')
                ->columns(5)
                ->schema([

                    TextInput::make('number_separator')
                        ->label('Separator')
                        ->default('/')
                        ->required()
                        ->maxLength(5),

                    TextInput::make('running_digits')
                        ->label('Running Digits')
                        ->numeric()
                        ->default(6)
                        ->minValue(1)
                        ->required(),

                    TextInput::make('start_number')
                        ->label('Start Number')
                        ->numeric()
                        ->default(1)
                        ->minValue(1)
                        ->required(),

                    TextInput::make('current_number')
                        ->label('Current Number')
                        ->numeric()
                        ->default(0)
                        ->minValue(0)
                        ->required(),

                    Select::make('reset_type')
                        ->label('Reset Type')
                        ->required()
                        ->native(false)
                        ->default(TransactionNumbering::RESET_MONTHLY)
                        ->options([

                            TransactionNumbering::RESET_NEVER
                                => 'Never',

                            TransactionNumbering::RESET_DAILY
                                => 'Daily',

                            TransactionNumbering::RESET_MONTHLY
                                => 'Monthly',

                            TransactionNumbering::RESET_YEARLY
                                => 'Yearly',

                        ]),

                ]),

            /*
            |--------------------------------------------------------------------------
            | Format
            |--------------------------------------------------------------------------
            */

            Section::make('Format')
                ->schema([

                    TextInput::make('format_pattern')
                        ->label('Format Pattern')
                        ->required()
                        ->default('{PREFIX}/{YYYY}/{MM}/{RUNNING}')
                        ->helperText(
                            'Example : {PREFIX}/{YYYY}/{MM}/{RUNNING}'
                        ),

                    Placeholder::make('preview')
                        ->label('Preview')
                        ->content(function ($get): string {

                            $separator = $get('number_separator') ?: '/';

                            $prefix = strtoupper(
                                $get('prefix') ?: 'DOC'
                            );

                            $suffix = strtoupper(
                                $get('suffix') ?: ''
                            );

                            $digits = (int) (
                                $get('running_digits') ?: 6
                            );

                            $running = str_pad(
                                '1',
                                $digits,
                                '0',
                                STR_PAD_LEFT
                            );

                            $pattern = $get('format_pattern')
                                ?: '{PREFIX}/{YYYY}/{MM}/{RUNNING}';

                            return strtr($pattern, [

                                '{PREFIX}' => $prefix,

                                '{SUFFIX}' => $suffix,

                                '{YYYY}' => now()->format('Y'),

                                '{YY}' => now()->format('y'),

                                '{MM}' => now()->format('m'),

                                '{DD}' => now()->format('d'),

                                '{RUNNING}' => $running,

                            ]);

                        }),

                ]),

            /*
            |--------------------------------------------------------------------------
            | Additional Information
            |--------------------------------------------------------------------------
            */

            Section::make('Additional Information')
                ->schema([

                    Textarea::make('remarks')
                        ->rows(4)
                        ->columnSpanFull(),

                    Toggle::make('is_active')
                        ->label('Active')
                        ->default(true),

                ]),

        ]);
    }
}