<?php

namespace App\Filament\Resources\Users\Tables;

use Filament\Tables\Table;

use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\IconColumn;

use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Filters\TernaryFilter;

use Filament\Actions\ViewAction;
use Filament\Actions\EditAction;
use Filament\Actions\DeleteAction;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;

class UsersTable
{
    public static function configure(Table $table): Table
    {
        return $table

            ->defaultSort('employee_no')

            ->striped()

            ->columns([

                /*
                |--------------------------------------------------------------------------
                | Employee
                |--------------------------------------------------------------------------
                */

                TextColumn::make('employee_no')
                    ->label('Employee No')
                    ->sortable()
                    ->searchable()
                    ->weight('bold')
                    ->copyable(),

                TextColumn::make('username')
                    ->label('Username')
                    ->sortable()
                    ->searchable()
                    ->copyable()
                    ->badge()
                    ->color('gray'),

                TextColumn::make('name')
                    ->label('Full Name')
                    ->sortable()
                    ->searchable(),

                TextColumn::make('email')
                    ->label('Email')
                    ->sortable()
                    ->searchable()
                    ->copyable()
                    ->toggleable(),

                TextColumn::make('phone')
                    ->label('Phone')
                    ->searchable()
                    ->toggleable(isToggledHiddenByDefault: true),

                /*
                |--------------------------------------------------------------------------
                | Organization
                |--------------------------------------------------------------------------
                */

                TextColumn::make('company.display_name')
                    ->label('Company')
                    ->badge()
                    ->sortable()
                    ->searchable()
                    ->toggleable(),

                TextColumn::make('branch.display_name')
                    ->label('Branch')
                    ->badge()
                    ->sortable()
                    ->toggleable(),

                TextColumn::make('businessUnit.display_name')
                    ->label('Business Unit')
                    ->badge()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),

                TextColumn::make('department.display_name')
                    ->label('Department')
                    ->badge()
                    ->sortable()
                    ->toggleable(),

                TextColumn::make('section.display_name')
                    ->label('Section')
                    ->badge()
                    ->toggleable(isToggledHiddenByDefault: true),

                TextColumn::make('costCenter.display_name')
                    ->label('Cost Center')
                    ->badge()
                    ->toggleable(isToggledHiddenByDefault: true),

                TextColumn::make('profitCenter.display_name')
                    ->label('Profit Center')
                    ->badge()
                    ->toggleable(isToggledHiddenByDefault: true),

                /*
                |--------------------------------------------------------------------------
                | Security
                |--------------------------------------------------------------------------
                */

                TextColumn::make('roles.name')
                    ->label('Roles')
                    ->badge()
                    ->listWithLineBreaks()
                    ->limitList(3)
                    ->expandableLimitedList()
                    ->color(fn (string $state): string => match ($state) {
                        'Super Administrator' => 'danger',
                        'Administrator' => 'warning',
                        'Manager' => 'info',
                        'Supervisor' => 'success',
                        default => 'gray',
                    }),

                IconColumn::make('is_active')
                    ->label('Status')
                    ->boolean()
                    ->alignCenter(),

                /*
                |--------------------------------------------------------------------------
                | Audit
                |--------------------------------------------------------------------------
                */

                TextColumn::make('last_login_at')
                    ->label('Last Login')
                    ->since()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),

                TextColumn::make('created_at')
                    ->label('Created')
                    ->dateTime('d M Y H:i')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),

                TextColumn::make('creator.name')
                    ->label('Created By')
                    ->toggleable(isToggledHiddenByDefault: true),

                TextColumn::make('updated_at')
                    ->label('Updated')
                    ->dateTime('d M Y H:i')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),

                TextColumn::make('updater.name')
                    ->label('Updated By')
                    ->toggleable(isToggledHiddenByDefault: true),

            ])

             ->filters([

                SelectFilter::make('company')
                    ->relationship('company', 'company_name')
                    ->searchable()
                    ->preload(),

                SelectFilter::make('branch')
                    ->relationship('branch', 'branch_name')
                    ->searchable()
                    ->preload(),

                SelectFilter::make('businessUnit')
                    ->relationship('businessUnit', 'business_unit_name')
                    ->label('Business Unit')
                    ->searchable()
                    ->preload(),

                SelectFilter::make('department')
                    ->relationship('department', 'department_name')
                    ->searchable()
                    ->preload(),

                SelectFilter::make('section')
                    ->relationship('section', 'section_name')
                    ->searchable()
                    ->preload(),

                SelectFilter::make('costCenter')
                    ->relationship('costCenter', 'cost_center_name')
                    ->label('Cost Center')
                    ->searchable()
                    ->preload(),

                SelectFilter::make('profitCenter')
                    ->relationship('profitCenter', 'profit_center_name')
                    ->label('Profit Center')
                    ->searchable()
                    ->preload(),

                SelectFilter::make('roles')
                    ->relationship('roles', 'name')
                    ->label('Role')
                    ->searchable()
                    ->preload(),

                TernaryFilter::make('is_active')
                    ->label('Status'),

            ])

            ->recordActions([

                ViewAction::make(),

                EditAction::make(),

                DeleteAction::make()
                    ->requiresConfirmation(),

            ])

            ->toolbarActions([

                BulkActionGroup::make([

                    DeleteBulkAction::make(),

                ]),

            ])

            ->emptyStateHeading('No Users Found')

            ->emptyStateDescription(
                'Create your first user to start using Nexus 2.'
            );
    }
}