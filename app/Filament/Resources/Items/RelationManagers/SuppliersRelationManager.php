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

use Filament\Forms\Components\Grid;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;

use Filament\Resources\RelationManagers\RelationManager;

use Filament\Schemas\Schema;

use Filament\Tables\Table;

use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;

use Filament\Tables\Filters\TrashedFilter;
use Filament\Tables\Filters\TernaryFilter;



class SuppliersRelationManager extends RelationManager
{
    protected static string $relationship = 'suppliers';

    protected static ?string $title = 'Purchase Information';

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

                /*
                |--------------------------------------------------------------------------
                | Supplier Information
                |--------------------------------------------------------------------------
                */

                Section::make('Supplier Information')

                    ->description(
                        'Supplier master information.'
                    )

                    ->columns(2)

                    ->schema([

                        Select::make('supplier_id')
                            ->label('Supplier')
                            ->relationship(
                                'supplier',
                                'supplier_name'
                            )
                            ->required()
                            ->searchable()
                            ->preload(),

                        Select::make('purchase_uom_id')
                            ->label('Purchase UOM')
                            ->relationship(
                                'purchaseUom',
                                'uom_name'
                            )
                            ->searchable()
                            ->preload(),

                        TextInput::make('supplier_item_code')
                            ->label('Supplier Part Number')
                            ->maxLength(100),

                        TextInput::make('supplier_item_name')
                            ->label('Supplier Item Name')
                            ->maxLength(255),

                        Textarea::make('purchase_description')
                            ->label('Purchase Description')
                            ->rows(3)
                            ->columnSpanFull(),

                    ]),

                /*
                |--------------------------------------------------------------------------
                | Purchasing Configuration
                |--------------------------------------------------------------------------
                */

                Section::make('Purchasing Configuration')

                    ->columns(4)

                    ->schema([

                        TextInput::make('minimum_order_qty')
                            ->label('Minimum Order Qty')
                            ->numeric()
                            ->default(1),

                        TextInput::make('purchase_multiple')
                            ->label('Purchase Multiple')
                            ->numeric()
                            ->default(1),

                        TextInput::make('lead_time_days')
                            ->label('Lead Time (Days)')
                            ->numeric()
                            ->default(0),

                        TextInput::make('supplier_priority')
                            ->label('Priority')
                            ->numeric()
                            ->default(1),

                    ]),

                /*
                |--------------------------------------------------------------------------
                | Commercial
                |--------------------------------------------------------------------------
                */

                Section::make('Commercial')

                    ->columns(2)

                    ->schema([

                        Select::make('default_tax_id')
                            ->label('Default Tax')
                            ->relationship(
                                'defaultTax',
                                'tax_name'
                            )
                            ->searchable()
                            ->preload(),

                        TextInput::make('default_discount')
                            ->label('Default Discount (%)')
                            ->numeric()
                            ->default(0),

                        Toggle::make('is_preferred')
                            ->label('Preferred Supplier')
                            ->default(false),

                        Toggle::make('is_active')
                            ->label('Active')
                            ->default(true),

                    ]),

                /*
                |--------------------------------------------------------------------------
                | Purchase History
                |--------------------------------------------------------------------------
                */

                Section::make('Purchase History')

                    ->description(
                        'Automatically updated from Purchasing transactions.'
                    )

                    ->columns(2)

                    ->schema([

                        TextInput::make('last_purchase_price')
                            ->label('Last Purchase Price')
                            ->disabled()
                            ->dehydrated(false)
                            ->numeric(),

                        TextInput::make('last_purchase_date')
                            ->label('Last Purchase Date')
                            ->disabled()
                            ->dehydrated(false),

                    ]),

                /*
                |--------------------------------------------------------------------------
                | Remarks
                |--------------------------------------------------------------------------
                */

                Section::make('Remarks')

                    ->schema([

                        Textarea::make('remarks')
                            ->rows(4)
                            ->columnSpanFull(),

                    ]),

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

            ->defaultSort(
                'is_preferred',
                'desc'
            )

            ->defaultSort(
                'supplier_priority',
                'asc'
            )

