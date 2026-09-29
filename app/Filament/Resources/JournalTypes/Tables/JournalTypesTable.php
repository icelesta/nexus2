<?php

namespace App\Filament\Resources\JournalTypes\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\DeleteAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Actions\ActionGroup;

use Filament\Tables\Table;

use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;

use Filament\Tables\Filters\TernaryFilter;

class JournalTypesTable
{
    public static function configure(Table $table): Table
    {
        return $table

            ->defaultSort('code')

            ->columns([

                TextColumn::make('code')
                    ->label('Code')
                    ->badge()
                    ->color('primary')
                    ->sortable()
                    ->searchable(),

                TextColumn::make('name')
                    ->label('Journal Type')
                    ->sortable()
                    ->searchable(),

                TextColumn::make('description')
                    ->label('Description')
                    ->limit(50)
                    ->toggleable(),

                IconColumn::make('is_system')
                    ->label('System')
                    ->boolean()
                    ->alignCenter(),

                IconColumn::make('is_active')
                    ->label('Active')
                    ->boolean()
                    ->alignCenter(),

                TextColumn::make('created_at')
                    ->label('Create Date')
                    ->dateTime('d M Y H:i')
                    ->sortable(),

                TextColumn::make('creator.name')
                    ->label('Created By')
                    ->sortable(),

                TextColumn::make('updated_at')
                    ->label('Update Date')
                    ->dateTime('d M Y H:i')
                    ->sortable(),

                TextColumn::make('updater.name')
                    ->label('Updated By')
                    ->sortable(),

            ])

            ->filters([

                TernaryFilter::make('is_system')
                    ->label('System Journal'),

                TernaryFilter::make('is_active')
                    ->label('Status'),

            ])

            ->recordActions([
                ActionGroup::make([
                    ViewAction::make()
                        ->label('View'),

                    EditAction::make()
                        ->label('Edit'),

                    DeleteAction::make()
                        ->label('Delete')
                        ->requiresConfirmation(),
                ])
                    ->label('Actions')
                    ->button()
                    ->color('warning')
                    ->icon('heroicon-m-ellipsis-vertical'),
            ])

            ->toolbarActions([

                BulkActionGroup::make([

                    DeleteBulkAction::make(),

                ]),

            ])

            ->emptyStateHeading('No Journal Types Found')

            ->emptyStateDescription(
                'Create your first journal type.'
            );
    }
}