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

class BarcodesRelationManager extends RelationManager
{
    protected static string $relationship = 'barcodes';

    protected static ?string $title = 'Barcodes';

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

                        TextInput::make('barcode')
                            ->label('Barcode')
                            ->required()
                            ->maxLength(150)
                            ->unique(
                                table: 'item_barcodes',
                                column: 'barcode',
                                ignoreRecord: true,
                            ),

                        Select::make('barcode_type')
                            ->label('Barcode Type')
                            ->options([
                                'CODE39'  => 'CODE39',
                                'CODE128' => 'CODE128',
                                'EAN13'   => 'EAN13',
                                'UPC'     => 'UPC',
                                'QR'      => 'QR Code',
                            ])
                            ->default('CODE128')
                            ->required(),

                        Toggle::make('is_default')
                            ->label('Default Barcode')
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

            ->defaultSort('barcode')

            ->columns([

                TextColumn::make('barcode')
                    ->label('Barcode')
                    ->copyable()
                    ->searchable()
                    ->sortable()
                    ->weight('bold'),

                TextColumn::make('barcode_type')
                    ->label('Type')
                    ->badge()
                    ->color(Color::Blue),

                IconColumn::make('is_default')
                    ->label('Default')
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
                'No Barcode Found'
            )

            ->emptyStateDescription(
                'Create the first barcode for this item.'
            )

            ->striped();
    }
}