<?php

declare(strict_types=1);

namespace App\Filament\Resources\Brands\Tables;

use Illuminate\Database\Eloquent\Collection;

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

class BrandsTable
{
    public static function configure(Table $table): Table
    {
        return $table

            ->defaultSort('sort_order')

            ->columns([

                TextColumn::make('brand_code')
                    ->label('Code')
                    ->searchable()
                    ->sortable()
                    ->copyable()
                    ->weight('bold'),

                TextColumn::make('brand_name')
                    ->label('Brand')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('short_name')
                    ->label('Short')
                    ->badge()
                    ->color(Color::Blue)
                    ->sortable(),

                TextColumn::make('manufacturer_name')
                    ->label('Manufacturer')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('website')
                    ->label('Website')
                    ->searchable()
                    ->url(
                        fn ($record) => $record->website,
                        shouldOpenInNewTab: true,
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
                    ->icon('heroicon-o-pencil-square')
                    ->label('Edit')
                    ->tooltip('Edit')
                    ->color('warning'),

                DeleteAction::make()
                    ->icon('heroicon-o-trash')
                    ->label('Delete')
                    ->tooltip('Delete')
                    ->color('danger')
                    ->requiresConfirmation(),

                RestoreAction::make()
                    ->icon('heroicon-o-arrow-path')
                    ->label('')
                    ->tooltip('Restore')
                    ->color('success'),

                ForceDeleteAction::make()
                    ->icon('heroicon-o-trash')
                    ->label('')
                    ->tooltip('Force Delete')
                    ->color('danger')
                    ->requiresConfirmation(),

            ])

            ->toolbarActions([

                BulkActionGroup::make([

                    BulkAction::make('activate')
                        ->label('Activate')
                        ->icon('heroicon-o-check-circle')
                        ->color('success')
                        ->requiresConfirmation()
                        ->action(function (Collection $records): void {
                            $records->each->update([
                                'is_active' => true,
                            ]);
                        })
                        ->successNotificationTitle('Selected brands have been activated.')
                        ->deselectRecordsAfterCompletion(),

                    BulkAction::make('deactivate')
                        ->label('Deactivate')
                        ->icon('heroicon-o-x-circle')
                        ->color('warning')
                        ->requiresConfirmation()
                        ->action(function (Collection $records): void {
                            $records->each->update([
                                'is_active' => false,
                            ]);
                        })
                        ->successNotificationTitle('Selected brands have been deactivated.')
                        ->deselectRecordsAfterCompletion(),

                    DeleteBulkAction::make()
                        ->requiresConfirmation()
                        ->deselectRecordsAfterCompletion(),

                    RestoreBulkAction::make()
                        ->requiresConfirmation()
                        ->deselectRecordsAfterCompletion(),

                    ForceDeleteBulkAction::make()
                        ->requiresConfirmation()
                        ->deselectRecordsAfterCompletion(),

                ])

            ])

            ->emptyStateHeading('No Brand Found')

            ->emptyStateDescription(
                'Create your first Brand.'
            )

            ->striped();
    }
}