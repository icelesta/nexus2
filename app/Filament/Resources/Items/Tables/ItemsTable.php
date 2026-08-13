<?php

declare(strict_types=1);

namespace App\Filament\Resources\Items\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\DeleteAction;
use Filament\Actions\RestoreAction;
use Filament\Actions\ForceDeleteAction;
use Filament\Actions\ViewAction;

use Filament\Support\Colors\Color;

use Filament\Tables\Table;

use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;

use Filament\Tables\Filters\TernaryFilter;
use Filament\Tables\Filters\TrashedFilter;
use Filament\Tables\Columns\ViewColumn;

class ItemsTable
{
    public static function configure(
        Table $table,
    ): Table {

        return $table

            ->defaultSort('item_code')

            ->columns([

                /*
                |--------------------------------------------------------------------------
                | Item Thumbnail
                |--------------------------------------------------------------------------
                |
                | Nexus ERP Enterprise Preview
                |
                */

                ViewColumn::make('thumbnail')
                    ->label('')
                    ->view('filament.tables.item-thumbnail'),

                /*
                |--------------------------------------------------------------------------
                | Item
                |--------------------------------------------------------------------------
                */

                TextColumn::make('item_code')
                    ->label('Item Code')
                    ->searchable()
                    ->sortable()
                    ->copyable()
                    ->weight('bold'),

                TextColumn::make('item_name')
                    ->label('Item Name')
                    ->searchable()
                    ->sortable()
                    ->wrap(),

                /*
                |--------------------------------------------------------------------------
                | Classification
                |--------------------------------------------------------------------------
                */

                TextColumn::make('category.category_name')
                    ->label('Category')
                    ->badge()
                    ->color(Color::Blue)
                    ->sortable(),

                TextColumn::make('brand.brand_name')
                    ->label('Brand')
                    ->toggleable(),

                TextColumn::make('manufacturer.manufacturer_name')
                    ->label('Manufacturer')
                    ->toggleable(
                        isToggledHiddenByDefault: true
                    ),

                /*
                |--------------------------------------------------------------------------
                | Engineering
                |--------------------------------------------------------------------------
                */

                TextColumn::make('part_number')
                    ->label('Part Number')
                    ->searchable()
                    ->toggleable(
                        isToggledHiddenByDefault: true
                    ),

                TextColumn::make('drawing_number')
                    ->label('Drawing')
                    ->toggleable(
                        isToggledHiddenByDefault: true
                    ),

                /*
                |--------------------------------------------------------------------------
                | UOM
                |--------------------------------------------------------------------------
                */

                TextColumn::make('baseUom.uom_name')
                    ->label('UOM')
                    ->badge()
                    ->color(Color::Gray),

                /*
                |--------------------------------------------------------------------------
                | Warehouse
                |--------------------------------------------------------------------------
                */

                TextColumn::make('warehouse.warehouse_name')
                    ->label('Warehouse')
                    ->toggleable(),

                /*
                |--------------------------------------------------------------------------
                | Inventory
                |--------------------------------------------------------------------------
                */

                TextColumn::make('stocks_sum_qty_on_hand')
                    ->label('Current Stock')
                    ->alignCenter()
                    ->sortable(),

                /*
                |--------------------------------------------------------------------------
                | Purchasing
                |--------------------------------------------------------------------------
                */

                TextColumn::make('preferred_supplier')
                    ->label('Preferred Supplier')

                    ->state(function ($record) {

                        return optional(

                            $record->suppliers()

                                ->where('is_preferred', true)

                                ->first()

                        )->supplier?->supplier_name;

                    })

                    ->placeholder('-')

                    ->toggleable(),

                /*
                |--------------------------------------------------------------------------
                | Status
                |--------------------------------------------------------------------------
                */

                IconColumn::make('is_inventory')
                    ->label('Inv')
                    ->boolean(),

                IconColumn::make('is_serialized')
                    ->label('Serial')
                    ->boolean(),

                IconColumn::make('is_batch_tracked')
                    ->label('Batch')
                    ->boolean(),

                IconColumn::make('is_active')
                    ->label('Active')
                    ->boolean(),

                /*
                |--------------------------------------------------------------------------
                | Audit
                |--------------------------------------------------------------------------
                */

                TextColumn::make('creator.name')
                    ->label('Created By')
                    ->toggleable(
                        isToggledHiddenByDefault: true
                    ),

                TextColumn::make('updater.name')
                    ->label('Updated By')
                    ->toggleable(
                        isToggledHiddenByDefault: true
                    ),

                TextColumn::make('created_at')
                    ->since()
                    ->toggleable(
                        isToggledHiddenByDefault: true
                    ),

                TextColumn::make('updated_at')
                    ->since()
                    ->toggleable(
                        isToggledHiddenByDefault: true
                    ),

                TextColumn::make('deleted_at')
                    ->since()
                    ->toggleable(
                        isToggledHiddenByDefault: true
                    ),

            ])




            ->filters([

                TernaryFilter::make('is_active')
                    ->label('Active'),

                TernaryFilter::make('is_inventory')
                    ->label('Inventory'),

                TernaryFilter::make('is_serialized')
                    ->label('Serialized'),

                TernaryFilter::make('is_batch_tracked')
                    ->label('Batch'),

                TrashedFilter::make(),

            ])

            ->recordActions([

                ViewAction::make()
                    ->icon('heroicon-o-eye')
                    ->tooltip('View'),

                EditAction::make()
                    ->icon('heroicon-o-pencil-square')
                    ->color('warning')
                    ->tooltip('Edit'),

                DeleteAction::make()
                    ->requiresConfirmation(),

                RestoreAction::make(),

                ForceDeleteAction::make()
                    ->requiresConfirmation(),

            ])

            ->toolbarActions([

                BulkActionGroup::make([

                    DeleteBulkAction::make()
                        ->requiresConfirmation(),

                ])

            ])

            ->emptyStateHeading(
                'No Inventory Item'
            )

            ->emptyStateDescription(
                'Create your first Item Master for Inventory.'
            )

            ->striped();
    }
}