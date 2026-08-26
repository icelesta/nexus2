<?php

namespace App\Filament\Resources\AssignmentMaterialRequisitionResource\Schemas;

use Filament\Infolists\Components\RepeatableEntry;
use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Illuminate\Support\Facades\Storage;
use App\Support\Timezone\UserTimezone;

class AssignmentMaterialRequisitionInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([

                /*
                |--------------------------------------------------------------------------
                | HEADER INFORMATION
                |--------------------------------------------------------------------------
                |
                | Assignment Information + Material Requisition Information
                | are intentionally displayed side-by-side.
                |
                */

                Grid::make(2)
                    ->schema([

                        /*
                        |--------------------------------------------------------------------------
                        | ASSIGNMENT INFORMATION
                        |--------------------------------------------------------------------------
                        */

                        Section::make('Assignment Information')
                            ->schema([

                                Grid::make(2)
                                    ->schema([

                                        /*
                                        |--------------------------------------------------------------------------
                                        | ROW 1
                                        |--------------------------------------------------------------------------
                                        */

                                        TextEntry::make('document_no')
                                            ->label('Assignment Number'),

                                        TextEntry::make('document_date')
                                            ->label('Assignment Date')
                                            ->date(),

                                        /*
                                        |--------------------------------------------------------------------------
                                        | ROW 2
                                        |--------------------------------------------------------------------------
                                        */

                                        TextEntry::make('assignedTo.name')
                                            ->label('Assigned To')
                                            ->placeholder('-'),

                                        TextEntry::make('assignedBy.name')
                                            ->label('Assigned By')
                                            ->placeholder('-'),

                                        /*
                                        |--------------------------------------------------------------------------
                                        | ROW 3
                                        |--------------------------------------------------------------------------
                                        */

                                        TextEntry::make('status')
                                            ->label('Status')
                                            ->badge(),

                                        TextEntry::make('payment_instruction')
                                            ->label('Payment Instruction')
                                            ->placeholder('-'),

                                        /*
                                        |--------------------------------------------------------------------------
                                        | ROW 4
                                        |--------------------------------------------------------------------------
                                        */

                                        TextEntry::make('remarks')
                                            ->label('Remarks')
                                            ->placeholder('-')
                                            ->columnSpanFull(),

                                    ]),

                            ]),

                        /*
                        |--------------------------------------------------------------------------
                        | MATERIAL REQUISITION INFORMATION
                        |--------------------------------------------------------------------------
                        */

                        Section::make(
                            'Material Requisition Information'
                        )
                            ->schema([

                                Grid::make(2)
                                    ->schema([

                                        TextEntry::make('pr_number')
                                            ->label('MR Number'),

                                        TextEntry::make('request_date')
                                            ->label('MR Date')
                                            ->date(),

                                        TextEntry::make('requester.name')
                                            ->label('Requester'),

                                        TextEntry::make(
                                            'department.department_name'
                                        )
                                            ->label('Department'),

                                        TextEntry::make(
                                            'warehouse.warehouse_name'
                                        )
                                            ->label('Warehouse'),

                                        TextEntry::make('required_date')
                                            ->label('Required Date')
                                            ->date(),

                                        TextEntry::make('priority')
                                            ->label('Priority'),

                                        TextEntry::make(
                                            'purchaseRequisition.status'
                                        )
                                            ->label('MR Status')
                                            ->badge(),

                                    ]),

                            ]),

                    ])
                    ->columnSpanFull(),

                /*
                |--------------------------------------------------------------------------
                | SUPPORTING DOCUMENTS
                |--------------------------------------------------------------------------
                |
                | Read-only supporting documents for AMR.
                |
                | Documents remain attached to AMR only.
                | They are NOT propagated to PO or other transactions.
                |
                | Users can:
                | - View / Open
                | - Download
                |
                | Users cannot:
                | - Edit
                | - Delete
                | - Upload
                |
                */

                Section::make('Supporting Documents')
                    ->description(
                        'Supplier quotations and other supporting documents for this Assignment Material Requisition.'
                    )
                    ->schema([

                        RepeatableEntry::make('documents')
                            ->label('')
                            ->schema([

                                TextEntry::make('file_name')
                                    ->label('Document')
                                    ->weight('bold')
                                    ->columnSpan(2),

                                TextEntry::make('document_type')
                                    ->label('Type')
                                    ->badge(),

                                TextEntry::make('uploader.name')
                                    ->label('Uploaded By')
                                    ->placeholder('-'),

                                TextEntry::make('created_at')
                                    ->label('Uploaded At')
                                    ->formatStateUsing(
                                        fn ($state) => $state
                                            ? UserTimezone::format(
                                                $state,
                                                'd M Y H:i:s'
                                            )
                                            : '-'
                                    ),

                                /*
                                |--------------------------------------------------------------------------
                                | VIEW DOCUMENT
                                |--------------------------------------------------------------------------
                                */

                                TextEntry::make('view_document')
                                    ->label('View')
                                    ->state('Open Document')
                                    ->icon('heroicon-o-eye')
                                    ->color('primary')
                                    ->url(
                                        fn (
                                            $record
                                        ): ?string =>
                                            filled(
                                                $record?->file_path
                                            )
                                                ? Storage::disk('public')
                                                    ->url(
                                                        $record->file_path
                                                    )
                                                : null
                                    )
                                    ->openUrlInNewTab(),

                                /*
                                |--------------------------------------------------------------------------
                                | DOWNLOAD DOCUMENT
                                |--------------------------------------------------------------------------
                                */

                                TextEntry::make('download_document')
                                    ->label('Download')
                                    ->state('Download')
                                    ->icon('heroicon-o-arrow-down-tray')
                                    ->color('primary')
                                    ->url(
                                        fn (
                                            $record
                                        ): ?string =>
                                            filled(
                                                $record?->file_path
                                            )
                                                ? route(
                                                    'purchasing.amr.documents.download',
                                                    [
                                                        'document' =>
                                                            $record->id,
                                                    ]
                                                )
                                                : null
                                    )
                                    ->openUrlInNewTab(),

                            ])
                            ->columns(6)
                            ->contained(true),

                    ])
                    ->columnSpanFull(),

                /*
                |--------------------------------------------------------------------------
                | AUDIT + SHIPPING ADDRESS
                |--------------------------------------------------------------------------
                |
                | These two sections are also displayed side-by-side.
                |
                */

                Grid::make(2)
                    ->schema([

                        /*
                        |--------------------------------------------------------------------------
                        | AUDIT INFORMATION
                        |--------------------------------------------------------------------------
                        */

                        Section::make('Audit Information')
                            ->schema([

                                Grid::make(2)
                                    ->schema([

                                        TextEntry::make('createdBy.name')
                                            ->label('Created By'),

                                        TextEntry::make('created_at')
                                            ->label('Created At')
                                            ->formatStateUsing(
                                                fn ($state) => $state
                                                    ? UserTimezone::format(
                                                        $state,
                                                        'd M Y H:i:s'
                                                    )
                                                    : '-'
                                            ),

                                        TextEntry::make('updatedBy.name')
                                            ->label('Updated By'),

                                        TextEntry::make('updated_at')
                                            ->label('Updated At')
                                            ->formatStateUsing(
                                                fn ($state) => $state
                                                    ? UserTimezone::format(
                                                        $state,
                                                        'd M Y H:i:s'
                                                    )
                                                    : '-'
                                            ),

                                    ]),

                            ]),

                        /*
                        |--------------------------------------------------------------------------
                        | SHIPPING ADDRESS INFORMATION
                        |--------------------------------------------------------------------------
                        */

                        Section::make(
                            '🚚 Shipping Address Information'
                        )
                            ->description(
                                'Shipping destination information for this Assignment Material Requisition.'
                            )
                            ->schema([

                                Grid::make(2)
                                    ->schema([

                                        TextEntry::make(
                                            'shippingAddress.shipping_name'
                                        )
                                            ->label('Shipping Address')
                                            ->weight('bold')
                                            ->columnSpanFull(),

                                        TextEntry::make(
                                            'shippingAddress.address'
                                        )
                                            ->label('Address')
                                            ->columnSpanFull(),

                                        TextEntry::make(
                                            'shippingAddress.city'
                                        )
                                            ->label('City'),

                                        TextEntry::make(
                                            'shippingAddress.province'
                                        )
                                            ->label('Province'),

                                        TextEntry::make(
                                            'shippingAddress.postal_code'
                                        )
                                            ->label('Postal Code'),

                                        TextEntry::make(
                                            'shippingAddress.country'
                                        )
                                            ->label('Country'),

                                        TextEntry::make(
                                            'shippingAddress.attention'
                                        )
                                            ->label('Attention'),

                                        TextEntry::make(
                                            'shippingAddress.contact_person'
                                        )
                                            ->label('Contact Person'),

                                        TextEntry::make(
                                            'shippingAddress.phone'
                                        )
                                            ->label('Phone'),

                                        TextEntry::make(
                                            'shippingAddress.email'
                                        )
                                            ->label('Email'),

                                    ]),

                            ]),

                    ])
                    ->columnSpanFull(),

            ]);
    }
}