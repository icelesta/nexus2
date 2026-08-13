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

use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Grid;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;

use Filament\Resources\RelationManagers\RelationManager;

use Filament\Schemas\Schema;

use Filament\Tables\Table;

use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;

use Filament\Tables\Filters\TrashedFilter;

class ImagesRelationManager extends RelationManager
{
    protected static string $relationship = 'images';

    protected static ?string $title = 'Images';

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

                        FileUpload::make('image_path')

                            ->label('Image')

                            ->image()

                            ->directory('items')

                            ->imageEditor()

                            ->acceptedFileTypes([
                                'image/jpeg',
                                'image/png',
                                'image/webp',
                            ])

                            ->maxSize(5120)

                            //->preserveFilenames() // untuk merubah nama asli file upload

                            ->downloadable()

                            ->openable()

                            ->previewable(true)

                            ->required()

                            ->columnSpanFull(),

                        TextInput::make('image_name')

                            ->label('Image Name')

                            ->required()

                            ->maxLength(255),

                        Toggle::make('is_primary')

                            ->label('Primary Image')

                            ->default(false),

                        TextInput::make('sort_order')

                            ->numeric()

                            ->default(0),

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

                ImageColumn::make('image_url')

                    ->label('Preview')

                    ->square()

                    ->size(70),

                TextColumn::make('display_name')

                    ->label('Image')

                    ->searchable()

                    ->alignCenter()

                    ->weight('bold'),

                IconColumn::make('is_primary')

                    ->label('Primary')

                    ->trueColor('success')
                    
                    ->falseColor('gray')

                    ->boolean()

                    ->alignCenter(),

                TextColumn::make('sort_order')

                    ->label('Order')

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
                'No Image Found'
            )

            ->emptyStateDescription(
                'Upload the first image for this item.'
            )

            ->striped();
    }
}