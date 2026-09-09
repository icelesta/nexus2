<?php

declare(strict_types=1);

namespace App\Filament\Resources\AssignmentDirectMarketResource\Schemas;

use App\Models\AssignmentDirectMarket;
use App\Models\ShippingAddress;
use Filament\Forms\Components\Placeholder;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Schema;
use Illuminate\Support\HtmlString;

use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Hidden;
use Filament\Forms\Components\Repeater;

class AssignmentDirectMarketForm
{
    public static function configure(
        Schema $schema
    ): Schema {

        return $schema

            ->components([

                /*
                |--------------------------------------------------------------------------
                | ASSIGNMENT DIRECT MARKET
                |--------------------------------------------------------------------------
                */

                Section::make(
                    'Assignment Direct Market'
                )
                    ->description(
                        'Assignment information generated from Direct Market.'
                    )
                    ->schema([

                        Grid::make(3)
                            ->schema([

                                /*
                                |--------------------------------------------------------------------------
                                | DM NO.
                                |--------------------------------------------------------------------------
                                */

                                TextInput::make(
                                    'dm_no_display'
                                )
                                    ->label('DM No.')
                                    ->disabled()
                                    ->dehydrated(false)
                                    ->formatStateUsing(
                                        fn (
                                            ?AssignmentDirectMarket $record
                                        ): string =>
                                            $record
                                                ?->directMarket
                                                ?->dm_no
                                                ?? '-'
                                    ),

                                /*
                                |--------------------------------------------------------------------------
                                | ASSIGNMENT DATE
                                |--------------------------------------------------------------------------
                                */

                                TextInput::make(
                                    'assignment_date_display'
                                )
                                    ->label('Assignment Date')
                                    ->disabled()
                                    ->dehydrated(false)
                                    ->formatStateUsing(
                                        fn (
                                            ?AssignmentDirectMarket $record
                                        ): string =>
                                            $record
                                                ?->assigned_at
                                                ?->format('d M Y')
                                                ?? '-'
                                    ),

                                /*
                                |--------------------------------------------------------------------------
                                | CURRENCY
                                |--------------------------------------------------------------------------
                                */

                                TextInput::make(
                                    'currency_display'
                                )
                                    ->label('Currency')
                                    ->disabled()
                                    ->dehydrated(false)
                                    ->formatStateUsing(
                                        fn (
                                            ?AssignmentDirectMarket $record
                                        ): string =>
                                            $record
                                                ?->directMarket
                                                ?->currency
                                                ?->display_name
                                                ?? $record
                                                    ?->directMarket
                                                    ?->currency
                                                    ?->currency_code
                                                ?? '-'
                                    ),

                                /*
                                |--------------------------------------------------------------------------
                                | REQUEST DATE
                                |--------------------------------------------------------------------------
                                */

                                TextInput::make(
                                    'request_date_display'
                                )
                                    ->label('Request Date')
                                    ->disabled()
                                    ->dehydrated(false)
                                    ->formatStateUsing(
                                        fn (
                                            ?AssignmentDirectMarket $record
                                        ): string =>
                                            $record
                                                ?->request_date
                                                ?->format('d M Y')
                                                ?? '-'
                                    ),

                                /*
                                |--------------------------------------------------------------------------
                                | REQUIRED DATE
                                |--------------------------------------------------------------------------
                                */

                                TextInput::make(
                                    'required_date_display'
                                )
                                    ->label('Required Date')
                                    ->disabled()
                                    ->dehydrated(false)
                                    ->formatStateUsing(
                                        fn (
                                            ?AssignmentDirectMarket $record
                                        ): string =>
                                            $record
                                                ?->required_date
                                                ?->format('d M Y')
                                                ?? '-'
                                    ),

                                /*
                                |--------------------------------------------------------------------------
                                | ASSIGNED BY
                                |--------------------------------------------------------------------------
                                */

                                TextInput::make(
                                    'assigned_by_display'
                                )
                                    ->label('Assigned By')
                                    ->disabled()
                                    ->dehydrated(false)
                                    ->formatStateUsing(
                                        fn (
                                            ?AssignmentDirectMarket $record
                                        ): string =>
                                            $record
                                                ?->assignedBy
                                                ?->name
                                                ?? '-'
                                    ),

                            ]),

                    ])
                    ->columnSpanFull()
                    ->collapsible(false),


                /*
                |--------------------------------------------------------------------------
                | ORGANIZATION
                |--------------------------------------------------------------------------
                */

                Section::make(
                    'Organization'
                )
                    ->schema([

                        Grid::make(3)
                            ->schema([

                                /*
                                |--------------------------------------------------------------------------
                                | BRANCH
                                |--------------------------------------------------------------------------
                                */

                                TextInput::make(
                                    'branch_display'
                                )
                                    ->label('Branch')
                                    ->disabled()
                                    ->dehydrated(false)
                                    ->formatStateUsing(
                                        fn (
                                            ?AssignmentDirectMarket $record
                                        ): string =>
                                            $record?->branch
                                                ? (
                                                    $record->branch->branch_code
                                                    . ' - '
                                                    . $record->branch->branch_name
                                                )
                                                : '-'
                                    ),

                                /*
                                |--------------------------------------------------------------------------
                                | DEPARTMENT
                                |--------------------------------------------------------------------------
                                */

                                TextInput::make(
                                    'department_display'
                                )
                                    ->label('Department')
                                    ->disabled()
                                    ->dehydrated(false)
                                    ->formatStateUsing(
                                        fn (
                                            ?AssignmentDirectMarket $record
                                        ): string =>
                                            $record?->department
                                                ? (
                                                    $record->department->department_code
                                                    . ' - '
                                                    . $record->department->department_name
                                                )
                                                : '-'
                                    ),

                                /*
                                |--------------------------------------------------------------------------
                                | COST CENTER
                                |--------------------------------------------------------------------------
                                */

                                TextInput::make(
                                    'cost_center_display'
                                )
                                    ->label('Cost Center')
                                    ->disabled()
                                    ->dehydrated(false)
                                    ->formatStateUsing(
                                        fn (
                                            ?AssignmentDirectMarket $record
                                        ): string =>
                                            $record?->costCenter
                                                ? (
                                                    $record->costCenter->cost_center_code
                                                    . ' - '
                                                    . $record->costCenter->cost_center_name
                                                )
                                                : '-'
                                    ),

                                /*
                                |--------------------------------------------------------------------------
                                | REQUESTER
                                |--------------------------------------------------------------------------
                                */

                                TextInput::make(
                                    'requester_display'
                                )
                                    ->label('Requester')
                                    ->disabled()
                                    ->dehydrated(false)
                                    ->formatStateUsing(
                                        fn (
                                            ?AssignmentDirectMarket $record
                                        ): string =>
                                            $record?->requester?->name
                                            ?? '-'
                                    ),

                                /*
                                |--------------------------------------------------------------------------
                                | ASSIGNED BY
                                |--------------------------------------------------------------------------
                                */

                                TextInput::make(
                                    'assigned_by_display'
                                )
                                    ->label('Assigned By')
                                    ->disabled()
                                    ->dehydrated(false)
                                    ->formatStateUsing(
                                        fn (
                                            ?AssignmentDirectMarket $record
                                        ): string =>
                                            $record?->assignedBy?->name
                                            ?? '-'
                                    ),

                                /*
                                |--------------------------------------------------------------------------
                                | REMARKS
                                |--------------------------------------------------------------------------
                                */

                                TextInput::make(
                                    'remarks_header_display'
                                )
                                    ->label('Remarks')
                                    ->disabled()
                                    ->dehydrated(false)
                                    ->formatStateUsing(
                                        fn (
                                            ?AssignmentDirectMarket $record
                                        ): string =>
                                            $record?->directMarket?->remarks
                                            ?? '-'
                                    ),

                                /*
                                |--------------------------------------------------------------------------
                                | SUPPORTING DOCUMENTS
                                |--------------------------------------------------------------------------
                                |
                                | Supporting documents for this Assignment Direct Market.
                                |
                                | Documents belong exclusively to ADM.
                                | They are not propagated to RR or other transactions.
                                |
                                */

                                Section::make(
                                    'Supporting Documents'
                                )
                                    ->description(
                                        'Upload supplier quotation or other supporting documents for this Assignment Direct Market.'
                                    )
                                    ->columnSpanFull()
                                    ->schema([

                                        Repeater::make(
                                            'documents'
                                        )
                                            ->relationship(
                                                'documents'
                                            )
                                            ->label(
                                                'Supplier Quotation / Supporting Document'
                                            )
                                            ->schema([

                                                FileUpload::make(
                                                    'file_path'
                                                )
                                                    ->label(
                                                        'Document'
                                                    )
                                                    ->disk(
                                                        'public'
                                                    )
                                                    ->directory(
                                                        'purchasing/adm/supporting-documents'
                                                    )
                                                    ->preserveFilenames()
                                                    ->downloadable()
                                                    ->openable()
                                                    ->maxSize(
                                                        10240
                                                    )
                                                    ->acceptedFileTypes([

                                                        'application/pdf',

                                                        'image/jpeg',

                                                        'image/png',

                                                        'application/msword',

                                                        'application/vnd.openxmlformats-officedocument.wordprocessingml.document',

                                                        'application/vnd.ms-excel',

                                                        'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',

                                                    ])
                                                    ->storeFileNamesIn(
                                                        'file_name'
                                                    )
                                                    ->required()
                                                    ->columnSpanFull(),

                                                Hidden::make(
                                                    'document_type'
                                                )
                                                    ->default(
                                                        'SUPPLIER_QUOTATION'
                                                    ),

                                                Hidden::make(
                                                    'uploaded_by'
                                                )
                                                    ->default(
                                                        fn (): ?int =>
                                                            auth()->id()
                                                    ),

                                            ])
                                            ->columns(1)
                                            ->addActionLabel(
                                                'Upload Supporting Document'
                                            )
                                            ->defaultItems(0)
                                            ->reorderable(false)
                                            ->collapsible()
                                            ->itemLabel(
                                                fn (
                                                    array $state
                                                ): ?string =>
                                                    $state['file_name']
                                                        ?? 'Supporting Document'
                                            ),

                                    ]),

                                    

                            ]),

                    ])
                    ->columnSpanFull()
                    ->collapsible(false),



                /*
                |--------------------------------------------------------------------------
                | PAYMENT INSTRUCTION
                |--------------------------------------------------------------------------
                |
                | Free-text instruction.
                | Editable by the user.
                |
                */

                Section::make(
                    'Payment & Shipping'
                )
                    ->schema([

                        Grid::make(2)
                            ->schema([

                                /*
                                |--------------------------------------------------------------------------
                                | PAYMENT INSTRUCTION
                                |--------------------------------------------------------------------------
                                */

                                Textarea::make(
                                    'payment_instruction'
                                )
                                    ->label('Payment Instruction')
                                    ->required()
                                    ->rows(2)
                                    ->autosize()
                                    ->maxLength(5000)
                                    ->placeholder(
                                        'Enter payment instruction...'
                                    ),

                                /*
                                |--------------------------------------------------------------------------
                                | SHIPPING ADDRESS
                                |--------------------------------------------------------------------------
                                */

                                Select::make(
                                    'shipping_address_id'
                                )
                                    ->label('Shipping Address')
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
                                    ->helperText(
                                        'Select the shipping destination from the Shipping Master.'
                                    ),

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

                                /*
                                |--------------------------------------------------------------------------
                                | SHIPPING LEFT
                                |--------------------------------------------------------------------------
                                */

                                Placeholder::make(
                                    'shipping_left'
                                )
                                    ->hiddenLabel()
                                    ->content(
                                        function (
                                            Get $get
                                        ): HtmlString|string {

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

                                /*
                                |--------------------------------------------------------------------------
                                | SHIPPING RIGHT
                                |--------------------------------------------------------------------------
                                */

                                Placeholder::make(
                                    'shipping_right'
                                )
                                    ->hiddenLabel()
                                    ->content(
                                        function (
                                            Get $get
                                        ): HtmlString|string {

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

                    ])
                    ->columnSpanFull()
                    ->collapsible(false),

            ]);
    }
}