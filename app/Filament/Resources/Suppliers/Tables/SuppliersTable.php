<?php

declare(strict_types=1);

namespace App\Filament\Resources\Suppliers\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;

use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Filters\TernaryFilter;

use Filament\Tables\Table;
use Filament\Tables\Columns\TextColumn;

class SuppliersTable
{
    public static function configure(
        Table $table,
    ): Table {

        return $table

            /*
            |--------------------------------------------------------------------------
            | Table Configuration
            |--------------------------------------------------------------------------
            */

            ->defaultSort(
                'supplier_code'
            )

            ->striped()

            ->columns([

                /*
                |--------------------------------------------------------------------------
                | Company
                |--------------------------------------------------------------------------
                */

                TextColumn::make('company.company_name')

                    ->label('Company')

                    ->badge()

                    ->color('primary')

                    ->sortable()

                    ->searchable()

                    ->toggleable(),

                /*
                |--------------------------------------------------------------------------
                | Supplier
                |--------------------------------------------------------------------------
                */

                TextColumn::make('supplier_code')

                    ->label('Supplier Code')

                    ->searchable()

                    ->sortable()

                    ->weight('bold')

                    ->copyable()

                    ->copyMessage('Supplier Code copied.')

                    ->toggleable(),

                TextColumn::make('supplier_name')

                    ->label('Supplier Name')

                    ->searchable()

                    ->sortable()

                    ->wrap()

                    ->description(
                        fn ($record): ?string =>
                            collect([
                                $record->contact_person,
                                $record->email,
                            ])
                            ->filter()
                            ->implode(' • ')
                    )

                    ->toggleable(),

                /*
                |--------------------------------------------------------------------------
                | Category
                |--------------------------------------------------------------------------
                */

                TextColumn::make('category.category_name')

                    ->label('Category')

                    ->badge()

                    ->color('gray')

                    ->placeholder('-')

                    ->sortable()

                    ->searchable()

                    ->toggleable(),

                /*
                |--------------------------------------------------------------------------
                | Company Type
                |--------------------------------------------------------------------------
                */

                TextColumn::make('company_type')

                    ->label('Type')

                    ->badge()

                    ->color(fn (string $state): string => match ($state) {

                        'Manufacturer' => 'success',

                        'Supplier' => 'primary',

                        'Contractor' => 'warning',

                        'Service' => 'info',

                        default => 'gray',

                    })

                    ->sortable()

                    ->toggleable(),

                /*
                |--------------------------------------------------------------------------
                | Currency
                |--------------------------------------------------------------------------
                */

                TextColumn::make('currency.currency_code')

                    ->label('Currency')

                    ->badge()

                    ->color('success')

                    ->placeholder('-')

                    ->sortable()

                    ->toggleable(),

                /*
                |--------------------------------------------------------------------------
                | Payment Term
                |--------------------------------------------------------------------------
                */

                TextColumn::make('paymentTerm.term_name')

                    ->label('Payment Term')

                    ->badge()

                    ->color('warning')

                    ->placeholder('-')

                    ->sortable()

                    ->toggleable(),

                /*
                |--------------------------------------------------------------------------
                | Vendor Rating
                |--------------------------------------------------------------------------
                */

                TextColumn::make('vendor_rating')

                    ->label('Rating')

                    ->formatStateUsing(

                        fn ($state) => '⭐ ' . number_format(
                            (float) $state,
                            2
                        )

                    )

                    ->sortable()

                    ->alignCenter()

                    ->toggleable(),

            ])


            /*
            |--------------------------------------------------------------------------
            | Filters
            |--------------------------------------------------------------------------
            */

            ->filters([

                /*
                |--------------------------------------------------------------------------
                | Company
                |--------------------------------------------------------------------------
                */

                SelectFilter::make('company_id')

                    ->label('Company')

                    ->relationship(
                        'company',
                        'company_name'
                    )

                    ->searchable()

                    ->preload(),

                /*
                |--------------------------------------------------------------------------
                | Category
                |--------------------------------------------------------------------------
                */

                SelectFilter::make('category_id')

                    ->label('Category')

                    ->relationship(
                        'category',
                        'category_name'
                    )

                    ->searchable()

                    ->preload(),

                /*
                |--------------------------------------------------------------------------
                | Company Type
                |--------------------------------------------------------------------------
                */

                SelectFilter::make('company_type')

                    ->label('Company Type')

                    ->options([

                        'Supplier'     => 'Supplier',

                        'Contractor'   => 'Contractor',

                        'Service'      => 'Service',

                        'Manufacturer' => 'Manufacturer',

                    ]),

                /*
                |--------------------------------------------------------------------------
                | Currency
                |--------------------------------------------------------------------------
                */

                SelectFilter::make('currency_id')

                    ->label('Currency')

                    ->relationship(
                        'currency',
                        'currency_code'
                    )

                    ->searchable()

                    ->preload(),

                /*
                |--------------------------------------------------------------------------
                | Payment Term
                |--------------------------------------------------------------------------
                */

                SelectFilter::make('payment_term_id')

                    ->label('Payment Term')

                    ->relationship(
                        'paymentTerm',
                        'term_name'
                    )

                    ->searchable()

                    ->preload(),

                /*
                |--------------------------------------------------------------------------
                | Preferred Supplier
                |--------------------------------------------------------------------------
                */

                TernaryFilter::make('is_preferred')

                    ->label('Preferred Supplier'),

                /*
                |--------------------------------------------------------------------------
                | Blacklisted
                |--------------------------------------------------------------------------
                */

                TernaryFilter::make('is_blacklisted')

                    ->label('Blacklisted'),

                /*
                |--------------------------------------------------------------------------
                | Active
                |--------------------------------------------------------------------------
                */

                TernaryFilter::make('is_active')

                    ->label('Active'),

            ])

            /*
            |--------------------------------------------------------------------------
            | Record Actions
            |--------------------------------------------------------------------------
            */

            ->recordActions([

                /*
                |--------------------------------------------------------------------------
                | View
                |--------------------------------------------------------------------------
                */

                ViewAction::make()

                    ->label('View')

                    ->icon('heroicon-o-eye')

                    ->tooltip('View supplier details'),

                /*
                |--------------------------------------------------------------------------
                | Edit
                |--------------------------------------------------------------------------
                */

                EditAction::make()

                    ->label('Edit')

                    ->icon('heroicon-o-pencil-square')

                    ->tooltip('Edit supplier information'),

                /*
                |--------------------------------------------------------------------------
                | Delete
                |--------------------------------------------------------------------------
                */

                DeleteAction::make()

                    ->label('Delete')

                    ->icon('heroicon-o-trash')

                    ->requiresConfirmation()

                    ->modalHeading('Delete Supplier')

                    ->modalDescription(
                        'Are you sure you want to delete this supplier? This action can be restored if soft delete is enabled.'
                    )

                    ->modalSubmitActionLabel('Delete')

                    ->successNotificationTitle(
                        'Supplier deleted successfully.'
                    ),

            ])

            /*
            |--------------------------------------------------------------------------
            | Bulk Actions
            |--------------------------------------------------------------------------
            */

            ->toolbarActions([

                BulkActionGroup::make([

                    /*
                    |--------------------------------------------------------------------------
                    | Delete
                    |--------------------------------------------------------------------------
                    */

                    DeleteBulkAction::make()

                        ->label('Delete Selected')

                        ->icon('heroicon-o-trash')

                        ->requiresConfirmation()

                        ->modalHeading('Delete Selected Suppliers')

                        ->modalDescription(
                            'Are you sure you want to delete the selected suppliers? This action uses Soft Delete and can be restored later.'
                        )

                        ->modalSubmitActionLabel('Delete')

                        ->successNotificationTitle(
                            'Selected suppliers have been deleted.'
                        ),

            ]),

        ]);   // <-- WAJIB ada ; di sini

    }
}