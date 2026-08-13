<?php

declare(strict_types=1);

namespace App\Filament\Resources\Warehouses\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class WarehousesTable
{
    public static function configure(Table $table): Table
    {
        return $table

            ->defaultSort('warehouse_code')

            ->columns([

                TextColumn::make('company.company_name')
                    ->label('Company')
                    ->sortable()
                    ->searchable(),

                TextColumn::make('branch.branch_name')
                    ->label('Branch')
                    ->sortable()
                    ->searchable()
                    ->placeholder('-'),

                TextColumn::make('warehouseType.type_name')
                    ->label('Type')
                    ->badge()
                    ->sortable()
                    ->placeholder('-'),

                TextColumn::make('warehouse_code')
                    ->label('Code')
                    ->searchable()
                    ->sortable()
                    ->badge()
                    ->color('primary'),

                TextColumn::make('warehouse_name')
                    ->label('Warehouse')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('city')
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

                IconColumn::make('allow_negative_stock')
                    ->label('Negative')
                    ->boolean(),

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
                    ->toggleable(isToggledHiddenByDefault: true),

                TextColumn::make('updated_at')
                    ->label('Updated')
                    ->dateTime('d M Y H:i')
                    ->sortable()
                    ->toggleable(),

                TextColumn::make('updater.name')
                    ->label('Updated By')
                    ->toggleable(isToggledHiddenByDefault: true),

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