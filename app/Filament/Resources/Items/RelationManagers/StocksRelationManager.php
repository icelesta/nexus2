<?php

declare(strict_types=1);

namespace App\Filament\Resources\Items\RelationManagers;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\CreateAction;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ForceDeleteAction;
use Filament\Actions\RestoreAction;
use Filament\Actions\ViewAction;

use Filament\Forms\Components\Grid;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;

use Filament\Resources\RelationManagers\RelationManager;

use Filament\Schemas\Schema;

use Filament\Support\Colors\Color;

use Filament\Tables\Table;

use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;

use Filament\Tables\Filters\TrashedFilter;

class StocksRelationManager extends RelationManager
{
    protected static string $relationship = 'stocks';

    protected static ?string $title = 'Warehouse Stock';

    /*
    |--------------------------------------------------------------------------
    | Form
    |--------------------------------------------------------------------------
    */

    public function form(
        Schema $schema,
    ): Schema {

        return $schema

            ->components([

                Grid::make(2)

                    ->schema([

                        Select::make('warehouse_id')

                            ->label('Warehouse')

                            ->relationship(
                                'warehouse',
                                'warehouse_name'
                            )

                            ->required()

                            ->searchable()

                            ->preload(),

                        TextInput::make('qty_on_hand')

                            ->label('On Hand Qty')

                            ->numeric()

                            ->default(0)

                            ->required(),

                        TextInput::make('qty_reserved')

                            ->label('Reserved Qty')

                            ->numeric()

                            ->default(0),

                        TextInput::make('qty_available')

                            ->label('Available Qty')

                            ->numeric()

                            ->disabled(),

                        TextInput::make('minimum_stock')

                            ->label('Minimum Stock')

                            ->numeric()

                            ->default(0),

                        TextInput::make('maximum_stock')

                            ->label('Maximum Stock')

                            ->numeric()

                            ->default(0),

                        Toggle::make('is_active')

                            ->label('Active')

                            ->default(true),

                    ])

            ]);
    }

    /*
    |--------------------------------------------------------------------------
    | Table
    |--------------------------------------------------------------------------
    */

    public function table(
        Table $table,
    ): Table {

        return $table

            ->defaultSort('warehouse.warehouse_name')

            ->columns([

                TextColumn::make('warehouse.warehouse_name')

                    ->label('Warehouse')

                    ->searchable()

                    ->sortable()

                    ->weight('bold'),

                TextColumn::make('qty_on_hand')

                    ->label('On Hand')

                    ->numeric()

                    ->sortable()

                    ->badge()

                    ->color(Color::Blue),

                TextColumn::make('qty_reserved')

                    ->label('Reserved')

                    ->numeric()

                    ->sortable()

                    ->badge()

                    ->color(Color::Orange),

                TextColumn::make('qty_available')

                    ->label('Available')

                    ->numeric()

                    ->sortable()

                    ->badge()

                    ->color(Color::Green),

                TextColumn::make('minimum_stock')

                    ->label('Min')

                    ->numeric()

                    ->alignCenter(),

                TextColumn::make('maximum_stock')

                    ->label('Max')

                    ->numeric()

                    ->alignCenter(),

                IconColumn::make('is_active')

                    ->label('Status')

                    ->boolean()

                    ->alignCenter(),

                TextColumn::make('created_at')

                    ->label('Created')

                    ->since()

                    ->toggleable(
                        isToggledHiddenByDefault: true,
                    ),

                TextColumn::make('updated_at')

                    ->label('Updated')

                    ->since()

                    ->toggleable(
                        isToggledHiddenByDefault: true,
                    ),

            ])

            ->filters([

                TrashedFilter::make(),

            ])

            ->headerActions([

                CreateAction::make(),

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

                    DeleteBulkAction::make(),

                ]),

            ])

            ->emptyStateHeading(
                'No Warehouse Stock Found'
            )

            ->emptyStateDescription(
                'Create the first warehouse stock record.'
            )

            ->striped();
    }
}