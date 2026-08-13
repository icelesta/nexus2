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
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;

use Filament\Resources\RelationManagers\RelationManager;

use Filament\Schemas\Schema;

use Filament\Support\Colors\Color;

use Filament\Tables\Table;

use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;

use Filament\Tables\Filters\TrashedFilter;

class BatchesRelationManager extends RelationManager
{
    protected static string $relationship = 'batches';

    protected static ?string $title = 'Batches';

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

                        TextInput::make('batch_number')

                            ->label('Batch Number')

                            ->required()

                            ->unique(
                                table: 'item_batches',
                                column: 'batch_number',
                                ignoreRecord: true,
                            )

                            ->maxLength(100),

                        TextInput::make('quantity')

                            ->label('Quantity')

                            ->numeric()

                            ->required()

                            ->default(0),

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

            ->defaultSort('manufacture_date', 'desc')

            ->columns([

                TextColumn::make('batch_number')

                    ->label('Batch Number')

                    ->searchable()

                    ->sortable()

                    ->copyable()

                    ->weight('bold'),

                TextColumn::make('quantity')

                    ->label('Quantity')

                    ->numeric()

                    ->badge()

                    ->color(Color::Blue)

                    ->sortable(),

                TextColumn::make('manufacture_date')

                    ->label('Manufacture')

                    ->date()

                    ->sortable(),

                TextColumn::make('expiry_date')

                    ->label('Expiry')

                    ->date()

                    ->sortable(),

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
                'No Batch Found'
            )

            ->emptyStateDescription(
                'Create the first batch record.'
            )

            ->striped();
    }
}