<?php

declare(strict_types=1);

namespace App\Filament\Resources\ShippingAddresses\Tables;

use Filament\Actions\ActionGroup;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ForceDeleteBulkAction;
use Filament\Actions\RestoreBulkAction;
use Filament\Actions\ViewAction;

use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\TrashedFilter;
use Filament\Tables\Table;

class ShippingAddressesTable
{
    public static function configure(
        Table $table,
    ): Table {

        return $table

            ->defaultSort(
                'shipping_code',
            )

            ->columns([

                TextColumn::make('shipping_code')
                    ->label('Code')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('shipping_name')
                    ->label('Shipping Address')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('branch.branch_name')
                    ->label('Branch')
                    ->sortable()
                    ->toggleable(),

                TextColumn::make('contact_person')
                    ->label('Contact')
                    ->searchable(),

                IconColumn::make('is_default')
                    ->label('Default')
                    ->boolean(),

                IconColumn::make('is_active')
                    ->label('Status')
                    ->boolean(),

                /*
                |--------------------------------------------------------------------------
                | Audit
                |--------------------------------------------------------------------------
                */

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

                TrashedFilter::make(),

            ])

            ->recordActions([

                ActionGroup::make([
                    ViewAction::make()
                        ->label('View'),

                    EditAction::make()
                        ->label('Edit'),

                    DeleteAction::make()
                        ->label('Delete'),
                ])
                    ->label('Actions')
                    ->button()
                    ->color('warning')
                    ->icon('heroicon-m-ellipsis-vertical'),

            ])

            ->toolbarActions([

                BulkActionGroup::make([

                    DeleteBulkAction::make(),

                    RestoreBulkAction::make(),

                    ForceDeleteBulkAction::make(),

                ]),

            ]);

    }
}