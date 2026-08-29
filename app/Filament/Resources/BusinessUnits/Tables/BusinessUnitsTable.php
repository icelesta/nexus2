<?php

namespace App\Filament\Resources\BusinessUnits\Tables;

use App\Filament\Concerns\HasProtectedDeleteAction;
use Filament\Actions\ActionGroup;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;

class BusinessUnitsTable
{
    use HasProtectedDeleteAction;

    public static function configure(Table $table): Table
    {
        return $table
            ->defaultSort('sort_order')

            ->columns([

                TextColumn::make('business_unit_code')
                    ->label('Code')
                    ->searchable()
                    ->sortable()
                    ->weight('bold'),

                TextColumn::make('business_unit_name')
                    ->label('Business Unit')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('company.company_name')
                    ->label('Company')
                    ->sortable()
                    ->searchable(),

                TextColumn::make('branch.branch_name')
                    ->label('Branch')
                    ->sortable()
                    ->searchable()
                    ->toggleable(),

                TextColumn::make('manager.name')
                    ->label('Manager')
                    ->sortable()
                    ->searchable()
                    ->toggleable(),

                TextColumn::make('short_name')
                    ->label('Short')
                    ->toggleable(),

                IconColumn::make('is_default')
                    ->label('Default')
                    ->boolean(),

                IconColumn::make('is_active')
                    ->label('Active')
                    ->boolean(),

                TextColumn::make('sort_order')
                    ->label('Sort')
                    ->sortable(),

                /*
                |--------------------------------------------------------------------------
                | Audit
                |--------------------------------------------------------------------------
                */

                TextColumn::make('creator.name')
                    ->label('Created By')
                    ->searchable()
                    ->sortable()
                    ->placeholder('-'),

                TextColumn::make('created_at')
                    ->label('Created Date')
                    ->dateTime('d M Y H:i:s')
                    ->sortable(),

                TextColumn::make('updater.name')
                    ->label('Updated By')
                    ->searchable()
                    ->sortable()
                    ->placeholder('-'),

                TextColumn::make('updated_at')
                    ->label('Updated Date')
                    ->dateTime('d M Y H:i:s')
                    ->sortable(),

            ])

            ->filters([

                SelectFilter::make('company')
                    ->relationship('company', 'company_name'),

                SelectFilter::make('branch')
                    ->relationship('branch', 'branch_name'),

                SelectFilter::make('is_active')
                    ->options([
                        1 => 'Active',
                        0 => 'Inactive',
                    ]),

            ])

            ->recordActions([

                ActionGroup::make([

                    ViewAction::make(),

                    EditAction::make(),

                    self::deleteAction()
                        ->requiresConfirmation(),

                ])
                    ->label('Action')
                    ->button(),

            ])

            ->toolbarActions([

                BulkActionGroup::make([

                    DeleteBulkAction::make(),

                ]),

            ]);
    }
}