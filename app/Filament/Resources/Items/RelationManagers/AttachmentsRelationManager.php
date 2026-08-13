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
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;

use Filament\Resources\RelationManagers\RelationManager;
use Filament\Schemas\Schema;
use Filament\Support\Colors\Color;
use Filament\Tables\Table;


use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\TrashedFilter;

class AttachmentsRelationManager extends RelationManager
{
    protected static string $relationship = 'attachments';

    protected static ?string $title = 'Attachments';

    /*
    |--------------------------------------------------------------------------
    | Form
    |--------------------------------------------------------------------------
    */

    public function form(
        Schema $schema,
    ): Schema {

        return $schema

            ->schema([

                Grid::make()
                    ->columns(2)

                    ->schema([

                        Select::make('document_type')
                            ->label('Document Type')
                            ->native(false)
                            ->options([

                                'Mill Certificate'         => 'Mill Certificate',
                                'Material Certificate'     => 'Material Certificate',
                                'Datasheet'                => 'Datasheet',
                                'Drawing'                  => 'Drawing',
                                'MSDS'                     => 'MSDS',
                                'Calibration Certificate'  => 'Calibration Certificate',
                                'Inspection Report'        => 'Inspection Report',
                                'ISO Certificate'          => 'ISO Certificate',
                                'Warranty'                 => 'Warranty',
                                'Manual'                   => 'Manual',

                            ]),

                        TextInput::make('document_title')
                            ->label('Document Title')
                            ->maxLength(255),

                        TextInput::make('revision_no')
                            ->maxLength(20),


                        FileUpload::make('file_path')
                            ->label('Attachment')
                            ->disk('public')
                            ->directory('item-attachments')
                            ->visibility('public')
                            ->preserveFilenames()
                            ->downloadable()
                            ->openable()
                            ->columnSpanFull(),

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

            ->defaultSort('created_at','desc')

            ->columns([

                TextColumn::make('document_type')
                    ->label('Type')
                    ->badge()
                    ->color(Color::Blue)
                    ->sortable()
                    ->searchable(),

                TextColumn::make('document_title')
                    ->label('Document')
                    ->weight('bold')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('revision_no')
                    ->label('Rev')
                    ->searchable()
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
                'No Attachment Found'
            )

            ->emptyStateDescription(
                'Upload the first technical document.'
            )

            ->striped();
    }
}