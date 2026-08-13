<?php

declare(strict_types=1);

namespace App\Filament\Resources\PurchaseOrderResource\Schemas;

use App\Models\PurchaseOrder;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Placeholder;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
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
                ->columnSpanFull()
                ->collapsible(false)
                ->compact(false)
                ->schema([

                    Grid::make(12)
                        ->extraAttributes([
                            'class' => 'gap-y-6',
                        ])
                        ->schema([


                            Placeholder::make('status_display')
                                ->label('Document Status')
                                ->content(
                                    fn (?PurchaseOrder $record) =>
                                        $record?->status ?? PurchaseOrder::STATUS_DRAFT
                                )
                                ->columnSpan(3),

                            Placeholder::make('buyer_display')
                                ->label('Buyer')
                                ->content(
                                    fn () => auth()->user()?->name ?? '-'
                                )
                                ->columnSpan(3),

                            /*
                            |--------------------------------------------------------------------------
                            | Row 2
                            |--------------------------------------------------------------------------
                            */

                            Placeholder::make('supplier_display')
                                ->label('Supplier')
                                ->content(
                                    fn (?PurchaseOrder $record) =>
                                        $record?->supplier?->supplier_name ?? '-'
                                )
                                ->columnSpan(2),

                            Placeholder::make('currency_display')
                                ->label('Currency')
                                ->content(
                                    fn (?PurchaseOrder $record) =>
                                        $record?->currency?->currency_code ?? '-'
                                )
                                ->columnSpan(3),

                            Placeholder::make('exchange_rate_display')
                                ->label('Exchange Rate')
                                ->content(
                                    fn (?PurchaseOrder $record) =>
                                        number_format(
                                            (float) ($record?->exchange_rate ?? 1),
                                            4
                                        )
                                )
                                ->columnSpan(3),

                            /*
                            |--------------------------------------------------------------------------
                            | Row 3
                            |--------------------------------------------------------------------------
                            */

                            DatePicker::make('expected_delivery_date')
                                ->label('Expected Delivery Date')
                                ->native(false)
                                ->displayFormat('d M Y')
                                ->closeOnDateSelection()
                                ->columnSpan(2),

                            Textarea::make('remarks')
                                ->label('Remarks')
                                ->rows(2)
                                ->autosize()
                                ->placeholder('Additional notes for this Purchase Order...')
                                ->columnSpan(6),

                        ]),

                ]),

                /*
                |--------------------------------------------------------------------------
                | Reference Information
                |--------------------------------------------------------------------------
                */

                Section::make('Reference Information')
                    ->description('Reference information copied from Assignment Material Requisition.')
                    ->columnSpanFull()
                    ->columns(4)
                    ->schema([

                        Placeholder::make('assignment_no')
                            ->label('Assignment No')
                            ->content(fn (?PurchaseOrder $record) =>
                                $record?->assignmentMaterialRequisition?->document_no ?? '-'
                            ),

                        Placeholder::make('material_requisition_no')
                            ->label('Material Requisition')
                            ->content(fn (?PurchaseOrder $record) =>
                                $record?->purchaseRequisition?->pr_no ?? '-'
                            ),

                        Placeholder::make('company')
                            ->label('Company')
                            ->content(fn (?PurchaseOrder $record) =>
                                $record?->company?->company_name ?? '-'
                            ),

                        Placeholder::make('business_unit')
                            ->label('Business Unit')
                            ->content(fn (?PurchaseOrder $record) =>
                                $record?->businessUnit?->business_unit_name ?? '-'
                            ),

                        Placeholder::make('branch')
                            ->label('Branch')
                            ->content(fn (?PurchaseOrder $record) =>
                                $record?->branch?->branch_name ?? '-'
                            ),

                        Placeholder::make('department')
                            ->label('Department')
                            ->content(fn (?PurchaseOrder $record) =>
                                $record?->department?->department_name ?? '-'
                            ),

                        Placeholder::make('section')
                            ->label('Section')
                            ->content(fn (?PurchaseOrder $record) =>
                                $record?->section?->section_name ?? '-'
                            ),

                        Placeholder::make('warehouse')
                            ->label('Warehouse')
                            ->content(fn (?PurchaseOrder $record) =>
                                $record?->warehouse?->warehouse_name ?? '-'
                            ),

                        Placeholder::make('requester')
                            ->label('Requester')
                            ->content(fn (?PurchaseOrder $record) =>
                                $record?->requester?->name ?? '-'
                            ),

                        Placeholder::make('request_date')
                            ->label('Request Date')
                            ->content(fn (?PurchaseOrder $record) =>
                                $record?->request_date?->format('d M Y') ?? '-'
                            ),

                        Placeholder::make('required_date')
                            ->label('Required Date')
                            ->content(fn (?PurchaseOrder $record) =>
                                $record?->required_date?->format('d M Y') ?? '-'
                            ),


                        Placeholder::make('reference_no')
                            ->label('Reference Number')
                            ->content(fn (?PurchaseOrder $record) =>
                                $record?->reference_no ?? '-'
                            ),



                    ]),

                /*
                |--------------------------------------------------------------------------
                | Supplier Information
                |--------------------------------------------------------------------------
                */

                Section::make('Supplier Information')
                    ->description('Supplier information for this Purchase Order.')
                    ->columnSpanFull()
                    ->columns(4)
                    ->schema([

                        Placeholder::make('supplier_name')
                            ->label('Supplier')
                            ->content(fn (?PurchaseOrder $record) =>
                                $record?->supplier?->supplier_name ?? '-'
                            ),

                        Placeholder::make('email')
                            ->label('Email')
                            ->content(fn (?PurchaseOrder $record) =>
                                $record?->supplier?->email ?? '-'
                            ),

                        Placeholder::make('phone')
                            ->label('Phone')
                            ->content(fn (?PurchaseOrder $record) =>
                                $record?->supplier?->phone ?? '-'
                            ),

                        Placeholder::make('payment_terms')
                            ->label('Payment Terms')
                            ->content(
                                fn (?PurchaseOrder $record) =>
                                    $record?->supplier?->paymentTerm?->display_name ?? '-'
                            ),
                            
                        Placeholder::make('supplier_currency')
                            ->label('Supplier Currency')
                            ->content(fn (?PurchaseOrder $record) =>
                                $record?->currency?->currency_code ?? '-'
                            ),

                        Placeholder::make('supplier_status')
                            ->label('Supplier Status')
                            ->content(fn (?PurchaseOrder $record) =>
                                $record?->supplier?->status ?? '-'
                            ),

                        Placeholder::make('supplier_address')
                            ->label('Address')
                            ->content(fn (?PurchaseOrder $record) =>
                                $record?->supplier?->address ?? '-'
                            )
                            ->columnSpanFull(),

                    ]),



                /*
                |--------------------------------------------------------------------------
                | Shipping Information
                |--------------------------------------------------------------------------
                */

                Section::make('Shipping To')
                    ->description('Shipping destination information for this Purchase Order.')
                    ->columnSpanFull()
                    ->collapsible(false)
                    ->compact(false)
                    ->schema([

                        Grid::make(2)
                            ->schema([

                                /*
                                |--------------------------------------------------------------------------
                                | Left
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
                                            '<div style="line-height:1.9">

                                                <strong style="font-size:15px">'
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
                                | Right
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

                                            '<table style="width:100%; line-height:2">

                                                <tr>
                                                    <td width="170"><strong>Attention</strong></td>
                                                    <td>: ' . e($shipping->attention) . '</td>
                                                </tr>

                                                <tr>
                                                    <td><strong>Contact Person</strong></td>
                                                    <td>: ' . e($shipping->contact_person) . '</td>
                                                </tr>

                                                <tr>
                                                    <td><strong>Phone</strong></td>
                                                    <td>: ' . e($shipping->phone) . '</td>
                                                </tr>

                                                <tr>
                                                    <td><strong>Email</strong></td>
                                                    <td>: ' . e($shipping->email) . '</td>
                                                </tr>

                                            </table>'
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
                    ->description('Financial summary calculated automatically from Purchase Order Items.')
                    ->columnSpanFull()
                    ->columns(4)
                    ->schema([

                        Placeholder::make('subtotal_display')
                            ->label('Subtotal')
                            ->content(fn (?PurchaseOrder $record) =>
                                'Rp. ' . number_format((float) ($record?->subtotal ?? 0), 2)
                            ),

                        Placeholder::make('discount_display')
                            ->label('Discount')
                            ->content(fn (?PurchaseOrder $record) =>
                                'Rp. ' . number_format((float) ($record?->discount_amount ?? 0), 2)
                            ),

                        Placeholder::make('tax_display')
                            ->label('Tax')
                            ->content(fn (?PurchaseOrder $record) =>
                                'Rp. ' . number_format((float) ($record?->tax_amount ?? 0), 2)
                            ),

                        Placeholder::make('grand_total_display')
                            ->label('Grand Total')
                            ->content(fn (?PurchaseOrder $record) =>
                                'Rp. ' . number_format((float) ($record?->grand_total ?? 0), 2)
                            ),

                    ]),

            ]);

    }
}