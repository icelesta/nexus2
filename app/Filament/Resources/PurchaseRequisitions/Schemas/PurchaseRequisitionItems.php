<?php

declare(strict_types=1);

namespace App\Filament\Resources\PurchaseRequisitions\Schemas;


use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Placeholder;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;

use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;



class PurchaseRequisitionItems
{
    public static function configure(
        Schema $schema,
    ): Schema {

        return $schema
            ->components([

				/*
				|--------------------------------------------------------------------------
				| Item Information
				|--------------------------------------------------------------------------
				*/

				Section::make('Item Information')
				    ->description('Material or service information.')
				    ->icon('heroicon-o-cube')
				    ->collapsible()
				    ->columns(12)
				    ->schema([

				        Select::make('item_id')
				            ->label('Item')
				            ->relationship(
				                'item',
				                'item_name',
				            )
				            ->searchable()
				            ->preload()
				            ->native(false)
				            ->required()
				            ->columnSpan(4),

				        TextInput::make('item_code')
				            ->label('Item Code')
				            ->disabled()
				            ->dehydrated()
				            ->columnSpan(2),

				        TextInput::make('part_number')
				            ->label('Part Number')
				            ->disabled()
				            ->dehydrated()
				            ->columnSpan(3),

				        TextInput::make('manufacturer')
				            ->label('Manufacturer')
				            ->disabled()
				            ->dehydrated()
				            ->columnSpan(3),

				        Textarea::make('description')
				            ->label('Description')
				            ->rows(3)
				            ->columnSpan(8),

				        TextInput::make('item_category')
				            ->label('Category')
				            ->disabled()
				            ->dehydrated()
				            ->columnSpan(2),

				        TextInput::make('base_uom')
				            ->label('Base UOM')
				            ->disabled()
				            ->dehydrated()
				            ->columnSpan(2),

				    ])
				    ->columnSpanFull(),

				/*
				|--------------------------------------------------------------------------
				| Quantity
				|--------------------------------------------------------------------------
				*/

				Section::make('Quantity')
				    ->description('Requested quantity and delivery requirement.')
				    ->icon('heroicon-o-scale')
				    ->collapsible()
				    ->columns(12)
				    ->schema([

						TextInput::make('requested_qty')
						    ->label('Requested Quantity')
						    ->numeric()
						    ->extraInputAttributes([
						        'inputmode' => 'decimal',
						        'onkeydown' => "return !['e', 'E', '+', '-'].includes(event.key)",
						    ])
						    ->required()
						    ->default(1)
						    ->minValue(0.01)
						    ->suffix('Qty')
						    ->columnSpan(3),

				        Select::make('purchase_uom_id')
				            ->label('Purchase UOM')
				            ->relationship(
				                'purchaseUom',
				                'uom_name',
				            )
				            ->searchable()
				            ->preload()
				            ->native(false)
				            ->required()
				            ->columnSpan(3),

				        Placeholder::make('conversion_factor')
				            ->label('Conversion Factor')
				            ->content('1.00')
				            ->columnSpan(3),

				        DatePicker::make('required_date')
				            ->label('Required Date')
				            ->required()
				            ->native(false)
				            ->columnSpan(3),

				    ])
				    ->columnSpanFull(),

				/*
				|--------------------------------------------------------------------------
				| Inventory
				|--------------------------------------------------------------------------
				*/

				Section::make('Inventory')
				    ->description('Inventory and warehouse assignment.')
				    ->icon('heroicon-o-building-storefront')
				    ->collapsible()
				    ->columns(12)
				    ->schema([

				        Select::make('warehouse_id')
				            ->label('Warehouse')
				            ->relationship(
				                'warehouse',
				                'warehouse_name',
				            )
				            ->searchable()
				            ->preload()
				            ->native(false)
				            ->required()
				            ->columnSpan(4),

				        Select::make('bin_location_id')
				            ->label('Bin Location')
				            ->relationship(
				                'binLocation',
				                'bin_name',
				            )
				            ->searchable()
				            ->preload()
				            ->native(false)
				            ->columnSpan(4),

				        Placeholder::make('available_stock')
				            ->label('Available Stock')
				            ->content('Calculated automatically')
				            ->columnSpan(4),

				        Placeholder::make('reserved_stock')
				            ->label('Reserved Stock')
				            ->content('Calculated automatically')
				            ->columnSpan(4),

				        Placeholder::make('projected_stock')
				            ->label('Projected Stock')
				            ->content('Calculated automatically')
				            ->columnSpan(4),

				        Placeholder::make('inventory_status')
				            ->label('Inventory Status')
				            ->content('Available')
				            ->columnSpan(4),

				    ])
				    ->columnSpanFull(),
				    
				/*
				|--------------------------------------------------------------------------
				| Purchasing
				|--------------------------------------------------------------------------
				*/

				Section::make('Purchasing')
				    ->description('Item purchasing and pricing information.')
				    ->icon('heroicon-o-banknotes')
				    ->collapsible()
				    ->columns(12)
				    ->schema([

				        TextInput::make('unit_price')
				            ->label('Unit Price')
				            ->numeric()
				            ->prefix('$')
				            ->default(0)
				            ->required()
				            ->columnSpan(3),

				        TextInput::make('discount_percent')
				            ->label('Discount (%)')
				            ->numeric()
				            ->default(0)
				            ->suffix('%')
				            ->columnSpan(3),

				        TextInput::make('tax_percent')
				            ->label('Tax (%)')
				            ->numeric()
				            ->default(11)
				            ->suffix('%')
				            ->columnSpan(3),

				        Placeholder::make('line_total')
				            ->label('Line Total')
				            ->content('Calculated automatically')
				            ->columnSpan(3),

				    ])
				    ->columnSpanFull(),

				/*
				|--------------------------------------------------------------------------
				| Summary
				|--------------------------------------------------------------------------
				*/

				Section::make('Summary')
				    ->description('Calculated summary for this Material Requisition item.')
				    ->icon('heroicon-o-calculator')
				    ->collapsible()
				    ->columns(12)
				    ->schema([

				        Placeholder::make('subtotal')
				            ->label('Subtotal')
				            ->content('Calculated automatically')
				            ->columnSpan(3),

				        Placeholder::make('discount_amount')
				            ->label('Discount Amount')
				            ->content('Calculated automatically')
				            ->columnSpan(3),

				        Placeholder::make('tax_amount')
				            ->label('Tax Amount')
				            ->content('Calculated automatically')
				            ->columnSpan(3),

				        Placeholder::make('line_total')
				            ->label('Line Total')
				            ->content('Calculated automatically')
				            ->columnSpan(3),

				    ])
				    ->columnSpanFull(),

            ]);

    }
}