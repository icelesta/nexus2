<?php

declare(strict_types=1);

namespace App\Filament\Resources\Uoms\Tables;

use Filament\Actions\BulkAction;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Actions\ForceDeleteBulkAction;
use Filament\Actions\RestoreAction;
use Filament\Actions\RestoreBulkAction;
use Filament\Actions\DeleteAction;
use Filament\Actions\ForceDeleteAction;

use Filament\Support\Colors\Color;

use Filament\Tables\Table;

use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\IconColumn;

use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Filters\TernaryFilter;
use Filament\Tables\Filters\TrashedFilter;

class UomsTable
{
    public static function configure(Table $table): Table
    {
        return $table

            ->defaultSort('sort_order')

            ->columns([

                TextColumn::make('uom_code')
                    ->label('Code')
                    ->searchable()
                    ->sortable()
                    ->copyable()
                    ->weight('bold'),

                TextColumn::make('uom_name')
                    ->label('Unit of Measure')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('symbol')
                    ->badge()
                    ->color(Color::Blue)
                    ->sortable(),

                TextColumn::make('category')
                    ->badge()
                    ->sortable()
                    ->searchable(),

                TextColumn::make('decimal_places')
                    ->label('Decimals')
                    ->alignCenter()
                    ->sortable(),

                IconColumn::make('allow_fraction')
                    ->label('Fraction')
                    ->boolean()
                    ->alignCenter(),

                IconColumn::make('is_base')
                    ->label('Base')
                    ->boolean()
                    ->alignCenter(),

                TextColumn::make('sort_order')
                    ->label('Order')
                    ->alignCenter()
                    ->sortable()
                    ->toggleable(),

                IconColumn::make('is_active')
                    ->label('Status')
                    ->boolean()
                    ->alignCenter(),

                TextColumn::make('creator.name')
                    ->label('Created By')
                    ->toggleable(isToggledHiddenByDefault: true),

                TextColumn::make('updater.name')
                    ->label('Updated By')
                    ->toggleable(isToggledHiddenByDefault: true),

                TextColumn::make('created_at')
                    ->label('Created')
                    ->since()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),

                TextColumn::make('updated_at')
                    ->label('Updated')
                    ->since()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),

                TextColumn::make('deleted_at')
                    ->label('Deleted')
                    ->since()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),

            ])

            ->filters([

                SelectFilter::make('category')
                    ->options([
                        'Quantity' => 'Quantity',
                        'Weight' => 'Weight',
                        'Length' => 'Length',
                        'Area' => 'Area',
                        'Volume' => 'Volume',
                        'Time' => 'Time',
                        'Temperature' => 'Temperature',
                        'Pressure' => 'Pressure',
                        'Energy' => 'Energy',
                    ]),

                TernaryFilter::make('is_active')
                    ->label('Status'),

                TernaryFilter::make('is_base')
                    ->label('Base UOM'),

                TrashedFilter::make(),

            ])

                ->recordActions([
                    ViewAction::make(),
                    EditAction::make(),
                    DeleteAction::make(),
                    RestoreAction::make(),
                    ForceDeleteAction::make(),
            ])
            
            ->toolbarActions([

                BulkActionGroup::make([

                    BulkAction::make('activate')
                        ->icon('heroicon-o-check-circle')
                        ->color('success')
                        ->requiresConfirmation()
                        ->action(fn ($records) => $records->each->update([
                            'is_active' => true,
                        ])),

                    BulkAction::make('deactivate')
                        ->icon('heroicon-o-x-circle')
                        ->color('warning')
                        ->requiresConfirmation()
                        ->action(fn ($records) => $records->each->update([
                            'is_active' => false,
                        ])),

                    DeleteBulkAction::make(),

                    RestoreBulkAction::make(),

                    ForceDeleteBulkAction::make(),

                ])

            ])

            ->emptyStateHeading('No Unit of Measure Found')

            ->emptyStateDescription(
                'Create your first Unit of Measure.'
            )

            ->striped();
    }
}