<?php

declare(strict_types=1);

namespace App\Filament\Resources\Warehouses\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Actions\ActionGroup;

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
                    ->label('Create Date')
                    ->dateTime('d M Y H:i')
                    ->sortable(),

                TextColumn::make('creator.name')
                    ->label('Created By')
                    ->sortable(),

                TextColumn::make('updated_at')
                    ->label('Update Date')
                    ->dateTime('d M Y H:i')
                    ->sortable(),

                TextColumn::make('updater.name')
                    ->label('Updated By')
                    ->sortable(),

            ])

            ->filters([

                //

            ])

            ->recordActions([
                ActionGroup::make([
                    ViewAction::make()
                        ->label('View'),

                    EditAction::make()
                        ->label('Edit'),

                    DeleteAction::make()
                        ->label('Delete')
                        ->requiresConfirmation(),
                ])
                    ->label('Actions')
                    ->button()
                    ->color('warning')
                    ->icon('heroicon-m-ellipsis-vertical'),
            ])

            ->toolbarActions([

                BulkActionGroup::make([

                    DeleteBulkAction::make(),

                ]),

            ]);
    }
}