<?php

declare(strict_types=1);

namespace App\Filament\Resources\Items\RelationManagers;

use Filament\Actions\CreateAction;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ForceDeleteAction;
use Filament\Actions\RestoreAction;
use Filament\Actions\ViewAction;
use Filament\Actions\BulkActionGroup;

use Filament\Forms\Components\Grid;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;

use Filament\Resources\RelationManagers\RelationManager;

use Filament\Schemas\Schema;

use Filament\Support\Colors\Color;

use Filament\Tables\Table;

use Filament\Tables\Columns\TextColumn;

use Filament\Tables\Filters\TrashedFilter;

class SpecificationsRelationManager extends RelationManager
{
    protected static string $relationship = 'specifications';

    protected static ?string $title = 'Specifications';

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

                        TextInput::make('specification_name')
                            ->label('Specification')
                            ->required()
                            ->maxLength(150),

                        TextInput::make('uom')
                            ->label('UOM')
                            ->maxLength(50),

                        TextInput::make('specification_value')
                            ->label('Value')
                            ->required()
                            ->columnSpanFull(),

                        TextInput::make('sort_order')
                            ->numeric()
                            ->default(0),

                        Textarea::make('remarks')
                            ->rows(3)
                            ->columnSpanFull(),

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

            ->defaultSort('sort_order')

            ->columns([

                TextColumn::make('specification_name')
                    ->label('Specification')
                    ->searchable()
                    ->sortable()
                    ->weight('bold'),

                TextColumn::make('specification_value')
                    ->label('Value')
                    ->searchable(),

                TextColumn::make('uom')
                    ->label('UOM')
                    ->badge()
                    ->color(Color::Blue),

                TextColumn::make('sort_order')
                    ->label('Order')
                    ->sortable(),

                TextColumn::make('created_at')
                    ->label('Created')
                    ->since()
                    ->toggleable(isToggledHiddenByDefault: true),

                TextColumn::make('updated_at')
                    ->label('Updated')
                    ->since()
                    ->toggleable(isToggledHiddenByDefault: true),

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
                'No Specification Found'
            )

            ->emptyStateDescription(
                'Create the first specification for this item.'
            )

            ->striped();
    }
}