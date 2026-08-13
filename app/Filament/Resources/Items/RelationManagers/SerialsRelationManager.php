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

use Filament\Forms\Components\DatePicker;
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

use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Filters\TrashedFilter;

class SerialsRelationManager extends RelationManager
{
    protected static string $relationship = 'serials';

    protected static ?string $title = 'Serial Numbers';

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

                        TextInput::make('serial_number')

                            ->label('Serial Number')

                            ->required()

                            ->unique(
                                table: 'item_serials',
                                column: 'serial_number',
                                ignoreRecord: true,
                            )

                            ->maxLength(100),

                        Select::make('warehouse_id')

                            ->label('Warehouse')

                            ->relationship(
                                'warehouse',
                                'warehouse_name'
                            )

                            ->required()

                            ->searchable()

                            ->preload(),

                        Select::make('status')

                            ->required()

                            ->default('Available')

                            ->options([

                                'Available' => 'Available',

                                'Reserved' => 'Reserved',

                                'Issued' => 'Issued',

                                'Returned' => 'Returned',

                                'Repair' => 'Repair',

                                'Scrap' => 'Scrap',

                            ]),

                        DatePicker::make('purchase_date')

                            ->label('Purchase Date'),

                        DatePicker::make('manufacture_date')

                            ->label('Manufacture Date'),

                        DatePicker::make('expiry_date')

                            ->label('Expiry Date'),

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

            ->defaultSort('serial_number')

            ->columns([

                TextColumn::make('serial_number')

                    ->label('Serial Number')

                    ->searchable()

                    ->sortable()

                    ->copyable()

                    ->weight('bold'),

                TextColumn::make('warehouse.warehouse_name')

                    ->label('Warehouse')

                    ->badge()

                    ->color(Color::Blue)

                    ->sortable(),

                TextColumn::make('status')

                    ->badge()

                    ->sortable(),

                TextColumn::make('purchase_date')

                    ->date(),

                TextColumn::make('expiry_date')

                    ->date(),

                IconColumn::make('is_active')

                    ->label('Status')

                    ->boolean()

                    ->alignCenter(),

                TextColumn::make('created_at')

                    ->since()

                    ->toggleable(
                        isToggledHiddenByDefault: true,
                    ),

                TextColumn::make('updated_at')

                    ->since()

                    ->toggleable(
                        isToggledHiddenByDefault: true,
                    ),

            ])

            ->filters([

                SelectFilter::make('status')

                    ->options([

                        'Available' => 'Available',

                        'Reserved' => 'Reserved',

                        'Issued' => 'Issued',

                        'Returned' => 'Returned',

                        'Repair' => 'Repair',

                        'Scrap' => 'Scrap',

                    ]),

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
                'No Serial Number Found'
            )

            ->emptyStateDescription(
                'Create the first serial number.'
            )

            ->striped();
    }
}