            ->columns([

                /*
                |--------------------------------------------------------------------------
                | Supplier
                |--------------------------------------------------------------------------
                */

                TextColumn::make('supplier.supplier_name')
                    ->label('Supplier')
                    ->searchable()
                    ->sortable()
                    ->weight('bold'),

                TextColumn::make('supplier_item_code')
                    ->label('Supplier Part No.')
                    ->searchable()
                    ->copyable(),

                TextColumn::make('supplier_item_name')
                    ->label('Supplier Item')
                    ->searchable()
                    ->toggleable(),

                /*
                |--------------------------------------------------------------------------
                | Purchasing
                |--------------------------------------------------------------------------
                */

                TextColumn::make('purchaseUom.uom_name')
                    ->label('Purchase UOM')
                    ->badge()
                    ->sortable(),

                TextColumn::make('minimum_order_qty')
                    ->label('MOQ')
                    ->numeric()
                    ->alignCenter(),

                TextColumn::make('purchase_multiple')
                    ->label('Multiple')
                    ->numeric()
                    ->alignCenter(),

                TextColumn::make('lead_time_days')
                    ->label('Lead Time')
                    ->suffix(' Days')
                    ->alignCenter()
                    ->sortable(),

                TextColumn::make('supplier_priority')
                    ->badge()
                    ->color(fn ($state) => match ($state) {

                        1 => 'success',

                        2 => 'warning',

                        default => 'gray',

                    }),

                /*
                |--------------------------------------------------------------------------
                | Commercial
                |--------------------------------------------------------------------------
                */

                TextColumn::make('default_discount')
                    ->label('Discount')
                    ->numeric(
                        decimalPlaces:2
                    )
                    ->alignCenter(),

                TextColumn::make('defaultTax.tax_name')
                    ->label('Tax')
                    ->badge()
                    ->placeholder('-')
                    ->toggleable(),
                    

                /*
                |--------------------------------------------------------------------------
                | Purchase History
                |--------------------------------------------------------------------------
                */

                TextColumn::make('last_purchase_price')
                    ->label('Last Purchase')
                    ->money(
                        'IDR',
                        divideBy: 1
                    )
                    ->placeholder('-')
                    ->sortable(),

                TextColumn::make('last_purchase_date')
                    ->label('Last Purchase')
                    ->date('d M Y')
                    ->sortable()
                    ->placeholder('-'),

                /*
                |--------------------------------------------------------------------------
                | Status
                |--------------------------------------------------------------------------
                */

                IconColumn::make('is_preferred')
                    ->label('Preferred')
                    ->boolean()
                    ->alignCenter(),

                IconColumn::make('is_active')
                    ->label('Active')
                    ->boolean()
                    ->alignCenter(),

                /*
                |--------------------------------------------------------------------------
                | Audit
                |--------------------------------------------------------------------------
                */

                TextColumn::make('creator.name')
                    ->label('Created By')
                    ->toggleable(
                        isToggledHiddenByDefault: true
                    ),

                TextColumn::make('created_at')
                    ->label('Created')
                    ->since()
                    ->toggleable(
                        isToggledHiddenByDefault: true
                    ),

                TextColumn::make('updater.name')
                    ->label('Updated By')
                    ->toggleable(
                        isToggledHiddenByDefault: true
                    ),

                TextColumn::make('updated_at')
                    ->label('Updated')
                    ->since()
                    ->toggleable(
                        isToggledHiddenByDefault: true
                    ),

            ])

            ->filters([

                TernaryFilter::make('is_active')
                    ->label('Active'),

                TernaryFilter::make('is_preferred')
                    ->label('Preferred Supplier'),

                TrashedFilter::make(),

            ])

            ->headerActions([

                CreateAction::make()
                    ->label('Add Supplier')
                    ->icon('heroicon-o-plus')
                    ->color('primary')
                    ->modalWidth('5xl'),

            ])

            ->recordActions([

                ViewAction::make()
                    ->icon('heroicon-o-eye')
                    ->tooltip('View'),

                EditAction::make()
                    ->icon('heroicon-o-pencil-square')
                    ->color('warning')
                    ->tooltip('Edit')
                    ->modalWidth('5xl'),

                DeleteAction::make()
                    ->icon('heroicon-o-trash')
                    ->color('danger')
                    ->requiresConfirmation(),

                RestoreAction::make()
                    ->icon('heroicon-o-arrow-uturn-left'),

                ForceDeleteAction::make()
                    ->icon('heroicon-o-x-circle')
                    ->requiresConfirmation(),

            ])

            ->toolbarActions([

                BulkActionGroup::make([

                    DeleteBulkAction::make()
                        ->requiresConfirmation(),

                ]),

            ])

            ->emptyStateHeading(
                'No Purchase Information'
            )

            ->emptyStateDescription(
                'Click "Add Supplier" to create purchasing information for this item.'
            )

            ->striped();
    }
}