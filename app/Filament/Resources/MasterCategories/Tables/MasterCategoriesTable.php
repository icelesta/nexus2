<?php

namespace App\Filament\Resources\MasterCategories\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;

use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class MasterCategoriesTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->defaultSort('category_code')

            ->columns([

                TextColumn::make('category_code')
                    ->label('Code')
                    ->searchable()
                    ->sortable()
                    ->badge()
                    ->color('primary'),

                TextColumn::make('category_name')
                    ->label('Category Name')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('parent.category_name')
                    ->label('Parent Category')
                    ->placeholder('-')
                    ->sortable(),

                TextColumn::make('category_type')
                    ->label('Type')
                    ->badge()
                    ->sortable(),

                IconColumn::make('is_active')
                    ->label('Active')
                    ->boolean()
                    ->sortable(),

                TextColumn::make('creator.name')
                    ->label('Created By')
                    ->placeholder('-')
                    ->toggleable(),

                TextColumn::make('created_at')
                    ->label('Created Date')
                    ->dateTime('d M Y H:i')
                    ->sortable()
                    ->toggleable(),

                TextColumn::make('updater.name')
                    ->label('Updated By')
                    ->placeholder('-')
                    ->toggleable(),

                TextColumn::make('updated_at')
                    ->label('Updated Date')
                    ->dateTime('d M Y H:i')
                    ->sortable()
                    ->toggleable(),

            ])

            ->filters([
                //
            ])

            ->recordActions([

                ViewAction::make(),

                EditAction::make(),

                DeleteAction::make()
                    ->requiresConfirmation(),

            ])

            ->toolbarActions([

                BulkActionGroup::make([

                    DeleteBulkAction::make(),

                ]),

            ]);
    }
}