<?php

declare(strict_types=1);

namespace App\Filament\Resources\AdmLimitMasters\Tables;

use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class AdmLimitMastersTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('id')
                    ->label('#')
                    ->sortable(),

                TextColumn::make('limit_amount')
                    ->label('Limit Amount')
                    ->money('IDR')
                    ->sortable(),

                TextColumn::make('is_active')
                    ->label('Active')
                    ->badge()
                    ->formatStateUsing(
                        fn (bool $state): string => $state
                            ? 'Active'
                            : 'Inactive'
                    )
                    ->color(
                        fn (bool $state): string => $state
                            ? 'success'
                            : 'gray'
                    ),

                TextColumn::make('description')
                    ->label('Description')
                    ->limit(60)
                    ->wrap(),

                TextColumn::make('updater.name')
                    ->label('Updated By')
                    ->placeholder('-')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('updated_at')
                    ->label('Updated At')
                    ->dateTime('d/m/Y H:i')
                    ->sortable(),
            ])
            ->defaultSort('id', 'desc')
            ->actions([]);
    }
}