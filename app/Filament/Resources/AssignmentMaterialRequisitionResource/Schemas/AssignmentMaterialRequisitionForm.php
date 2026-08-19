<?php

declare(strict_types=1);

namespace App\Filament\Resources\AssignmentMaterialRequisitionResource\Schemas;

use App\Models\AssignmentMaterialRequisition;
use App\Models\ShippingAddress;

use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Placeholder;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Illuminate\Support\HtmlString;

class AssignmentMaterialRequisitionForm
{
    public static function configure(
        Schema $schema,
    ): Schema {

        return $schema

            ->columns(1)

            ->components([

                /*
                |--------------------------------------------------------------------------
                | Assignment Information
                |--------------------------------------------------------------------------
                */

                Section::make('Assignment Information')
                    ->description(
                        'Reference information from the approved Material Requisition.'
                    )
                    ->columnSpanFull()
                    ->schema([

                        Grid::make(12)
                            ->schema([

                                /*
                                |--------------------------------------------------------------------------
                                | PURCHASE REQUISITION
                                |--------------------------------------------------------------------------
                                |
                                | Snapshot from Material Requisition.
                                | Cannot be changed from AMR.
                                |
                                */

                                Placeholder::make(
                                    'purchase_requisition_display'
                                )
                                    ->label(
                                        'Purchase Requisition'
                                    )
                                    ->content(
                                        fn (
                                            ?AssignmentMaterialRequisition $record
                                        ): string =>
                                            $record
                                                ?->purchaseRequisition
                                                ?->pr_no
                                                ?? '-'
                                    )
                                    ->columnSpan(4),

                                /*
                                |--------------------------------------------------------------------------
                                | ASSIGNMENT DATE
                                |--------------------------------------------------------------------------
                                |
                                | AMR document date is already generated.
                                | It is not a business input after AMR creation.
                                |
                                */

                                Placeholder::make(
                                    'assigned_at_display'
                                )
                                    ->label(
                                        'Assignment Date'
                                    )
                                    ->content(
                                        fn (
                                            ?AssignmentMaterialRequisition $record
                                        ): string =>
                                            $record?->assigned_at
                                                ?->format('d M Y')
                                                ?? '-'
                                    )
                                    ->columnSpan(2),

                                /*
                                |--------------------------------------------------------------------------
                                | CURRENCY
                                |--------------------------------------------------------------------------
                                |
                                | Currency is inherited from Material Requisition.
                                | Buyer must not change it in AMR.
                                |
                                */

                                Placeholder::make(
                                    'currency_display'
                                )
                                    ->label(
                                        'Currency'
                                    )
                                    ->content(
                                        fn (
                                            ?AssignmentMaterialRequisition $record
                                        ): string =>
                                            $record
                                                ?->currency
                                                ?->currency_code
                                                ?? '-'
                                    )
                                    ->columnSpan(2),

                                /*
                                |--------------------------------------------------------------------------
                                | EXCHANGE RATE
                                |--------------------------------------------------------------------------
                                |
                                | Exchange rate is inherited from the
                                | Material Requisition / AMR snapshot.
                                |
                                */

                                Placeholder::make(
                                    'exchange_rate_display'
                                )
                                    ->label(
                                        'Exchange Rate'
                                    )
                                    ->content(
                                        fn (
                                            ?AssignmentMaterialRequisition $record
                                        ): string =>
                                            $record?->exchange_rate !== null
                                                ? (string) $record->exchange_rate
                                                : '-'
                                    )
                                    ->columnSpan(2),

                                /*
                                |--------------------------------------------------------------------------
                                | SHIPPING ADDRESS
                                |--------------------------------------------------------------------------
                                |
                                | THIS IS THE ONLY EDITABLE HEADER FIELD.
                                |
                                | Source:
                                | Inventory / Shipping Address Master
                                |
                                */

                                Select::make(
                                    'shipping_address_id'
                                )
                                    ->label(
                                        'Shipping Address'
                                    )
                                    ->relationship(
                                        'shippingAddress',
                                        'shipping_name',
                                        fn ($query) => $query
                                            ->where(
                                                'is_active',
                                                true
                                            )
                                            ->orderByDesc(
                                                'is_default'
                                            )
                                            ->orderBy(
                                                'shipping_name'
                                            )
                                    )
                                    ->searchable()
                                    ->preload()
                                    ->live()
                                    ->required()
                                    ->columnSpan(4)
                                    ->helperText(
                                        'Select the shipping destination from the Inventory Setup Master.'
                                    ),

                                /*
                                |--------------------------------------------------------------------------
                                | ASSIGNED BY
                                |--------------------------------------------------------------------------
                                */

                                Placeholder::make(
                                    'assigned_by_display'
                                )
                                    ->label(
                                        'Assigned By'
                                    )
                                    ->content(
                                        fn (
                                            ?AssignmentMaterialRequisition $record
                                        ): string =>
                                            $record
                                                ?->assignedBy
                                                ?->name
                                                ?? auth()->user()?->name
                                                ?? '-'
                                    )
                                    ->columnSpan(2),

                                /*
                                |--------------------------------------------------------------------------
                                | REMARKS
                                |--------------------------------------------------------------------------
                                |
                                | Snapshot from Material Requisition.
                                | Buyer cannot change the source remark.
                                |
                                */

                                Placeholder::make(
                                    'remarks_display'
                                )
                                    ->label(
                                        'Remarks'
                                    )
                                    ->content(
                                        fn (
                                            ?AssignmentMaterialRequisition $record
                                        ): string =>
                                            $record
                                                ?->remarks
                                                ?? $record
                                                    ?->purchaseRequisition
                                                    ?->remarks
                                                ?? '-'
                                    )
                                    ->columnSpan(6),

                            ]),

                        /*
                        |--------------------------------------------------------------------------
                        | SHIPPING ADDRESS INFORMATION
                        |--------------------------------------------------------------------------
                        */

                        Section::make(
                            '🚚 Shipping Address Information'
                        )
                            ->columns(2)
                            ->compact()
                            ->schema([

                                Placeholder::make(
                                    'shipping_left'
                                )
                                    ->hiddenLabel()
                                    ->content(
                                        function (
                                            Get $get
                                        ) {

                                            $shipping =
                                                ShippingAddress::find(
                                                    $get(
                                                        'shipping_address_id'
                                                    )
                                                );

                                            if (! $shipping) {
                                                return '-';
                                            }

                                            return new HtmlString(
                                                '<strong>'
                                                . e(
                                                    $shipping->shipping_name
                                                )
                                                . '</strong><br><br>'
                                                . e(
                                                    $shipping->address
                                                )
                                                . '<br>'
                                                . e(
                                                    $shipping->city
                                                )
                                                . ', '
                                                . e(
                                                    $shipping->province
                                                )
                                                . ' '
                                                . e(
                                                    $shipping->postal_code
                                                )
                                                . '<br>'
                                                . e(
                                                    $shipping->country
                                                )
                                            );
                                        }
                                    ),

                                Placeholder::make(
                                    'shipping_right'
                                )
                                    ->hiddenLabel()
                                    ->content(
                                        function (
                                            Get $get
                                        ) {

                                            $shipping =
                                                ShippingAddress::find(
                                                    $get(
                                                        'shipping_address_id'
                                                    )
                                                );

                                            if (! $shipping) {
                                                return '-';
                                            }

                                            return new HtmlString(
                                                '<table style="width:100%">
                                                    <tr>
                                                        <td width="160">
                                                            <strong>Attention</strong>
                                                        </td>
                                                        <td>: '
                                                        . e(
                                                            $shipping->attention
                                                        )
                                                        . '</td>
                                                    </tr>

                                                    <tr>
                                                        <td>
                                                            <strong>Contact Person</strong>
                                                        </td>
                                                        <td>: '
                                                        . e(
                                                            $shipping->contact_person
                                                        )
                                                        . '</td>
                                                    </tr>

                                                    <tr>
                                                        <td>
                                                            <strong>Phone</strong>
                                                        </td>
                                                        <td>: '
                                                        . e(
                                                            $shipping->phone
                                                        )
                                                        . '</td>
                                                    </tr>

                                                    <tr>
                                                        <td>
                                                            <strong>Email</strong>
                                                        </td>
                                                        <td>: '
                                                        . e(
                                                            $shipping->email
                                                        )
                                                        . '</td>
                                                    </tr>
                                                </table>'
                                            );
                                        }
                                    ),

                            ])
                            ->collapsible(false),

                    ]),

                /*
                |--------------------------------------------------------------------------
                | MATERIAL REQUISITION INFORMATION
                |--------------------------------------------------------------------------
                |
                | Completely read-only reference section.
                |
                */

                Section::make(
                    'Material Requisition Information'
                )
                    ->description(
                        'Reference information from the selected Material Requisition.'
                    )
                    ->columnSpanFull()
                    ->columns(4)
                    ->schema([

                        Placeholder::make(
                            'pr_number'
                        )
                            ->label(
                                'PR Number'
                            )
                            ->content(
                                fn (
                                    ?AssignmentMaterialRequisition $record
                                ): string =>
                                    $record
                                        ?->purchaseRequisition
                                        ?->pr_no
                                        ?? '-'
                            ),

                        Placeholder::make(
                            'request_date'
                        )
                            ->label(
                                'Request Date'
                            )
                            ->content(
                                fn (
                                    ?AssignmentMaterialRequisition $record
                                ): string =>
                                    $record
                                        ?->purchaseRequisition
                                        ?->request_date
                                        ?->format('d M Y')
                                        ?? '-'
                            ),

                        Placeholder::make(
                            'company'
                        )
                            ->label(
                                'Company'
                            )
                            ->content(
                                fn (
                                    ?AssignmentMaterialRequisition $record
                                ): string =>
                                    $record
                                        ?->purchaseRequisition
                                        ?->company
                                        ?->company_name
                                        ?? '-'
                            ),

                        Placeholder::make(
                            'business_unit'
                        )
                            ->label(
                                'Business Unit'
                            )
                            ->content(
                                fn (
                                    ?AssignmentMaterialRequisition $record
                                ): string =>
                                    $record
                                        ?->purchaseRequisition
                                        ?->businessUnit
                                        ?->business_unit_name
                                        ?? '-'
                            ),

                        Placeholder::make(
                            'branch'
                        )
                            ->label(
                                'Branch'
                            )
                            ->content(
                                fn (
                                    ?AssignmentMaterialRequisition $record
                                ): string =>
                                    $record
                                        ?->purchaseRequisition
                                        ?->branch
                                        ?->branch_name
                                        ?? '-'
                            ),

                        Placeholder::make(
                            'department'
                        )
                            ->label(
                                'Department'
                            )
                            ->content(
                                fn (
                                    ?AssignmentMaterialRequisition $record
                                ): string =>
                                    $record
                                        ?->purchaseRequisition
                                        ?->department
                                        ?->department_name
                                        ?? '-'
                            ),

                        Placeholder::make(
                            'section'
                        )
                            ->label(
                                'Section'
                            )
                            ->content(
                                fn (
                                    ?AssignmentMaterialRequisition $record
                                ): string =>
                                    $record
                                        ?->purchaseRequisition
                                        ?->section
                                        ?->section_name
                                        ?? '-'
                            ),

                        Placeholder::make(
                            'warehouse'
                        )
                            ->label(
                                'Warehouse'
                            )
                            ->content(
                                fn (
                                    ?AssignmentMaterialRequisition $record
                                ): string =>
                                    $record
                                        ?->purchaseRequisition
                                        ?->warehouse
                                        ?->warehouse_name
                                        ?? '-'
                            ),

                        Placeholder::make(
                            'requester'
                        )
                            ->label(
                                'Requester'
                            )
                            ->content(
                                fn (
                                    ?AssignmentMaterialRequisition $record
                                ): string =>
                                    $record
                                        ?->purchaseRequisition
                                        ?->requester
                                        ?->name
                                        ?? '-'
                            ),

                        Placeholder::make(
                            'required_date'
                        )
                            ->label(
                                'Required Date'
                            )
                            ->content(
                                fn (
                                    ?AssignmentMaterialRequisition $record
                                ): string =>
                                    $record
                                        ?->purchaseRequisition
                                        ?->required_date
                                        ?->format('d M Y')
                                        ?? '-'
                            ),

                        Placeholder::make(
                            'reference_no'
                        )
                            ->label(
                                'Reference Number'
                            )
                            ->content(
                                fn (
                                    ?AssignmentMaterialRequisition $record
                                ): string =>
                                    $record
                                        ?->purchaseRequisition
                                        ?->reference_no
                                        ?? '-'
                            ),

                    ]),

            ]);
    }
}