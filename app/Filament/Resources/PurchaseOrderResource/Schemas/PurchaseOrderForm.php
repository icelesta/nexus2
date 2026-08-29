<?php

declare(strict_types=1);

namespace App\Filament\Resources\PurchaseOrderResource\Schemas;

use App\Models\PurchaseOrder;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Placeholder;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class PurchaseOrderForm
{
    public static function configure(
        Schema $schema,
    ): Schema {

        return $schema
            ->columns(1)
            ->components([

            /*
            |--------------------------------------------------------------------------
            | Purchase Order Information
            |--------------------------------------------------------------------------
            */

            Section::make('Purchase Order Information')
                ->description('General information of this Purchase Order document.')
                ->schema([

                    /*
                    |--------------------------------------------------------------------------
                    | Row 1
                    |--------------------------------------------------------------------------
                    */

                    Grid::make(12)
                        ->schema([

                            Placeholder::make('document_status')
                                ->label('Document Status')
                                ->content(fn ($record) =>
                                    $record?->status ?? '-'
                                )
                                ->columnSpan(2),

                            Placeholder::make('purchasing_pic')
                                ->label('Purchasing PIC')
                                ->content(fn ($record) =>
                                    $record?->purchasingPic?->name
                                        ?? auth()->user()?->name
                                        ?? '-'
                                )
                                ->columnSpan(2),

                            Placeholder::make('supplier')
                                ->label('Supplier')
                                ->content(fn ($record) =>
                                    $record?->supplier?->supplier_name
                                        ?? '-'
                                )
                                ->columnSpan(4),

                            Placeholder::make('currency')
                                ->label('Currency')
                                ->content(fn ($record) =>
                                    $record?->currency?->currency_code
                                        ?? $record?->currency_code
                                        ?? '-'
                                )
                                ->columnSpan(2),

                            Placeholder::make('exchange_rate')
                                ->label('Exchange Rate')
                                ->content(fn ($record) =>
                                    $record?->exchange_rate !== null
                                        ? number_format(
                                            (float) $record->exchange_rate,
                                            4,
                                            '.',
                                            ''
                                        )
                                        : '-'
                                )
                                ->columnSpan(2),

                        ]),

                    /*
                    |--------------------------------------------------------------------------
                    | Row 2
                    |--------------------------------------------------------------------------
                    */

                    Grid::make(12)
                        ->schema([

                            Placeholder::make('payment_instruction_display')
                                ->label('Payment Instruction')
                                ->content(fn ($record) =>
                                    $record?->payment_instruction ?? '-'
                                )
                                ->columnSpan(3),

                            DatePicker::make('expected_delivery_date')
                                ->label('Expected Delivery Date')
                                ->columnSpan(3),

                            Textarea::make('remarks')
                                ->label('Remarks')
                                ->rows(2)
                                ->columnSpan(6),

                        ]),

                ])
                ->columnSpanFull(),

                /*
                |--------------------------------------------------------------------------
                | Reference Information
                |--------------------------------------------------------------------------
                */

                Section::make('Reference Information')
                    ->description(
                        'Reference information copied from Assignment Material Requisition.'
                    )
                    ->columnSpanFull()
                    ->extraAttributes([
                        'class' => 'po-section',
                    ])
                    ->columns(4)
                    ->schema([

                        /*
                        |--------------------------------------------------------------------------
                        | Assignment / Material Requisition
                        |--------------------------------------------------------------------------
                        */

                        Placeholder::make('assignment_no')
                            ->label('Assignment No')
                            ->content(
                                fn (?PurchaseOrder $record) =>
                                    $record?->assignmentMaterialRequisition?->document_no ?? '-'
                            ),

                        Placeholder::make('material_requisition_no')
                            ->label('Material Requisition')
                            ->content(
                                fn (?PurchaseOrder $record) =>
                                    $record?->purchaseRequisition?->pr_no ?? '-'
                            ),

                        Placeholder::make('company')
                            ->label('Company')
                            ->content(
                                fn (?PurchaseOrder $record) =>
                                    $record?->company?->company_name ?? '-'
                            ),

                        Placeholder::make('business_unit')
                            ->label('Business Unit')
                            ->content(
                                fn (?PurchaseOrder $record) =>
                                    $record?->businessUnit?->business_unit_name ?? '-'
                            ),

                        /*
                        |--------------------------------------------------------------------------
                        | Organization
                        |--------------------------------------------------------------------------
                        */

                        Placeholder::make('branch')
                            ->label('Branch')
                            ->content(
                                fn (?PurchaseOrder $record) =>
                                    $record?->branch?->branch_name ?? '-'
                            ),

                        Placeholder::make('department')
                            ->label('Department')
                            ->content(
                                fn (?PurchaseOrder $record) =>
                                    $record?->department?->department_name ?? '-'
                            ),

                        Placeholder::make('section')
                            ->label('Section')
                            ->content(
                                fn (?PurchaseOrder $record) =>
                                    $record?->section?->section_name ?? '-'
                            ),

                        Placeholder::make('warehouse')
                            ->label('Warehouse')
                            ->content(
                                fn (?PurchaseOrder $record) =>
                                    $record?->warehouse?->warehouse_name ?? '-'
                            ),

                        /*
                        |--------------------------------------------------------------------------
                        | Request Information
                        |--------------------------------------------------------------------------
                        */

                        Placeholder::make('requester')
                            ->label('Requester')
                            ->content(
                                fn (?PurchaseOrder $record) =>
                                    $record?->requester?->name ?? '-'
                            ),

                        Placeholder::make('request_date')
                            ->label('Request Date')
                            ->content(
                                fn (?PurchaseOrder $record) =>
                                    $record?->request_date?->format('d M Y') ?? '-'
                            ),

                        Placeholder::make('required_date')
                            ->label('Required Date')
                            ->content(
                                fn (?PurchaseOrder $record) =>
                                    $record?->required_date?->format('d M Y') ?? '-'
                            ),

                        Placeholder::make('reference_no')
                            ->label('Reference Number')
                            ->content(
                                fn (?PurchaseOrder $record) =>
                                    $record?->reference_no ?? '-'
                            ),

                    ]),

                /*
                |--------------------------------------------------------------------------
                | Supplier Information
                |--------------------------------------------------------------------------
                |
                | UI ONLY
                |
                | Supplier Currency and Supplier Status are intentionally hidden.
                | Supplier master data remains unchanged.
                |
                | Layout:
                |
                | Row 1:
                | Supplier       : 3
                | Email          : 3
                | Phone          : 3
                | Payment Terms  : 3
                |
                | Row 2:
                | Address        : 12
                |
                |--------------------------------------------------------------------------
                */

                Section::make('Supplier Information')
                    ->description(
                        'Supplier information for this Purchase Order.'
                    )
                    ->columnSpanFull()
                    ->extraAttributes([
                        'class' => 'po-section',
                    ])
                    ->columns(12)
                    ->schema([

                        /*
                        |--------------------------------------------------------------------------
                        | Supplier
                        |--------------------------------------------------------------------------
                        */

                        Placeholder::make('supplier_name')
                            ->label('Supplier')
                            ->content(
                                fn (?PurchaseOrder $record) =>
                                    $record?->supplier?->supplier_name ?? '-'
                            )
                            ->columnSpan(3),

                        /*
                        |--------------------------------------------------------------------------
                        | Email
                        |--------------------------------------------------------------------------
                        */

                        Placeholder::make('email')
                            ->label('Email')
                            ->content(
                                fn (?PurchaseOrder $record) =>
                                    $record?->supplier?->email ?? '-'
                            )
                            ->columnSpan(3),

                        /*
                        |--------------------------------------------------------------------------
                        | Phone
                        |--------------------------------------------------------------------------
                        */

                        Placeholder::make('phone')
                            ->label('Phone')
                            ->content(
                                fn (?PurchaseOrder $record) =>
                                    $record?->supplier?->phone ?? '-'
                            )
                            ->columnSpan(3),

                        /*
                        |--------------------------------------------------------------------------
                        | Payment Terms
                        |--------------------------------------------------------------------------
                        */

                        Placeholder::make('payment_terms')
                            ->label('Payment Terms')
                            ->content(
                                fn (?PurchaseOrder $record) =>
                                    $record?->supplier?->paymentTerm?->display_name ?? '-'
                            )
                            ->columnSpan(3),

                        /*
                        |--------------------------------------------------------------------------
                        | Address
                        |--------------------------------------------------------------------------
                        |
                        | Full-width supplier address information.
                        |
                        */

                        Placeholder::make('supplier_address')
                            ->label('Address')
                            ->content(
                                fn (?PurchaseOrder $record) =>
                                    $record?->supplier?->address ?? '-'
                            )
                            ->extraAttributes([
                                'class' =>
                                    'rounded-lg bg-gray-50 px-4 py-3 dark:bg-gray-900/40',
                            ])
                            ->columnSpanFull(),

                    ]),



                /*
                |--------------------------------------------------------------------------
                | Shipping Information
                |--------------------------------------------------------------------------
                */

                Section::make('Shipping To')
                    ->description(
                        'Shipping destination information for this Purchase Order.'
                    )
                    ->columnSpanFull()
                    ->extraAttributes([
                        'class' => 'po-section',
                    ])
                    ->collapsible(false)
                    ->compact(false)
                    ->schema([

                        Grid::make(2)
                            ->schema([

                                /*
                                |--------------------------------------------------------------------------
                                | Shipping Address
                                |--------------------------------------------------------------------------
                                */

                                Placeholder::make('shipping_to_address')
                                    ->hiddenLabel()
                                    ->content(function (?PurchaseOrder $record) {

                                        $shipping = $record?->shippingAddress;

                                        if (! $shipping) {
                                            return '-';
                                        }

                                        return new \Illuminate\Support\HtmlString(
                                            '<div style="
                                                line-height:1.8;
                                                padding:4px 0;
                                            ">

                                                <strong style="
                                                    font-size:15px;
                                                    font-weight:600;
                                                ">'
                                                    . e($shipping->shipping_name) .
                                                '</strong><br>

                                                ' . e($shipping->address) . '<br>

                                                ' . e($shipping->city) . ', '
                                                    . e($shipping->province) . ' '
                                                    . e($shipping->postal_code) . '<br>

                                                ' . e($shipping->country) . '

                                            </div>'
                                        );
                                    }),

                                /*
                                |--------------------------------------------------------------------------
                                | Shipping Contact
                                |--------------------------------------------------------------------------
                                */

                                Placeholder::make('shipping_to_contact')
                                    ->hiddenLabel()
                                    ->content(function (?PurchaseOrder $record) {

                                        $shipping = $record?->shippingAddress;

                                        if (! $shipping) {
                                            return '-';
                                        }

                                        return new \Illuminate\Support\HtmlString(

                                            '<div style="
                                                line-height:1.9;
                                            ">

                                                <div>
                                                    <strong style="display:inline-block;width:140px;">
                                                        Attention
                                                    </strong>
                                                    <span>: '
                                                        . e($shipping->attention) .
                                                    '</span>
                                                </div>

                                                <div>
                                                    <strong style="display:inline-block;width:140px;">
                                                        Contact Person
                                                    </strong>
                                                    <span>: '
                                                        . e($shipping->contact_person) .
                                                    '</span>
                                                </div>

                                                <div>
                                                    <strong style="display:inline-block;width:140px;">
                                                        Phone
                                                    </strong>
                                                    <span>: '
                                                        . e($shipping->phone) .
                                                    '</span>
                                                </div>

                                                <div>
                                                    <strong style="display:inline-block;width:140px;">
                                                        Email
                                                    </strong>
                                                    <span>: '
                                                        . e($shipping->email) .
                                                    '</span>
                                                </div>

                                            </div>'
                                        );
                                    }),

                            ]),

                    ]),

                /*
                |--------------------------------------------------------------------------
                | Financial Summary
                |--------------------------------------------------------------------------
                */

                Section::make('Financial Summary')
                    ->description(
                        'Financial summary calculated automatically from Purchase Order Items.'
                    )
                    ->columnSpanFull()
                    ->extraAttributes([
                        'class' => 'po-section',
                    ])
                    ->columns(4)
                    ->schema([

                        /*
                        |--------------------------------------------------------------------------
                        | Subtotal
                        |--------------------------------------------------------------------------
                        */

                        Placeholder::make('subtotal_display')
                            ->label('Subtotal')
                            ->content(
                                fn (?PurchaseOrder $record) =>
                                    'Rp. ' . number_format(
                                        (float) ($record?->subtotal ?? 0),
                                        2
                                    )
                            ),

                        /*
                        |--------------------------------------------------------------------------
                        | Discount
                        |--------------------------------------------------------------------------
                        */

                        Placeholder::make('discount_display')
                            ->label('Discount')
                            ->content(
                                fn (?PurchaseOrder $record) =>
                                    'Rp. ' . number_format(
                                        (float) ($record?->discount_amount ?? 0),
                                        2
                                    )
                            ),

                        /*
                        |--------------------------------------------------------------------------
                        | Tax
                        |--------------------------------------------------------------------------
                        */

                        Placeholder::make('tax_display')
                            ->label('Tax')
                            ->content(
                                fn (?PurchaseOrder $record) =>
                                    'Rp. ' . number_format(
                                        (float) ($record?->tax_amount ?? 0),
                                        2
                                    )
                            ),

                        /*
                        |--------------------------------------------------------------------------
                        | Grand Total
                        |--------------------------------------------------------------------------
                        */

                        Placeholder::make('grand_total_display')
                            ->label('Grand Total')
                            ->content(
                                fn (?PurchaseOrder $record) =>
                                    'Rp. ' . number_format(
                                        (float) ($record?->grand_total ?? 0),
                                        2
                                    )
                            )
                            ->extraAttributes([
                                'class' =>
                                    'font-semibold text-primary-600 dark:text-primary-400',
                            ]),

                    ]),

            ]);

    }
}