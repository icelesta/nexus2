<?php

declare(strict_types=1);

namespace App\Filament\Resources\WarehouseTypes\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;

use Filament\Tables\Table;

use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;

class WarehouseTypesTable
{
    public static function configure(Table $table): Table
    {
        return $table

            ->defaultSort('sort_order')

            ->columns([

                TextColumn::make('company.company_name')
                    ->label('Company')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('type_code')
                    ->label('Code')
                    ->badge()
                    ->color('primary')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('type_name')
                    ->label('Warehouse Type')
                    ->description(fn ($record) => $record->description)
                    ->searchable()
                    ->sortable(),

                IconColumn::make('allow_purchase')
                    ->label('Purchase')
                    ->boolean(),

                IconColumn::make('allow_sales')
                    ->label('Sales')
                    ->boolean(),

                IconColumn::make('allow_transfer')
                    ->label('Transfer')
                    ->boolean(),

                IconColumn::make('allow_production')
                    ->label('Production')
                    ->boolean(),

                TextColumn::make('sort_order')
                    ->label('Sort')
                    ->alignCenter()
                    ->sortable(),

                IconColumn::make('is_default')
                    ->label('Default')
                    ->boolean(),

                IconColumn::make('is_active')
                    ->label('Active')
                    ->boolean(),

                TextColumn::make('created_at')
                    ->label('Created')
                    ->dateTime('d M Y H:i')
                    ->sortable()
                    ->toggleable(),

                TextColumn::make('creator.name')
                    ->label('Created By')
                    ->toggleable(
                        isToggledHiddenByDefault: true
                    ),

                TextColumn::make('updated_at')
                    ->label('Updated')
                    ->dateTime('d M Y H:i')
                    ->sortable()
                    ->toggleable(),

                TextColumn::make('updater.name')
                    ->label('Updated By')
                    ->toggleable(
                        isToggledHiddenByDefault: true
                    ),

            ])

            ->filters([

                //

            ])

            ->recordActions([

                ViewAction::make()
                    ->icon('heroicon-o-eye')
                    ->label('View')
                    ->tooltip('View'),

                EditAction::make()
                    ->icon('heroicon-o-pencil-square')
                    ->label('Edit')
                    ->color('warning')
                    ->tooltip('Edit'),

                DeleteAction::make()
                    ->icon('heroicon-o-trash')
                    ->label('Delete')
                    ->color('danger')
                    ->tooltip('Delete')
                    ->requiresConfirmation(),

            ])

            ->toolbarActions([

                BulkActionGroup::make([

                    DeleteBulkAction::make(),

                ]),

            ]);

    }
}