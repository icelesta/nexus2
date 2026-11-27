<?php

declare(strict_types=1);

namespace App\Filament\Resources\GoodsReceipts\Schemas;

use App\Models\AssignmentDirectMarket;
use App\Models\DirectMarket;
use App\Models\PurchaseOrder;
use App\Models\GoodsReceipt;
use App\Models\Supplier;
use App\Models\TransactionNumbering;

use App\Services\Numbering\NumberingService;
use App\Services\Purchasing\ReceivingRecordEligibilityService;

use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;

use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Schemas\Components\Utilities\Get;

class GoodsReceiptForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema->components([

            Section::make('Receiving Record Information')
                ->icon('heroicon-o-clipboard-document-check')
                ->extraAttributes([
                    'class' => 'overflow-hidden rounded-xl border border-slate-200 bg-white shadow-sm',
                ])
                ->schema([

                    /*
                    |--------------------------------------------------------------------------
                    | ROW 1
                    | Supplier | Source Type | Source No
                    |--------------------------------------------------------------------------
                    */

                    Grid::make(12)
                        ->extraAttributes([
                            'class' => 'gap-y-4',
                        ])
                        ->schema([

                            /*
                            |--------------------------------------------------------------------------
                            | SUPPLIER
                            |--------------------------------------------------------------------------
                            */

                            Select::make('supplier_id')
                                ->label('Supplier')
                                ->options(
                                    function (): array {
                                        $service = app(
                                            ReceivingRecordEligibilityService::class
                                        );

                                        /*
                                        |--------------------------------------------------------------------------
                                        | SUPPLIER FROM ELIGIBLE PURCHASE ORDER
                                        |--------------------------------------------------------------------------
                                        */

                                            $eligiblePurchaseOrders = $service
                                                ->getEligiblePurchaseOrders();

                                            $poSupplierIds = $eligiblePurchaseOrders
                                                ->pluck('supplier_id')
                                                ->filter()
                                                ->unique();

                                            $admSupplierIds = $service
                                            ->getEligibleAssignmentDirectMarkets()
                                            ->flatMap(
                                                fn (AssignmentDirectMarket $assignment) =>
                                                    $assignment->items->pluck('supplier_id')
                                            )
                                            ->filter()
                                            ->unique();

                                        /*
                                        |--------------------------------------------------------------------------
                                        | MERGE SUPPLIERS
                                        |--------------------------------------------------------------------------
                                        */

                                        $supplierIds = $poSupplierIds
                                            ->merge($admSupplierIds)
                                            ->unique()
                                            ->values();

                                        if ($supplierIds->isEmpty()) {
                                            return [];
                                        }


                                        /*
                                        |--------------------------------------------------------------------------
                                        | LOAD ONLY ELIGIBLE SUPPLIERS
                                        |--------------------------------------------------------------------------
                                        */

                                        return Supplier::query()
                                            ->whereIn('id', $supplierIds)
                                            ->orderBy('supplier_code')
                                            ->get()
                                            ->mapWithKeys(
                                                fn (Supplier $supplier): array => [
                                                    $supplier->getKey() => collect([
                                                        $supplier->supplier_code ?? null,
                                                        $supplier->supplier_name
                                                            ?? $supplier->name
                                                            ?? null,
                                                    ])
                                                        ->filter()
                                                        ->implode(' - '),
                                                ]
                                            )
                                            ->all();
                                    }
                                )
                                ->searchable()
                                ->preload()

                                ->default(
                                    fn (): ?int => request()->filled('supplier_id')
                                        ? (int) request()->query('supplier_id')
                                        : null
                                )
                                ->getOptionLabelUsing(
                                    function ($value): ?string {
                                        if (blank($value)) {
                                            return null;
                                        }

                                        $supplier = Supplier::query()
                                            ->find($value);

                                        if (! $supplier) {
                                            return null;
                                        }

                                        return collect([
                                            $supplier->supplier_code ?? null,
                                            $supplier->supplier_name
                                                ?? $supplier->name
                                                ?? null,
                                        ])
                                            ->filter()
                                            ->implode(' - ');
                                    }
                                )
                                ->disabledOn('edit')
                                ->required()
                                ->live()
                                ->extraFieldWrapperAttributes([
                                    'class' => 'rounded-lg',
                                ])
                                ->extraInputAttributes([
                                    'class' => 'rounded-lg border-slate-200 bg-slate-50/60 shadow-sm',
                                ])
                                ->afterStateUpdated(
                                    function (
                                        $state,
                                        callable $set,
                                        callable $get
                                    ): void {

                                        /*
                                        |--------------------------------------------------------------------------
                                        | RESET DEPENDENT FIELDS
                                        |--------------------------------------------------------------------------
                                        */

                                        $set('source_id', null);
                                        $set('rr_no_preview', null);
                                        $set('currency_display', null);

                                        /*
                                        |--------------------------------------------------------------------------
                                        | PAYMENT TERM COMES FROM SUPPLIER / VENDOR MASTER
                                        |--------------------------------------------------------------------------
                                        */

                                        $supplier = Supplier::query()
                                            ->with('paymentTerm')
                                            ->find($state);

                                        $set(
                                            'payment_term_display',
                                            $supplier?->paymentTerm?->term_name
                                        );
                                    }
                                )
                                ->columnSpan(4),

                            /*
                            |--------------------------------------------------------------------------
                            | SOURCE TYPE
                            |--------------------------------------------------------------------------
                            */

                            Select::make('source_type')
                                ->label('Source Type')
                                ->options([
                                    'PURCHASE_ORDER' =>
                                        'Purchase Order',

                                    'ASSIGNMENT_DIRECT_MARKET' =>
                                        'Direct Market',
                                ])
                                ->placeholder('Select an option')
                                ->required()
                                ->disabledOn('edit')
                                ->live()
                                ->extraFieldWrapperAttributes([
                                    'class' => 'rounded-lg',
                                ])
                                ->extraInputAttributes([
                                    'class' => 'rounded-lg border-slate-200 bg-slate-50/60 shadow-sm',
                                ])
                                ->afterStateUpdated(
                                    function (
                                        callable $set,
                                        callable $get
                                    ): void {

                                        /*
                                        |--------------------------------------------------------------------------
                                        | RESET SOURCE-DEPENDENT FIELDS
                                        |--------------------------------------------------------------------------
                                        */

                                        $set('source_id', null);
                                        $set('rr_no_preview', null);
                                        $set('currency_display', null);
                                    }
                                )
                                ->default(
                                    fn (): ?string => in_array(
                                        request()->query('source_type'),
                                        [
                                            'PURCHASE_ORDER',
                                            'ASSIGNMENT_DIRECT_MARKET',
                                        ],
                                        true
                                    )
                                        ? request()->query('source_type')
                                        : null
                                )
                                ->columnSpan(4),

                            /*
                            |--------------------------------------------------------------------------
                            | SOURCE DOCUMENT
                            |--------------------------------------------------------------------------
                            */

                            Select::make('source_id')
                                ->disabledOn('edit')
                                ->label(
                                    fn (callable $get): string =>
                                        match ($get('source_type')) {
                                            'ASSIGNMENT_DIRECT_MARKET' =>
                                                'DM No',

                                            'PURCHASE_ORDER' =>
                                                'PO No',

                                            default =>
                                                'Source No',
                                        }
                                )
                                ->options(
                                    function (
                                        callable $get
                                    ): array {

                                        $supplierId =
                                            $get('supplier_id');

                                        $sourceType =
                                            $get('source_type');

                                        if (
                                            blank($supplierId)
                                            ||
                                            blank($sourceType)
                                        ) {
                                            return [];
                                        }

                                        $service = app(
                                            ReceivingRecordEligibilityService::class
                                        );

                                        /*
                                        |--------------------------------------------------------------------------
                                        | PURCHASE ORDER
                                        |--------------------------------------------------------------------------
                                        */

                                        if (
                                            $sourceType ===
                                            'PURCHASE_ORDER'
                                        ) {

                                        $eligiblePurchaseOrders = $service
                                            ->getEligiblePurchaseOrders()
                                            ->filter(
                                                fn (PurchaseOrder $po): bool =>
                                                    (int) $po->supplier_id === (int) $supplierId
                                            );

                                        return $eligiblePurchaseOrders
                                            ->filter(
                                                fn (PurchaseOrder $po): bool =>
                                                    (int) $po->supplier_id === (int) $supplierId
                                            )
                                            ->mapWithKeys(
                                                fn (PurchaseOrder $po): array => [
                                                    $po->getKey() => $po->document_no,
                                                ]
                                            )
                                            ->all();
                                        }

                                        /*
                                        |--------------------------------------------------------------------------
                                        | DIRECT MARKET
                                        |--------------------------------------------------------------------------
                                        */

                                        if (
                                            $sourceType ===
                                            'ASSIGNMENT_DIRECT_MARKET'
                                        ) {

                                            $currentSourceId = (int) ($get('source_id') ?? 0);

                                            $assignments = $service
                                                ->getEligibleAssignmentDirectMarkets()
                                                ->filter(
                                                    fn (AssignmentDirectMarket $assignment): bool =>
                                                        $assignment->items
                                                            ->where('supplier_id', (int) $supplierId)
                                                            ->isNotEmpty()
                                                );

                                            $directMarketIds =
                                                $assignments
                                                    ->pluck('direct_market_id')
                                                    ->filter()
                                                    ->unique()
                                                    ->values();

                                            if (
                                                $directMarketIds->isEmpty()
                                            ) {
                                                return [];
                                            }

                                            $directMarkets =
                                                DirectMarket::query()
                                                    ->whereIn(
                                                        'id',
                                                        $directMarketIds
                                                    )
                                                    ->pluck(
                                                        'dm_no',
                                                        'id'
                                                    );

                                            return $assignments
                                                ->mapWithKeys(
                                                    fn (
                                                        AssignmentDirectMarket $assignment
                                                    ): array => [
                                                        $assignment->getKey() =>
                                                            $directMarkets->get(
                                                                $assignment->direct_market_id
                                                            )
                                                            ??
                                                            $assignment->document_no,
                                                    ]
                                                )
                                                ->all();
                                        }
                                    }
                                )
                                ->disabledOn('edit')
                                ->searchable()
                                ->preload()
                                ->required()
                                ->live()
                                ->disabled(
                                    fn (
                                        callable $get
                                    ): bool =>
                                        blank(
                                            $get('supplier_id')
                                        )
                                )
                                ->extraFieldWrapperAttributes([
                                    'class' => 'rounded-lg',
                                ])
                                ->extraInputAttributes([
                                    'class' => 'rounded-lg border-slate-200 bg-slate-50/60 shadow-sm',
                                ])
                                ->helperText(
                                    'Select Supplier first, then select the eligible source document.'
                                )
                                ->afterStateUpdated(
                                    function (
                                        $state,
                                        callable $set,
                                        callable $get
                                    ): void {

                                        /*
                                        |--------------------------------------------------------------------------
                                        | RR NUMBER PREVIEW
                                        |--------------------------------------------------------------------------
                                        */

                                        try {

                                            $set(
                                                'rr_no_preview',
                                                self::getReceivingRecordNumberPreview(
                                                    $get
                                                )
                                            );

                                        } catch (
                                            \Throwable $exception
                                        ) {

                                            report($exception);

                                            $set(
                                                'rr_no_preview',
                                                null
                                            );
                                        }

                                        /*
                                        |--------------------------------------------------------------------------
                                        | CURRENCY FROM SOURCE / JOB HEADER
                                        |--------------------------------------------------------------------------
                                        */

                                        $sourceType =
                                            $get('source_type');

                                        $currencyCode = null;

                                        /*
                                        |--------------------------------------------------------------------------
                                        | PURCHASE ORDER
                                        |--------------------------------------------------------------------------
                                        */

                                        if (
                                            $sourceType ===
                                            'PURCHASE_ORDER'
                                        ) {

                                            $purchaseOrder =
                                                PurchaseOrder::query()
                                                    ->with('currency')
                                                    ->find($state);

                                            $currencyCode =
                                                $purchaseOrder
                                                    ?->currency
                                                    ?->currency_code;
                                        }

                                        /*
                                        |--------------------------------------------------------------------------
                                        | DIRECT MARKET
                                        |--------------------------------------------------------------------------
                                        */

                                        elseif (
                                            $sourceType ===
                                            'ASSIGNMENT_DIRECT_MARKET'
                                        ) {

                                            $assignment =
                                                AssignmentDirectMarket::query()
                                                    ->find($state);

                                            if (
                                                $assignment
                                                    ?->direct_market_id
                                            ) {

                                                $directMarket =
                                                    DirectMarket::query()
                                                        ->with('currency')
                                                        ->find(
                                                            $assignment
                                                                ->direct_market_id
                                                        );

                                                $currencyCode =
                                                    $directMarket
                                                        ?->currency
                                                        ?->currency_code;
                                            }
                                        }

                                        /*
                                        |--------------------------------------------------------------------------
                                        | SET CURRENCY
                                        |--------------------------------------------------------------------------
                                        */

                                        $set(
                                            'currency_display',
                                            $currencyCode
                                        );
                                    }
                                )
                                ->afterStateHydrated(
                                    function (
                                        $state,
                                        callable $set
                                    ): void {

                                        if (blank($state)) {
                                            return;
                                        }

                                        $supplier = Supplier::query()
                                            ->with('paymentTerm')
                                            ->find($state);

                                        $set(
                                            'payment_term_display',
                                            $supplier?->paymentTerm?->term_name
                                        );
                                    }
                                )
                                ->columnSpan(4),

                        ]),

                    /*
                    |--------------------------------------------------------------------------
                    | ROW 2
                    | Receiving Date | RR No | Received By
                    |--------------------------------------------------------------------------
                    */

                    Grid::make(12)
                        ->extraAttributes([
                            'class' => 'gap-y-4',
                        ])
                        ->schema([

                            /*
                            |--------------------------------------------------------------------------
                            | RECEIVING DATE
                            |--------------------------------------------------------------------------
                            */

                            DatePicker::make('receipt_date')
                                ->label('Receiving Date')
                                ->default(
                                    now()->toDateString()
                                )
                                ->required()
                                ->native(false)
                                ->disabled()
                                ->dehydrated(true)
                                ->extraFieldWrapperAttributes([
                                    'class' => 'rounded-lg',
                                ])
                                ->extraInputAttributes([
                                    'class' => 'rounded-lg border-slate-200 bg-slate-50 shadow-sm',
                                ])
                                ->columnSpan(4),

                            /*
                            |--------------------------------------------------------------------------
                            | RR NUMBER
                            |--------------------------------------------------------------------------
                            */

                            TextInput::make('rr_no_preview')
                                ->label('RR No')
                                ->placeholder(
                                    'Select PO / DM to generate preview'
                                )
                                ->readOnly()
                                ->dehydrated(false)
                                ->suffix('Auto')
                                ->extraFieldWrapperAttributes([
                                    'class' => 'rounded-lg',
                                ])
                                ->extraInputAttributes([
                                    'class' => 'rounded-lg border-blue-200 bg-blue-50/70 font-semibold text-blue-700 shadow-sm',
                                ])
                                ->columnSpan(4),

                            /*
                            |--------------------------------------------------------------------------
                            | RECEIVED BY
                            |--------------------------------------------------------------------------
                            */

                            TextInput::make('received_by')
                                ->label('Received By')
                                ->default(
                                    fn (): string =>
                                        auth()->user()?->name
                                        ?? '-'
                                )
                                ->disabled()
                                ->dehydrated(false)
                                ->extraFieldWrapperAttributes([
                                    'class' => 'rounded-lg',
                                ])
                                ->extraInputAttributes([
                                    'class' => 'rounded-lg border-slate-200 bg-slate-50 shadow-sm',
                                ])
                                ->columnSpan(4),

                        ]),

                    /*
                    |--------------------------------------------------------------------------
                    | ROW 3
                    | Payment Term | Currency
                    |--------------------------------------------------------------------------
                    */

                    Grid::make(12)
                        ->extraAttributes([
                            'class' => 'gap-y-4',
                        ])
                        ->schema([

                            /*
                            |--------------------------------------------------------------------------
                            | PAYMENT TERM
                            |--------------------------------------------------------------------------
                            |
                            | Payment Term is taken from Supplier / Vendor Master.
                            |
                            */

                            TextInput::make('payment_term_display')
                                ->label('Payment Term')
                                ->default(function (Get $get): ?string {
                                    $supplierId = $get('supplier_id');

                                    if (blank($supplierId)) {
                                        return null;
                                    }

                                    return Supplier::query()
                                        ->with('paymentTerm')
                                        ->find($supplierId)
                                        ?->paymentTerm
                                        ?->term_name;
                                })
                                ->placeholder('-')
                                ->disabled()
                                ->dehydrated(false)
                                ->extraFieldWrapperAttributes([
                                    'class' => 'rounded-lg',
                                ])
                                ->extraInputAttributes([
                                    'class' => 'rounded-lg border-slate-200 bg-slate-50 shadow-sm',
                                ])
                                ->columnSpan(4),

                            /*
                            |--------------------------------------------------------------------------
                            | CURRENCY
                            |--------------------------------------------------------------------------
                            |
                            | Currency is taken from the selected Source / Job Header.
                            |
                            */

                            TextInput::make('currency_display')
                                ->label('Currency')
                                ->placeholder('-')
                                ->disabled()
                                ->dehydrated(false)
                                ->extraFieldWrapperAttributes([
                                    'class' => 'rounded-lg',
                                ])
                                ->extraInputAttributes([
                                    'class' => 'rounded-lg border-slate-200 bg-slate-50 shadow-sm',
                                ])
                                ->columnSpan(4),

                        ]),

                    /*
                    |--------------------------------------------------------------------------
                    | ROW 4
                    | Document Number | Remark
                    |--------------------------------------------------------------------------
                    */

                    Grid::make(12)
                        ->extraAttributes([
                            'class' => 'gap-y-4',
                        ])
                        ->schema([

                            /*
                            |--------------------------------------------------------------------------
                            | DOCUMENT NUMBER
                            |--------------------------------------------------------------------------
                            |
                            | Optional free-text external/reference document number.
                            |
                            */

                            TextInput::make('document_number')
                                ->label('Document Number')
                                ->placeholder(
                                    'Optional — enter external document number'
                                )
                                ->maxLength(255)
                                ->nullable()
                                ->extraFieldWrapperAttributes([
                                    'class' => 'rounded-lg',
                                ])
                                ->extraInputAttributes([
                                    'class' => 'rounded-lg border-slate-200 bg-white shadow-sm focus:border-blue-400 focus:ring-blue-400/20',
                                ])
                                ->columnSpan(6),

                            /*
                            |--------------------------------------------------------------------------
                            | REMARK
                            |--------------------------------------------------------------------------
                            |
                            | Optional free-text remarks.
                            |
                            */

                            Textarea::make('remarks')
                                ->label('Remark')
                                ->placeholder('Optional')
                                ->rows(1)
                                ->maxLength(1000)
                                ->extraFieldWrapperAttributes([
                                    'class' => 'rounded-lg',
                                ])
                                ->extraInputAttributes([
                                    'class' => 'rounded-lg border-slate-200 bg-white shadow-sm focus:border-blue-400 focus:ring-blue-400/20',
                                ])
                                ->columnSpan(6),

                        ]),

                ])
                ->columnSpanFull(),

        ]);
    }

    /*
    |--------------------------------------------------------------------------
    | RR NUMBER PREVIEW
    |--------------------------------------------------------------------------
    |
    | IMPORTANT:
    | - Menggunakan NumberingService::preview()
    | - Tidak increment current_number
    | - Tidak mengubah Master Transaction Numbering
    | - Branch / Department mengikuti source document
    |
    */

    private static function getReceivingRecordNumberPreview(
        callable $get
    ): ?string {

        $sourceType = $get('source_type');
        $sourceId = $get('source_id');

        if (
            blank($sourceType)
            ||
            blank($sourceId)
        ) {
            return null;
        }

        $context = match ($sourceType) {

            'PURCHASE_ORDER' =>
                PurchaseOrder::query()
                    ->find($sourceId),

            'ASSIGNMENT_DIRECT_MARKET' =>
                AssignmentDirectMarket::query()
                    ->find($sourceId),

            default =>
                null,
        };

        if (! $context) {
            return null;
        }

        return app(NumberingService::class)->preview(
            TransactionNumbering::DOC_GOODS_RECEIPT,
            $context->company_id ?? null,
            $context->business_unit_id ?? null,
            $context->branch_id ?? null,
            $context->department_id ?? null,
        );
    }
}