<?php

namespace App\Filament\Resources\JournalTypes\Schemas;

use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class JournalTypeForm
{
    public static function configure(
        Schema $schema
    ): Schema {
        return $schema
            ->components([

                Section::make('Journal Information')
                    ->description('Define journal type information.')
                    ->columns(2)
                    ->schema([

                        TextInput::make('code')
                            ->label('Journal Code')
                            ->required()
                            ->unique(ignoreRecord: true)
                            ->maxLength(20)
                            ->placeholder('GJ')
                            ->autocomplete(false)
                            ->helperText('Unique journal code.'),

                        TextInput::make('name')
                            ->label('Journal Name')
                            ->required()
                            ->maxLength(100)
                            ->placeholder('General Journal'),

                        Textarea::make('description')
                            ->label('Description')
                            ->rows(3)
                            ->columnSpanFull()
                            ->placeholder('Optional description of this journal type.'),

                        Toggle::make('is_system')
                            ->label('System Journal')
                            ->default(false)
                            ->helperText('Protected journal used internally by the system.'),

                        Toggle::make('is_active')
                            ->label('Active')
                            ->default(true)
                            ->helperText('Enable or disable this journal type.'),

                    ]),

            ]);
    }
}