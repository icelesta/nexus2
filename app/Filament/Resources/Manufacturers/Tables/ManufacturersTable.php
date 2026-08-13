<?php

declare(strict_types=1);

namespace App\Filament\Resources\Manufacturers\Tables;

use Filament\Actions\BulkAction;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ForceDeleteAction;
use Filament\Actions\ForceDeleteBulkAction;
use Filament\Actions\RestoreAction;
use Filament\Actions\RestoreBulkAction;

use Filament\Support\Colors\Color;

use Filament\Tables\Table;

use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;

use Filament\Tables\Filters\TernaryFilter;
use Filament\Tables\Filters\TrashedFilter;

class ManufacturersTable
{
    public static function configure(
        Table $table,
    ): Table {

        return $table

            ->defaultSort('sort_order')

            ->columns([

                TextColumn::make('manufacturer_code')
                    ->label('Code')
                    ->searchable()
                    ->sortable()
                    ->copyable()
                    ->weight('bold'),

                TextColumn::make('manufacturer_name')
                    ->label('Manufacturer')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('short_name')
                    ->label('Short Name')
                    ->badge()
                    ->color(Color::Blue)
                    ->sortable(),

                TextColumn::make('country')
                    ->label('Country')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('city')
                    ->label('City')
                    ->searchable()
                    ->toggleable(),

                TextColumn::make('website')
                    ->label('Website')
                    ->url(
                        fn ($record) => $record->website,
                        true
                    )
                    ->toggleable(),

                TextColumn::make('phone')
                    ->label('Phone')
                    ->toggleable(),

                TextColumn::make('sort_order')
                    ->label('Order')
                    ->alignCenter()
                    ->sortable(),

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

                TernaryFilter::make('is_active')
                    ->label('Status'),

                TrashedFilter::make(),

            ])

            ->recordActions([

                EditAction::make()
                    ->label(''),

                DeleteAction::make()
                    ->label(''),

                RestoreAction::make()
                    ->label(''),

                ForceDeleteAction::make()
                    ->label(''),

            ])

            ->toolbarActions([

                BulkActionGroup::make([

                    BulkAction::make('activate')
                        ->label('Activate')
                        ->icon('heroicon-o-check-circle')
                        ->color('success')
                        ->requiresConfirmation()
                        ->action(
                            fn ($records) => $records->each->update([
                                'is_active' => true,
                            ])
                        ),

                    BulkAction::make('deactivate')
                        ->label('Deactivate')
                        ->icon('heroicon-o-x-circle')
                        ->color('warning')
                        ->requiresConfirmation()
                        ->action(
                            fn ($records) => $records->each->update([
                                'is_active' => false,
                            ])
                        ),

                    DeleteBulkAction::make(),

                    RestoreBulkAction::make(),

                    ForceDeleteBulkAction::make(),

                ])

            ])

            ->emptyStateHeading(
                'No Manufacturer Found'
            )

            ->emptyStateDescription(
                'Create your first Manufacturer.'
            )

            ->striped();
    }
}