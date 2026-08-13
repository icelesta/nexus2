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

use Filament\Tables\Filters\TrashedFilter;

class PricesRelationManager extends RelationManager
{
    protected static string $relationship = 'prices';

    protected static ?string $title = 'Prices';

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

                        Select::make('price_type')
                            ->label('Price Type')
                            ->required()
                            ->options([

                                'Purchase'        => 'Purchase',

                                'Sales'           => 'Sales',

                                'Standard Cost'   => 'Standard Cost',

                                'Average Cost'    => 'Average Cost',

                                'Replacement Cost'=> 'Replacement Cost',

                            ]),

                        Select::make('currency_id')
                            ->label('Currency')
                            ->relationship(
                                'currency',
                                'currency_code'
                            )
                            ->required()
                            ->searchable()
                            ->preload(),

                        TextInput::make('price')
                            ->label('Price')
                            ->numeric()
                            ->required()
                            ->prefix('$'),

                        DatePicker::make('effective_date')
                            ->label('Effective Date')
                            ->required(),

                        DatePicker::make('expiry_date')
                            ->label('Expiry Date'),

                        Toggle::make('is_default')
                            ->label('Default Price')
                            ->default(false),

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

            ->defaultSort('effective_date', 'desc')

            ->columns([

                TextColumn::make('price_type')
                    ->label('Type')
                    ->badge()
                    ->color(Color::Blue)
                    ->sortable()
                    ->searchable(),

                TextColumn::make('currency.currency_code')
                    ->label('Currency')
                    ->badge()
                    ->color(Color::Gray),

                TextColumn::make('price')
                    ->label('Price')
                    ->money('USD')
                    ->sortable(),

                TextColumn::make('effective_date')
                    ->label('Effective')
                    ->date()
                    ->sortable(),

                TextColumn::make('expiry_date')
                    ->label('Expiry')
                    ->date()
                    ->sortable(),

                IconColumn::make('is_default')
                    ->label('Default')
                    ->boolean()
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
                'No Price Found'
            )

            ->emptyStateDescription(
                'Create the first price for this item.'
            )

            ->striped();
    }
}