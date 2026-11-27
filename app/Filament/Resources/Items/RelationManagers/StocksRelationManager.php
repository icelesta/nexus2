<?php

declare(strict_types=1);

namespace App\Filament\Resources\Items\RelationManagers;

use Filament\Actions\ViewAction;
use Filament\Forms\Components\Grid;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class StocksRelationManager extends RelationManager
{
    protected static string $relationship = 'stocks';

    protected static ?string $title = 'Warehouse Stock';

    /*
    |--------------------------------------------------------------------------
    | Form - Read Only
    |--------------------------------------------------------------------------
    */

    public function form(Schema $schema): Schema
    {
        return $schema->components([
            Grid::make(2)->schema([

                Select::make('warehouse_id')
                    ->label('Warehouse')
                    ->relationship('warehouse', 'warehouse_name')
                    ->disabled(),

                TextInput::make('qty_on_hand')
                    ->label('On Hand Qty')
                    ->numeric()
                    ->disabled(),

                TextInput::make('qty_reserved')
                    ->label('Reserved Qty')
                    ->numeric()
                    ->disabled(),

                TextInput::make('qty_available')
                    ->label('Available Qty')
                    ->numeric()
                    ->disabled(),

                TextInput::make('last_stock_take')
                    ->label('Last Stock Take')
                    ->disabled(),

            ]),
        ]);
    }

    /*
    |--------------------------------------------------------------------------
    | Table - Read Only
    |--------------------------------------------------------------------------
    */

    public function table(Table $table): Table
    {
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
                    ->numeric(decimalPlaces: 4)
                    ->sortable()
                    ->badge()
                    ->color('info'),

                TextColumn::make('qty_reserved')
                    ->label('Reserved')
                    ->numeric(decimalPlaces: 4)
                    ->sortable()
                    ->badge()
                    ->color('warning'),

                TextColumn::make('qty_available')
                    ->label('Available')
                    ->numeric(decimalPlaces: 4)
                    ->sortable()
                    ->badge()
                    ->color('success'),

                TextColumn::make('last_stock_take')
                    ->label('Last Stock Take')
                    ->date()
                    ->placeholder('-'),

            ])

            ->recordActions([
                ViewAction::make(),
            ])

            ->emptyStateHeading('No Warehouse Stock Found')

            ->emptyStateDescription(
                'Warehouse stock will appear here after inventory transactions.'
            )

            ->striped();
    }
}