<?php

declare(strict_types=1);

namespace App\Filament\Resources\Items\Schemas;

use App\Models\Brand;
use App\Models\ItemCategory;
use App\Models\Manufacturer;
use App\Models\Uom;
use App\Models\Warehouse;
use App\Models\Country;

use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Placeholder;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Components\Utilities\Set;

use Filament\Schemas\Components\View;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Schemas\Components\Grid;

use App\Support\Barcode\BarcodeGenerator;
use App\Support\Barcode\QrGenerator;
use Illuminate\Support\HtmlString;



class ItemForm
{
    public static function configure(
        Schema $schema,
    ): Schema {

        return $schema

            ->components([

                 /*
                |--------------------------------------------------------------------------
                | General Information
                |--------------------------------------------------------------------------
                */

                Section::make('General Information')
                    ->description('Basic information and master references for this item.')
                    ->icon('heroicon-o-cube')
                    ->columns(2)
                    ->collapsible()
                    ->schema([

                    TextInput::make('item_code')
                        ->label('Item Code')
                        ->placeholder('ITEM-000001')
                        ->required()
                        ->unique(
                            table: 'items',
                            column: 'item_code',
                            ignoreRecord: true,
                        )
                        ->maxLength(50)
                        ->autocomplete(false)
                        ->live(onBlur: true)
                        ->afterStateUpdated(function (
                            Get $get,
                            Set $set,
                            ?string $state,
                        ) {

                            $itemCode = trim($state ?? '');

                            $itemName = trim(
                                $get('item_name') ?? ''
                            );

                            if ($itemCode === '') {
                                $set('barcode', null);
                                $set('qr_code', null);
                                return;
                            }

                            $barcode = BarcodeGenerator::make(
                                $itemCode,
                                $itemName,
                            );

                            $payload = QrGenerator::item(
                                $itemCode,
                                $itemName,
                            );

                            $set('barcode', $barcode);

                            $set('qr_code', $payload);

                        })
                        ->columnSpan(1),

                    TextInput::make('item_name')
                        ->label('Item Name')
                        ->required()
                        ->maxLength(255)
                        ->autocomplete(false)
                        ->live(onBlur: true)
                        ->afterStateUpdated(function (
                            Get $get,
                            Set $set,
                            ?string $state,
                        ) {

                            $itemCode = trim(
                                $get('item_code') ?? ''
                            );

                            $itemName = trim(
                                $state ?? ''
                            );

                            if ($itemCode === '') {
                                return;
                            }

                            $barcode = BarcodeGenerator::make(
                                $itemCode,
                                $itemName,
                            );

                            $payload = QrGenerator::item(
                                $itemCode,
                                $itemName,
                            );

                            $set('barcode', $barcode);

                            $set('qr_code', $payload);

                        })
                        ->columnSpan(1),

                        Select::make('category_id')
                            ->label('Item Category')
                            ->relationship(
                                'category',
                                'category_name',
                            )
                            ->searchable()
                            ->preload()
                            ->native(false)
                            ->columnSpan(1),

                        Select::make('brand_id')
                            ->label('Brand')
                            ->relationship(
                                'brand',
                                'brand_name',
                            )
                            ->searchable()
                            ->preload()
                            ->native(false)
                            ->columnSpan(1),

                        Select::make('manufacturer_id')
                            ->label('Manufacturer')
                            ->relationship(
                                'manufacturer',
                                'manufacturer_name',
                            )
                            ->searchable()
                            ->preload()
                            ->native(false)
                            ->columnSpan(1),

                        Select::make('base_uom_id')
                            ->label('Base UOM')
                            ->relationship(
                                'baseUom',
                                'uom_name',
                            )
                            ->required()
                            ->searchable()
                            ->preload()
                            ->native(false)
                            ->columnSpan(1),

                        Grid::make(2)
                            ->schema([

                                TextInput::make('country_of_origin')
                                    ->label('Country of Origin')
                                    ->maxLength(100)
                                    ->placeholder('Indonesia'),

                                TextInput::make('part_number')
                                    ->label('Part Number')
                                    ->maxLength(100),

                                TextInput::make('drawing_number')
                                    ->label('Drawing Number')
                                    ->maxLength(100),

                                TextInput::make('unique_number')
                                    ->label('Unique Number')
                                    ->maxLength(100),

                            ])
                            ->columnSpanFull(),

                        TextInput::make('barcode')
                            ->label('Barcode')
                            ->disabled()
                            ->dehydrated()
                            ->hint('Generated automatically from Item Code.')
                            ->columnSpan(1),

                        TextInput::make('qr_code')
                            ->label('QR Payload')
                            ->disabled()
                            ->dehydrated()
                            ->hint('Generated automatically from Code & Name.')
                            ->columnSpan(1),

                    ])
                    ->columnSpanFull(),


                /*
                |--------------------------------------------------------------------------
                | Item Image
                |--------------------------------------------------------------------------
                */

                Section::make('Item Image')
                    ->description('Upload product image for identification and reporting.')
                    ->icon('heroicon-o-photo')
                    ->columns(2)
                    ->collapsible()
                    ->schema([

                        FileUpload::make('image')
                            ->label('Product Image')
                            ->disk('public')
                            ->directory('items')
                            ->visibility('public')

                            ->image()

                            ->imageEditor()

                            ->imageResizeMode('contain')

                            ->imageEditorMode(2)

                            ->imagePreviewHeight('250')

                            ->loadingIndicatorPosition('left')

                            ->panelLayout('integrated')

                            ->acceptedFileTypes([
                                'image/jpeg',
                                'image/png',
                                'image/webp',
                            ])

                            ->maxSize(5120)

                            ->preserveFilenames()

                            ->downloadable()

                            ->openable()

                            ->previewable(true)

                            ->live()

                            ->columnSpanFull(),

                    ])

                    ->columnSpanFull(),                  


                    /*
                    |--------------------------------------------------------------------------
                    | Barcode
                    |--------------------------------------------------------------------------
                    */

                    Section::make('Barcode')
                        ->description('Barcode configuration and preview.')
                        ->icon('heroicon-o-bars-3-bottom-left')
                        ->columns(2)
                        ->collapsible()
                        ->schema([

                            TextInput::make('barcode')
                                ->label('Barcode')
                                ->disabled()
                                ->dehydrated()
                                ->hint('Generated automatically from Item Code.')
                                ->columnSpan(1),

                            Select::make('barcode_type')
                                ->label('Barcode Type')
                                ->options([
                                    'CODE128' => 'CODE 128',
                                    'CODE39'  => 'CODE 39',
                                    'EAN13'   => 'EAN 13',
                                    'EAN8'    => 'EAN 8',
                                ])
                                ->default('CODE128')
                                ->native(false)
                                ->live()
                                ->columnSpan(1),

                            Toggle::make('barcode_printed')
                                ->label('Barcode Printed')
                                ->default(false)
                                ->inline(false)
                                ->columnSpan(1),

                            Placeholder::make('barcode_preview')
                                ->label('Barcode Preview')
                                ->content(function (Get $get): \Illuminate\Support\HtmlString {

                                    $barcode = $get('barcode');

                                    if (blank($barcode)) {
                                        return new \Illuminate\Support\HtmlString(
                                            '<div class="text-gray-400 text-sm">Barcode will be generated automatically.</div>'
                                        );
                                    }

                                    return new \Illuminate\Support\HtmlString(
                                        BarcodeGenerator::svg(
                                            $barcode,
                                            $get('barcode_type') ?? 'CODE128',
                                        )
                                    );
                                })
                                ->columnSpan(1),

                        ])
                        ->columnSpanFull(),
                    /*
                    |--------------------------------------------------------------------------
                    | QR Code
                    |--------------------------------------------------------------------------
                    */

                    Section::make('QR Code')
                        ->description('QR Code configuration and preview.')
                        ->icon('heroicon-o-qr-code')
                        ->columns(2)
                        ->collapsible()
                        ->schema([

                            TextInput::make('qr_code')
                                ->label('QR Payload')
                                ->disabled()
                                ->dehydrated()
                                ->hint('Generated automatically from Code & Name.')
                                ->columnSpan(1),

                            Select::make('qr_code_type')
                                ->label('QR Type')
                                ->options([
                                    'ITEM'   => 'Item',
                                    'ASSET'  => 'Asset',
                                    'BATCH'  => 'Batch',
                                    'SERIAL' => 'Serial Number',
                                ])
                                ->default('ITEM')
                                ->native(false)
                                ->columnSpan(1),

                            Toggle::make('qr_printed')
                                ->label('QR Printed')
                                ->default(false)
                                ->inline(false)
                                ->columnSpan(1),

                            Placeholder::make('qr_preview')
                                ->label('QR Preview')
                                ->content(function (Get $get): \Illuminate\Support\HtmlString {

                                    $payload = $get('qr_code');

                                    if (blank($payload)) {
                                        return new \Illuminate\Support\HtmlString(
                                            '<div class="text-gray-400 text-sm">QR Code will be generated automatically.</div>'
                                        );
                                    }

                                    return new \Illuminate\Support\HtmlString(
                                        QrGenerator::svg($payload)
                                    );
                                })
                                ->columnSpan(1),

                        ])
                        ->columnSpanFull(),

                    /*
                    |--------------------------------------------------------------------------
                    | Purchase Configuration
                    |--------------------------------------------------------------------------
                    */

                    Section::make('Purchase Configuration')
                        ->description('Default purchasing configuration for this item.')
                        ->icon('heroicon-o-shopping-cart')
                        ->columns(2)
                        ->collapsible()

                        ->schema([

                            Toggle::make('is_purchase')
                                ->label('Available for Purchase')
                                ->default(true)
                                ->inline(false),

                            Toggle::make('is_subcontract')
                                ->label('Subcontract Item')
                                ->default(false)
                                ->inline(false),

                            TextInput::make('default_purchase_lead_time')
                                ->label('Default Lead Time (Days)')
                                ->numeric()
                                ->default(0)
                                ->minValue(0),

                            TextInput::make('purchase_tolerance')
                                ->label('Purchase Tolerance (%)')
                                ->numeric()
                                ->default(0),

                            TextInput::make('minimum_purchase_qty')
                                ->label('Minimum Purchase Qty')
                                ->numeric()
                                ->default(1),

                            Toggle::make('purchase_requires_approval')
                                ->label('Purchase Requires Approval')
                                ->default(false)
                                ->inline(false),

                        ])

                        ->columnSpanFull(),

                    /*
                    |--------------------------------------------------------------------------
                    | Sales Configuration
                    |--------------------------------------------------------------------------
                    */

                    Section::make('Sales Configuration')
                        ->description('Default sales configuration.')

                        ->icon('heroicon-o-banknotes')

                        ->columns(2)

                        ->collapsible()

                        ->schema([

                            Toggle::make('is_sales')
                                ->label('Available For Sales')
                                ->default(false)
                                ->inline(false),

                            Toggle::make('allow_discount')
                                ->label('Allow Discount')
                                ->default(true)
                                ->inline(false),

                            TextInput::make('minimum_sales_qty')
                                ->label('Minimum Sales Qty')
                                ->numeric()
                                ->default(1),

                            TextInput::make('sales_multiple')
                                ->label('Sales Multiple')
                                ->numeric()
                                ->default(1),

                            Toggle::make('allow_backorder')
                                ->label('Allow Backorder')
                                ->default(false)
                                ->inline(false),

                            Toggle::make('requires_serial_sales')
                                ->label('Require Serial Validation')
                                ->default(false)
                                ->inline(false),

                        ])

                        ->columnSpanFull(),

                    /*
                    |--------------------------------------------------------------------------
                    | Inventory Control
                    |--------------------------------------------------------------------------
                    */

                    Section::make('Inventory Control')

                        ->description('Inventory planning and warehouse configuration.')

                        ->icon('heroicon-o-building-storefront')

                        ->columns(3)

                        ->collapsible()

                        ->schema([

                            Select::make('default_warehouse_id')
                                ->label('Default Warehouse')
                                ->relationship(
                                    'warehouse',
                                    'warehouse_name'
                                )
                                ->searchable()
                                ->preload()
                                ->native(false),

                            TextInput::make('minimum_stock')
                                ->numeric()
                                ->default(0),

                            TextInput::make('maximum_stock')
                                ->numeric()
                                ->default(0),

                            TextInput::make('reorder_level')
                                ->numeric()
                                ->default(0),

                            TextInput::make('safety_stock')
                                ->numeric()
                                ->default(0),

                            TextInput::make('economic_order_qty')
                                ->label('EOQ')
                                ->numeric()
                                ->default(0),

                            Toggle::make('allow_negative_stock')
                                ->default(false)
                                ->inline(false),

                            Toggle::make('cycle_count_required')
                                ->default(false)
                                ->inline(false),

                            Toggle::make('quality_inspection_required')
                                ->default(false)
                                ->inline(false),

                        ])

                        ->columnSpanFull(),

                    /*
                    |--------------------------------------------------------------------------
                    | Physical Information
                    |--------------------------------------------------------------------------
                    */

                    Section::make('Physical Information')
                        ->description('Physical dimensions and weight specification.')
                        ->icon('heroicon-o-scale')
                        ->columns(3)
                        ->collapsible()

                        ->schema([

                            TextInput::make('length')
                                ->label('Length')
                                ->numeric()
                                ->suffix('mm')
                                ->default(0),

                            TextInput::make('width')
                                ->label('Width')
                                ->numeric()
                                ->suffix('mm')
                                ->default(0),

                            TextInput::make('height')
                                ->label('Height')
                                ->numeric()
                                ->suffix('mm')
                                ->default(0),

                            TextInput::make('volume')
                                ->label('Volume')
                                ->numeric()
                                ->suffix('m³')
                                ->default(0),

                            TextInput::make('net_weight')
                                ->label('Net Weight')
                                ->numeric()
                                ->suffix('Kg')
                                ->default(0),

                            TextInput::make('gross_weight')
                                ->label('Gross Weight')
                                ->numeric()
                                ->suffix('Kg')
                                ->default(0),

                        ])

                        ->columnSpanFull(),

                    /*
                    |--------------------------------------------------------------------------
                    | Engineering Information
                    |--------------------------------------------------------------------------
                    */

                    Section::make('Engineering Information')

                        ->description('Engineering and technical references.')

                        ->icon('heroicon-o-wrench-screwdriver')

                        ->columns(2)

                        ->collapsible()

                        ->schema([

                            TextInput::make('drawing_number')
                                ->label('Drawing Number')
                                ->maxLength(100),

                            TextInput::make('part_number')
                                ->label('Part Number')
                                ->maxLength(100),

                            TextInput::make('model_number')
                                ->label('Model Number')
                                ->maxLength(100),

                            TextInput::make('revision_number')
                                ->label('Revision')
                                ->maxLength(50),

                            TextInput::make('unique_number')
                                ->label('Unique Number')
                                ->maxLength(100),

                            TextInput::make('country_of_origin')
                                ->label('Country of Origin')
                                ->maxLength(100),

                        ])

                        ->columnSpanFull(),


                    /*
                    |--------------------------------------------------------------------------
                    | Compliance
                    |--------------------------------------------------------------------------
                    */

                    Section::make('Compliance')

                        ->description('Quality and compliance information.')

                        ->icon('heroicon-o-shield-check')

                        ->columns(3)

                        ->collapsible()

                        ->schema([

                            Toggle::make('is_quality_control')
                                ->label('Quality Control')
                                ->default(false),

                            Toggle::make('is_returnable')
                                ->label('Returnable')
                                ->default(true),

                            Toggle::make('is_expirable')
                                ->label('Expiry Control')
                                ->default(false),

                            Toggle::make('is_hazardous')
                                ->label('Hazardous Material')
                                ->default(false),

                            Toggle::make('is_consignment')
                                ->label('Consignment')
                                ->default(false),

                            Toggle::make('requires_certificate')
                                ->label('Certificate Required')
                                ->default(false),

                        ])

                        ->columnSpanFull(),


                    /*
                    |--------------------------------------------------------------------------
                    | Classification
                    |--------------------------------------------------------------------------
                    */

                    Section::make('Classification')
                        ->description('Define how this item will be used throughout the ERP modules.')
                        ->icon('heroicon-o-tag')
                        ->columns(4)
                        ->collapsible()
                        ->schema([

                            Toggle::make('is_inventory')
                                ->label('Inventory')
                                ->default(true)
                                ->inline(false),

                            Toggle::make('is_purchase')
                                ->label('Purchase')
                                ->default(true)
                                ->inline(false),

                            Toggle::make('is_sales')
                                ->label('Sales')
                                ->inline(false),

                            Toggle::make('is_asset')
                                ->label('Asset')
                                ->inline(false),

                            Toggle::make('is_service')
                                ->label('Service')
                                ->inline(false),

                            Toggle::make('is_serialized')
                                ->label('Serialized')
                                ->inline(false),

                            Toggle::make('is_batch_tracked')
                                ->label('Batch Tracking')
                                ->inline(false),

                            Toggle::make('is_active')
                                ->label('Active')
                                ->default(true)
                                ->inline(false),

                        ])
                        ->columnSpanFull(),

                    /*
                    |--------------------------------------------------------------------------
                    | Inventory Control
                    |--------------------------------------------------------------------------
                    */

                    Section::make('Inventory Control')
                        ->description('Inventory planning and warehouse configuration.')
                        ->icon('heroicon-o-building-storefront')
                        ->columns(2)
                        ->collapsible()
                        ->schema([

                            Select::make('default_warehouse_id')
                                ->label('Default Warehouse')
                                ->relationship(
                                    'warehouse',
                                    'warehouse_name'
                                )
                                ->searchable()
                                ->preload()
                                ->native(false),

                            TextInput::make('minimum_stock')
                                ->label('Minimum Stock')
                                ->numeric()
                                ->default(0),

                            TextInput::make('maximum_stock')
                                ->label('Maximum Stock')
                                ->numeric()
                                ->default(0),

                            TextInput::make('reorder_level')
                                ->label('Reorder Level')
                                ->numeric()
                                ->default(0),

                        ])
                        ->columnSpanFull(),

                    /*
                    |--------------------------------------------------------------------------
                    | Remarks
                    |--------------------------------------------------------------------------
                    */

                    Section::make('Remarks')
                        ->description('Additional notes or operational remarks.')
                        ->icon('heroicon-o-document-text')
                        ->collapsible()
                        ->schema([

                            Textarea::make('remarks')
                                ->label('Remarks')
                                ->rows(5)
                                ->placeholder('Enter additional information...')
                                ->columnSpanFull(),

                        ])
                        ->columnSpanFull(),


                    /*
                    |--------------------------------------------------------------------------
                    | SUMMARY
                    |--------------------------------------------------------------------------
                    */

                    Section::make('Purchasing Summary')

                        ->description('Purchasing information.')

                        ->icon('heroicon-o-shopping-cart')

                        ->collapsed()

                        ->schema([

                            Placeholder::make('supplier_count')
                                ->label('Suppliers')
                                ->content(fn ($record) => $record
                                    ? $record->suppliers()->count()
                                    : '-'),

                            Placeholder::make('preferred_supplier')
                                ->label('Preferred Supplier')
                                ->content(function ($record) {

                                    if (! $record) {
                                        return '-';
                                    }

                                    return optional(
                                        $record->suppliers()
                                            ->where('is_preferred', true)
                                            ->first()
                                    )->supplier?->supplier_name ?? '-';

                                }),

                            Placeholder::make('last_purchase')
                                ->label('Last Purchase')
                                ->content(function ($record) {

                                    if (! $record) {
                                        return '-';
                                    }

                                    return optional(
                                        $record->suppliers()
                                            ->latest('last_purchase_date')
                                            ->first()
                                    )->last_purchase_date ?? '-';

                                }),

                        ])

                        ->columns(3)

                        ->columnSpanFull(),


                    Section::make('Inventory Summary')

                        ->description('Current inventory summary.')

                        ->icon('heroicon-o-building-storefront')

                        ->collapsed()

                        ->schema([

                            Placeholder::make('warehouse_count')
                                ->label('Warehouses')
                                ->content(fn ($record) => $record
                                    ? $record->stocks()->count()
                                    : '-'),

                            Placeholder::make('stock_balance')
                                ->label('Current Stock')
                                ->content(fn ($record) => $record
                                    ? $record->stocks()->sum('qty_on_hand')
                                    : '-'),

                            Placeholder::make('available_stock')
                                ->label('Available')
                                ->content(fn ($record) => $record
                                    ? $record->stocks()->sum('qty_on_hand')
                                    : '-'),

                        ])

                        ->columns(3)

                        ->columnSpanFull(),

                    

                    Section::make('Price Summary')

                        ->description('Current pricing information.')

                        ->icon('heroicon-o-banknotes')

                        ->collapsed()

                        ->schema([

                            Placeholder::make('price_count')
                                ->label('Price Lists')
                                ->content(fn ($record) => $record
                                    ? $record->prices()->count()
                                    : '-'),

                            Placeholder::make('default_price')
                                ->label('Default Price')
                                ->content(function ($record) {

                                    if (! $record) {
                                        return '-';
                                    }

                                    return optional(
                                        $record->prices()
                                            ->where('is_default', true)
                                            ->first()
                                    )->selling_price ?? '-';

                                }),

                        ])

                        ->columns(2)

                        ->columnSpanFull(),






                    /*
                    |--------------------------------------------------------------------------
                    | Business Summary
                    |--------------------------------------------------------------------------
                    */

                    Section::make('Business Summary')
                        ->description(
                            'Read-only information generated automatically from Purchasing, Inventory and Pricing modules.'
                        )
                        ->icon('heroicon-o-chart-bar')
                        ->collapsible()
                        ->persistCollapsed()
                        ->columns(3)

                        ->schema([

                            Placeholder::make('purchasing_summary')
                                ->label('Purchasing')
                                ->content(function ($record) {

                                    if (! $record) {
                                        return 'No supplier assigned';
                                    }

                                    return sprintf(
                                        "Suppliers : %d\nPreferred : %s",
                                        $record->suppliers()->count(),
                                        optional(
                                            $record->suppliers()
                                                ->where('is_preferred', true)
                                                ->first()
                                        )->supplier?->supplier_name ?? '-'
                                    );
                                }),

                            Placeholder::make('inventory_summary')
                                ->label('Inventory')
                                ->content(function ($record) {

                                    if (! $record) {
                                        return 'No stock available';
                                    }

                                    return sprintf(
                                        "Warehouse : %d\nStock : %s",
                                        $record->stocks()->count(),
                                        number_format(
                                            $record->stocks()->sum('qty_on_hand'),
                                            2
                                        )
                                    );
                                }),

                            Placeholder::make('price_summary')
                                ->label('Pricing')
                                ->content(function ($record) {

                                    if (! $record) {
                                        return 'No pricing available';
                                    }

                                    return sprintf(
                                        "Price List : %d\nDefault : %s",
                                        $record->prices()->count(),
                                        optional(
                                            $record->prices()
                                                ->where('is_default', true)
                                                ->first()
                                        )->selling_price ?? '-'
                                    );
                                }),

                        ])
                        ->columnSpanFull(),


                    /*
                    |--------------------------------------------------------------------------
                    | System Information
                    |--------------------------------------------------------------------------
                    */                    

                    Section::make('System Information')
                        ->description('System generated information.')
                        ->icon('heroicon-o-information-circle')
                        ->collapsible()
                        ->collapsed()
                        ->schema([

                            Placeholder::make('uuid')
                                ->label('UUID')
                                ->content(fn ($record) => $record?->uuid ?? '-'),

                            Placeholder::make('created_at')
                                ->label('Created')
                                ->content(fn ($record) => $record?->created_at?->format('d M Y H:i') ?? '-'),

                            Placeholder::make('updated_at')
                                ->label('Last Updated')
                                ->content(fn ($record) => $record?->updated_at?->format('d M Y H:i') ?? '-'),

                        ])
                        ->columns(3)
                        ->columnSpanFull(),

            ]);

    }
}