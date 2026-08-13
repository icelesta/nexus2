<?php

namespace App\Filament\Resources\CostCenters\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\TrashedFilter;
use Filament\Tables\Table;

use App\Filament\Concerns\HasProtectedDeleteAction;

class CostCentersTable
{

    use HasProtectedDeleteAction;

    public static function configure(Table $table): Table
    {
        return $table
            ->defaultSort('cost_center_code')

            ->columns([

                TextColumn::make('cost_center_code')
                    ->label('Code')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('cost_center_name')
                    ->label('Cost Center Name')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('section.section_name')
                    ->label('Section')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('department.department_name')
                    ->label('Department')
                    ->searchable()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),

                TextColumn::make('businessUnit.business_unit_name')
                    ->label('Business Unit')
                    ->searchable()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),

                TextColumn::make('branch.branch_name')
                    ->label('Branch')
                    ->searchable()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),

                TextColumn::make('company.company_name')
                    ->label('Company')
                    ->searchable()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),

                IconColumn::make('is_active')
                    ->label('Active')
                    ->boolean(),

                TextColumn::make('created_at')
                    ->label('Create Date')
                    ->dateTime('d M Y H:i')
                    ->sortable(),

                TextColumn::make('creator.name')
                    ->label('Created By')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('updated_at')
                    ->label('Update Date')
                    ->dateTime('d M Y H:i')
                    ->sortable(),

                TextColumn::make('updater.name')
                    ->label('Updated By')
                    ->searchable()
                    ->sortable(),

            ])

            ->filters([
                TrashedFilter::make(),
            ])

            ->recordActions([

                ViewAction::make()
                    ->icon('heroicon-o-eye')
                    ->label('View')
                    ->tooltip('View'),

                EditAction::make()
                    ->icon('heroicon-o-pencil-square')
                    ->label('Edit')
                    ->color('warning')
                    ->tooltip('Edit'),

                self::deleteAction()
                    ->icon('heroicon-o-trash')
                    ->label('Delete')
                    ->color('danger')
                    ->tooltip('Delete')
                    ->requiresConfirmation(),

            ])

            ->toolbarActions([

                BulkActionGroup::make([

                    DeleteBulkAction::make(),

                ]),

            ]);
    }
}