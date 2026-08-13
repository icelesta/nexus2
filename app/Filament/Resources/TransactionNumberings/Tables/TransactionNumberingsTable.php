<?php

declare(strict_types=1);

namespace App\Filament\Resources\TransactionNumberings\Tables;

use App\Models\TransactionNumbering;
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

class TransactionNumberingsTable
{
    public static function configure(Table $table): Table
    {
        return $table

            ->defaultSort('sort_order')

            ->columns([

                /*
                |--------------------------------------------------------------------------
                | Organization
                |--------------------------------------------------------------------------
                */

                TextColumn::make('company.company_name')
                    ->label('Company')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('businessUnit.business_unit_name')
                    ->label('Business Unit')
                    ->searchable()
                    ->sortable()
                    ->toggleable(),

                TextColumn::make('branch.branch_name')
                    ->label('Branch')
                    ->searchable()
                    ->sortable()
                    ->toggleable(),

                /*
                |--------------------------------------------------------------------------
                | Document
                |--------------------------------------------------------------------------
                */

                TextColumn::make('sort_order')
                    ->label('#')
                    ->sortable()
                    ->alignCenter()
                    ->toggleable(),

                TextColumn::make('module')
                    ->label('Module')
                    ->badge()
                    ->color('info')
                    ->sortable()
                    ->searchable(),

                TextColumn::make('document_type')
                    ->label('Document Type')
                    ->badge()
                    ->color('primary')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('document_name')
                    ->label('Document Name')
                    ->searchable()
                    ->sortable(),

                /*
                |--------------------------------------------------------------------------
                | Numbering
                |--------------------------------------------------------------------------
                */

                TextColumn::make('prefix')
                    ->label('Prefix')
                    ->badge()
                    ->color('warning')
                    ->sortable(),

                TextColumn::make('suffix')
                    ->label('Suffix')
                    ->badge()
                    ->color('gray')
                    ->toggleable(),

                TextColumn::make('number_separator')
                    ->label('Separator')
                    ->alignCenter()
                    ->toggleable(),

                TextColumn::make('running_digits')
                    ->label('Digits')
                    ->numeric()
                    ->alignCenter()
                    ->sortable(),

                TextColumn::make('start_number')
                    ->label('Start')
                    ->numeric()
                    ->sortable()
                    ->alignCenter()
                    ->toggleable(),

                TextColumn::make('current_number')
                    ->label('Current')
                    ->numeric()
                    ->sortable(),

                TextColumn::make('preview')
                    ->label('Preview')
                    ->badge()
                    ->color('success')
                    ->copyable()
                    ->toggleable(),

                /*
                |--------------------------------------------------------------------------
                | Format
                |--------------------------------------------------------------------------
                */

                TextColumn::make('format_pattern')
                    ->label('Pattern')
                    ->badge()
                    ->color('success')
                    ->toggleable(),

                TextColumn::make('reset_type')
                    ->label('Reset')
                    ->badge()
                    ->sortable()
                    ->color(fn (string $state): string => match ($state) {

                        TransactionNumbering::RESET_NEVER => 'danger',

                        TransactionNumbering::RESET_DAILY => 'info',

                        TransactionNumbering::RESET_MONTHLY => 'success',

                        TransactionNumbering::RESET_YEARLY => 'warning',

                        default => 'gray',

                    }),

                /*
                |--------------------------------------------------------------------------
                | Status
                |--------------------------------------------------------------------------
                */

                IconColumn::make('is_active')
                    ->label('Active')
                    ->boolean()
                    ->sortable(),

                TextColumn::make('last_generated_at')
                    ->label('Last Generated')
                    ->dateTime('d M Y H:i')
                    ->placeholder('-')
                    ->toggleable(),

                /*
                |--------------------------------------------------------------------------
                | Audit
                |--------------------------------------------------------------------------
                */

                TextColumn::make('creator.name')
                    ->label('Created By')
                    ->sortable()
                    ->toggleable(),

                TextColumn::make('created_at')
                    ->label('Created')
                    ->dateTime('d M Y H:i')
                    ->sortable()
                    ->toggleable(),

                TextColumn::make('updater.name')
                    ->label('Updated By')
                    ->sortable()
                    ->toggleable(),

                TextColumn::make('updated_at')
                    ->label('Updated')
                    ->dateTime('d M Y H:i')
                    ->sortable()
                    ->toggleable(),

            ])

            ->filters([

                SelectFilter::make('company')
                    ->relationship('company', 'company_name'),

                SelectFilter::make('businessUnit')
                    ->relationship('businessUnit', 'business_unit_name'),

                SelectFilter::make('branch')
                    ->relationship('branch', 'branch_name'),

                SelectFilter::make('module')
                    ->options([

                        TransactionNumbering::MODULE_PROCUREMENT => 'Procurement',

                        TransactionNumbering::MODULE_INVENTORY => 'Inventory',

                        TransactionNumbering::MODULE_FINANCE => 'Finance',

                    ]),

                SelectFilter::make('reset_type')
                    ->options([

                        TransactionNumbering::RESET_NEVER => 'Never',

                        TransactionNumbering::RESET_DAILY => 'Daily',

                        TransactionNumbering::RESET_MONTHLY => 'Monthly',

                        TransactionNumbering::RESET_YEARLY => 'Yearly',

                    ]),

                TernaryFilter::make('is_active')
                    ->label('Active'),

            ])

            ->recordActions([

                ViewAction::make()
                    ->modalWidth('6xl'),

                EditAction::make(),

                DeleteAction::make()
                    ->requiresConfirmation(),

            ])

            ->toolbarActions([

                BulkActionGroup::make([

                    DeleteBulkAction::make()
                        ->requiresConfirmation(),

                ]),

            ]);
    }
}