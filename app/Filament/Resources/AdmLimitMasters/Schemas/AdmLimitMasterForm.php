<?php

declare(strict_types=1);

namespace App\Filament\Resources\AdmLimitMasters\Schemas;

use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Schema;

class AdmLimitMasterForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([

                TextInput::make('limit_amount')
                    ->label('Limit Amount')
                    ->required()
                    ->numeric()
                    ->prefix('Rp')
                    ->default(1000000)
                    ->minValue(0)
                    ->step(0.01),

                Toggle::make('is_active')
                    ->label('Active')
                    ->default(true),

                Textarea::make('description')
                    ->label('Description')
                    ->rows(3)
                    ->maxLength(255),

            ]);
    }
}