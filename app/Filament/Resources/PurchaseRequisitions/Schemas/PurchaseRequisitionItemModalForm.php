<?php

declare(strict_types=1);

namespace App\Filament\Resources\PurchaseRequisitions\Schemas;

use App\Models\Item;
use App\Models\Warehouse;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Placeholder;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;

class PurchaseRequisitionItemModalForm
{
    /**
     * Build Purchase Requisition Item modal schema.
     *
     * @return array<int, \Filament\Forms\Components\Component>
     */
    public static function schema(): array
    {
        return [

            /*
            |--------------------------------------------------------------------------
            | Item Selection
            |--------------------------------------------------------------------------
            */

            Select::make('item_id')
                ->label('Item')
                ->relationship(
                    name: 'item',
                    titleAttribute: 'item_name',
                    modifyQueryUsing: fn ($query) => $query
                        ->purchase()
                        ->active()
                        ->orderBy('item_code')
                )
                ->searchable()
                ->preload()
                ->required()
                ->native(false),

            /*
            |--------------------------------------------------------------------------
            | Item Information
            |--------------------------------------------------------------------------
            */

            TextInput::make('item_code')
                ->label('Item Code')
                ->disabled()
                ->dehydrated(false),

            TextInput::make('item_name')
                ->label('Item Name')
                ->disabled()
                ->dehydrated(false),

            Textarea::make('description')
                ->label('Description')
                ->rows(3)
                ->disabled()
                ->dehydrated(false)
                ->columnSpanFull(),

            TextInput::make('uom')
                ->label('Unit of Measure')
                ->disabled()
                ->dehydrated(false),

            /*
            |--------------------------------------------------------------------------
            | Transaction Information
            |--------------------------------------------------------------------------
            */

            TextInput::make('quantity')
                ->label('Quantity')
                ->numeric()
                ->default(1)
                ->required(),

            Select::make('warehouse_id')
                ->label('Warehouse')
                ->relationship(
                    'warehouse',
                    'warehouse_name'
                )
                ->searchable()
                ->preload()
                ->native(false)
                ->required(),

            DatePicker::make('required_date')
                ->label('Required Date')
                ->native(false)
                ->required(),

            Textarea::make('remarks')
                ->label('Remarks')
                ->rows(3)
                ->columnSpanFull(),

        ];
    }
}