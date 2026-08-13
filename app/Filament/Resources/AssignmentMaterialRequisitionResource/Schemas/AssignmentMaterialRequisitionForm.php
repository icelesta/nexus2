<?php

declare(strict_types=1);

namespace App\Filament\Resources\AssignmentMaterialRequisitionResource\Schemas;

use App\Models\AssignmentMaterialRequisition;
use App\Models\Currency;
use App\Models\ShippingAddress;

use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Placeholder;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Utilities\Get;

use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;

class AssignmentMaterialRequisitionForm
{
public static function configure(
    \Filament\Schemas\Schema $schema,
): \Filament\Schemas\Schema {

    return $schema

        ->columns(1)

        ->components([

            /*
            |--------------------------------------------------------------------------
            | Assignment Information
            |--------------------------------------------------------------------------
            */

            Section::make('Assignment Information')
                ->description('General information for this assignment document.')
                ->columnSpanFull()
                ->schema([

                    Grid::make(12)
                        ->schema([

                            Select::make('purchase_requisition_id')
                                ->label('Purchase Requisition')
                                ->relationship('purchaseRequisition', 'pr_no')
                                ->searchable()
                                ->preload()
                                ->required()
                                ->columnSpan(4),

                            DatePicker::make('assigned_at')
                                ->label('Assignment Date')
                                ->native(false)
                                ->default(now())
                                ->required()
                                ->columnSpan(2),

                            Select::make('assigned_to')
                                ->label('Assigned To')
                                ->relationship('assignedTo', 'name')
                                ->searchable()
                                ->preload()
                                ->required()
                                ->columnSpan(2),

                            Select::make('currency_id')
                                ->label('Currency')
                                ->relationship('currency', 'currency_code')
                                ->searchable()
                                ->preload()
                                ->required()
                                ->live()
                                ->afterStateUpdated(function ($set) {
                                    $set('exchange_rate', 1.000000);
                                })
                                ->columnSpan(2), 
                                
                            TextInput::make('exchange_rate')
                                ->label('Exchange Rate')
                                ->numeric()
                                ->default(1.000000)
                                ->required()
                                ->columnSpan(2),     

                            Select::make('shipping_address_id')
                                ->label('Shipping Address')
                                ->relationship(
                                    'shippingAddress',
                                    'shipping_name',
                                    fn ($query) => $query
                                        ->where('is_active', true)
                                        ->orderByDesc('is_default')
                                        ->orderBy('shipping_name')
                                )
                                ->searchable()
                                ->preload()
                                ->live()
                                ->required()
                                ->columnSpan(4)
                                ->helperText('Select the shipping destination for this Purchase Order.'),


                            Placeholder::make('assigned_by_display')
                                ->label('Assigned By')
                                ->content(
                                    fn () => auth()->user()?->name ?? '-'
                                )
                                ->columnSpan(2),

                            Textarea::make('remarks')
                                ->label('Remarks')
                                ->rows(3)
                                ->autosize()
                                ->columnSpan(6),

                        ]),

                        Section::make('🚚 Shipping Address Information')
                            ->columns(2)
                            ->compact()
                            ->schema([

                                Placeholder::make('shipping_left')
                                    ->hiddenLabel()
                                    ->content(function (Get $get) {

                                        $shipping = ShippingAddress::find(
                                            $get('shipping_address_id')
                                        );

                                        if (! $shipping) {
                                            return '-';
                                        }

                                        return new \Illuminate\Support\HtmlString(
                                            '<strong>'.$shipping->shipping_name.'</strong><br><br>'
                                            .$shipping->address.'<br>'
                                            .$shipping->city.', '.$shipping->province.' '.$shipping->postal_code.'<br>'
                                            .$shipping->country
                                        );
                                    }),

                                Placeholder::make('shipping_right')
                                    ->hiddenLabel()
                                    ->content(function (Get $get) {

                                        $shipping = ShippingAddress::find(
                                            $get('shipping_address_id')
                                        );

                                        if (! $shipping) {
                                            return '-';
                                        }

                                        return new \Illuminate\Support\HtmlString(
                                            '<table style="width:100%">
                                                <tr><td width="160"><strong>Attention</strong></td><td>: '.$shipping->attention.'</td></tr>
                                                <tr><td><strong>Contact Person</strong></td><td>: '.$shipping->contact_person.'</td></tr>
                                                <tr><td><strong>Phone</strong></td><td>: '.$shipping->phone.'</td></tr>
                                                <tr><td><strong>Email</strong></td><td>: '.$shipping->email.'</td></tr>
                                            </table>'
                                        );
                                    }),

                            ])
                            ->collapsible(false),


                ]),

            /*
            |--------------------------------------------------------------------------
            | Material Requisition Information
            |--------------------------------------------------------------------------
            */

            Section::make('Material Requisition Information')
                ->description('Reference information from the selected Material Requisition.')
                ->columnSpanFull()
                ->columns(4)
                ->schema([

                    Placeholder::make('pr_number')
                        ->label('PR Number')
                        ->content(fn (?AssignmentMaterialRequisition $record) =>
                            $record?->purchaseRequisition?->pr_no ?? '-'
                        ),

                    Placeholder::make('request_date')
                        ->label('Request Date')
                        ->content(fn (?AssignmentMaterialRequisition $record) =>
                            $record?->purchaseRequisition?->request_date?->format('d M Y') ?? '-'
                        ),

                    Placeholder::make('company')
                        ->label('Company')
                        ->content(fn (?AssignmentMaterialRequisition $record) =>
                            $record?->purchaseRequisition?->company?->company_name ?? '-'
                        ),

                    Placeholder::make('business_unit')
                        ->label('Business Unit')
                        ->content(fn (?AssignmentMaterialRequisition $record) =>
                            $record?->purchaseRequisition?->businessUnit?->business_unit_name ?? '-'
                        ),

                    Placeholder::make('branch')
                        ->label('Branch')
                        ->content(fn (?AssignmentMaterialRequisition $record) =>
                            $record?->purchaseRequisition?->branch?->branch_name ?? '-'
                        ),

                    Placeholder::make('department')
                        ->label('Department')
                        ->content(fn (?AssignmentMaterialRequisition $record) =>
                            $record?->purchaseRequisition?->department?->department_name ?? '-'
                        ),

                    Placeholder::make('section')
                        ->label('Section')
                        ->content(fn (?AssignmentMaterialRequisition $record) =>
                            $record?->purchaseRequisition?->section?->section_name ?? '-'
                        ),

                    Placeholder::make('warehouse')
                        ->label('Warehouse')
                        ->content(fn (?AssignmentMaterialRequisition $record) =>
                            $record?->purchaseRequisition?->warehouse?->warehouse_name ?? '-'
                        ),

                    Placeholder::make('requester')
                        ->label('Requester')
                        ->content(fn (?AssignmentMaterialRequisition $record) =>
                            $record?->purchaseRequisition?->requester?->name ?? '-'
                        ),

                    Placeholder::make('required_date')
                        ->label('Required Date')
                        ->content(fn (?AssignmentMaterialRequisition $record) =>
                            $record?->purchaseRequisition?->required_date?->format('d M Y') ?? '-'
                        ),

                    Placeholder::make('reference_no')
                        ->label('Reference Number')
                        ->content(fn (?AssignmentMaterialRequisition $record) =>
                            $record?->purchaseRequisition?->reference_no ?? '-'
                        ),

                ]),

        ]);
    }
}