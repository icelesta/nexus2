<?php

declare(strict_types=1);

namespace App\Filament\Resources\ItemCategories\Tables;

use Filament\Actions\BulkAction;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ForceDeleteAction;
use Filament\Actions\ForceDeleteBulkAction;
use Filament\Actions\RestoreAction;
use Filament\Actions\RestoreBulkAction;
use Filament\Actions\ViewAction;

use Filament\Tables\Table;

use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;

use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Filters\TernaryFilter;
use Filament\Tables\Filters\TrashedFilter;

use Illuminate\Database\Eloquent\Collection;

class ItemCategoriesTable
{
    public static function configure(Table $table): Table
    {
        return $table

            ->defaultSort('sort_order')

            ->columns([

                TextColumn::make('category_code')
                    ->label('Code')
                    ->searchable()
                    ->sortable()
                    ->copyable()
                    ->weight('bold'),

                TextColumn::make('category_name')
                    ->label('Category')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('parent.category_name')
                    ->label('Parent Category')
                    ->placeholder('-')
                    ->sortable(),

                TextColumn::make('sort_order')
                    ->label('Order')
                    ->sortable()
                    ->alignCenter(),

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

                SelectFilter::make('parent_id')
                    ->relationship(
                        'parent',
                        'category_name'
                    )
                    ->label('Parent Category'),

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
                        ->label('Activate')
                        ->icon('heroicon-o-check-circle')
                        ->color('success')
                        ->requiresConfirmation()
                        ->action(function (Collection $records): void {
                            $records->each->update([
                                'is_active' => true,
                            ]);
                        }),

                    BulkAction::make('deactivate')
                        ->label('Deactivate')
                        ->icon('heroicon-o-x-circle')
                        ->color('warning')
                        ->requiresConfirmation()
                        ->action(function (Collection $records): void {
                            $records->each->update([
                                'is_active' => false,
                            ]);
                        }),

                    DeleteBulkAction::make(),

                    RestoreBulkAction::make(),

                    ForceDeleteBulkAction::make(),

                ])

            ])

            ->emptyStateHeading('No Item Categories Found')

            ->emptyStateDescription(
                'Create your first Item Category.'
            )

            ->striped();
    }
}