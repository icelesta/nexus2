<?php

namespace App\Filament\Resources\FiscalYears\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Filters\TernaryFilter;
use Filament\Tables\Table;

class FiscalYearsTable
{
    public static function configure(Table $table): Table
    {
        return $table

            ->defaultSort('start_date', 'desc')

            ->columns([

                TextColumn::make('company.company_name')
                    ->label('Company')
                    ->sortable()
                    ->searchable(),

                TextColumn::make('fiscal_code')
                    ->label('Code')
                    ->sortable()
                    ->searchable()
                    ->weight('bold'),

                TextColumn::make('fiscal_name')
                    ->label('Fiscal Year')
                    ->sortable()
                    ->searchable(),

                TextColumn::make('start_date')
                    ->label('Start Date')
                    ->date('d M Y')
                    ->sortable(),

                TextColumn::make('end_date')
                    ->label('End Date')
                    ->date('d M Y')
                    ->sortable(),

                TextColumn::make('periods')
                    ->label('Periods')
                    ->alignCenter(),

                TextColumn::make('status')
                    ->badge()
                    ->sortable()
                    ->colors([
                        'success' => 'Open',
                        'danger' => 'Closed',
                    ]),

                IconColumn::make('is_default')
                    ->label('Default')
                    ->boolean(),

                IconColumn::make('is_active')
                    ->label('Active')
                    ->boolean(),

                TextColumn::make('created_at')
                    ->label('Created')
                    ->dateTime('d M Y H:i')
                    ->toggleable(isToggledHiddenByDefault: true),

                TextColumn::make('creator.name')
                    ->label('Created By')
                    ->toggleable(isToggledHiddenByDefault: true),

                TextColumn::make('updated_at')
                    ->label('Updated')
                    ->dateTime('d M Y H:i')
                    ->toggleable(isToggledHiddenByDefault: true),

                TextColumn::make('updater.name')
                    ->label('Updated By')
                    ->toggleable(isToggledHiddenByDefault: true),

            ])

            ->filters([

                SelectFilter::make('status')
                    ->options([
                        'Open' => 'Open',
                        'Closed' => 'Closed',
                    ]),

                TernaryFilter::make('is_default')
                    ->label('Default Fiscal Year'),

                TernaryFilter::make('is_active')
                    ->label('Active'),

            ])

            ->recordActions([

                ViewAction::make()
                    ->label(''),

                EditAction::make()
                    ->label(''),

                DeleteAction::make()
                    ->label('')
                    ->requiresConfirmation()
                    ->visible(fn ($record) => ! $record->is_default),

            ])

            ->toolbarActions([

                BulkActionGroup::make([

                    DeleteBulkAction::make(),

                ]),

            ]);
    }
}