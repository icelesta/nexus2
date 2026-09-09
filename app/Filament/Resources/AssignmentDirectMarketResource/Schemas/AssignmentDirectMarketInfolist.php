<?php

declare(strict_types=1);

namespace App\Filament\Resources\AssignmentDirectMarketResource\Schemas;

use App\Models\AssignmentDirectMarket;

use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Infolists\Components\RepeatableEntry;
use Illuminate\Support\Facades\Storage;

use App\Models\ShippingAddress;

class AssignmentDirectMarketInfolist
{
    public static function configure(
        Schema $schema
    ): Schema {

        return $schema

            ->components([

                /*
                |--------------------------------------------------------------------------
                | DOCUMENT INFORMATION
                |--------------------------------------------------------------------------
                */

                Section::make(
                    'Assignment Direct Market'
                )
                    ->description(
                        'Assignment information generated from Direct Market.'
                    )
                    ->schema([

                        TextEntry::make(
                            'directMarket.dm_no'
                        )
                            ->label('DM No.')
                            ->placeholder('-'),

                        TextEntry::make(
                            'assigned_at'
                        )
                            ->label('Assignment Date')
                            ->date('d M Y')
                            ->placeholder('-'),

                        TextEntry::make(
                            'directMarket.currency.display_name'
                        )
                            ->label('Currency')
                            ->placeholder('-'),

                        TextEntry::make(
                            'request_date'
                        )
                            ->label('Request Date')
                            ->date('d M Y')
                            ->placeholder('-'),

                        TextEntry::make(
                            'required_date'
                        )
                            ->label('Required Date')
                            ->date('d M Y')
                            ->placeholder('-'),

                        TextEntry::make(
                            'assignedBy.name'
                        )
                            ->label('Assigned By')
                            ->placeholder('-'),

                    ])
                    ->columns(3)
                    ->columnSpanFull(),
/*
                |--------------------------------------------------------------------------
                | ORGANIZATION
                |--------------------------------------------------------------------------
                */

                Section::make(
                    'Organization'
                )
                    ->schema([

                        TextEntry::make(
                            'branch.branch_name'
                        )
                            ->label('Branch')
                            ->placeholder('-'),

                        TextEntry::make(
                            'department.department_name'
                        )
                            ->label('Department')
                            ->placeholder('-'),

                        TextEntry::make(
                            'costCenter.cost_center_name'
                        )
                            ->label('Cost Center')
                            ->placeholder('-'),

                        TextEntry::make(
                            'requester.name'
                        )
                            ->label('Requester')
                            ->placeholder('-'),

                        TextEntry::make(
                            'assignedBy.name'
                        )
                            ->label('Assigned By')
                            ->placeholder('-'),

                        TextEntry::make(
                            'directMarket.remarks'
                        )
                            ->label('Remarks')
                            ->placeholder('-'),

                    ])
                    ->columns(3)
                    ->columnSpanFull(),

                /*
                |--------------------------------------------------------------------------
                | PAYMENT & SHIPPING
                |--------------------------------------------------------------------------
                |
                | Read-only presentation aligned with the ADM Edit page.
                |
                */

                Section::make(
                    'Payment & Shipping'
                )
                    ->schema([

                        TextEntry::make(
                            'payment_instruction'
                        )
                            ->label('Payment Instruction')
                            ->placeholder('-'),

                        TextEntry::make(
                            'shippingAddress.shipping_name'
                        )
                            ->label('Shipping Address')
                            ->placeholder('-'),

                        Section::make(
                            '🚚 Shipping Address Information'
                        )
                            ->schema([

                                /*
                                |--------------------------------------------------------------------------
                                | ADDRESS
                                |--------------------------------------------------------------------------
                                */

                                TextEntry::make(
                                    'shipping_address_display'
                                )
                                    ->label('')
                                    ->hiddenLabel()
                                    ->html()
                                    ->state(
                                        function (
                                            AssignmentDirectMarket $record
                                        ): string {

                                            $shipping =
                                                $record->shippingAddress;

                                            if (! $shipping) {
                                                return '-';
                                            }

                                            return
                                                '<div style="line-height:1.7;">'
                                                . '<strong style="font-size:14px;">'
                                                . e(
                                                    $shipping->shipping_name
                                                )
                                                . '</strong>'
                                                . '<br>'
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
                                                . '</div>';
                                        }
                                    )
                                    ->columnSpan(1),

                                /*
                                |--------------------------------------------------------------------------
                                | CONTACT INFORMATION
                                |--------------------------------------------------------------------------
                                */

                                TextEntry::make(
                                    'shipping_contact_display'
                                )
                                    ->label('')
                                    ->hiddenLabel()
                                    ->html()
                                    ->state(
                                        function (
                                            AssignmentDirectMarket $record
                                        ): string {

                                            $shipping =
                                                $record->shippingAddress;

                                            if (! $shipping) {
                                                return '-';
                                            }

                                            return
                                                '<div style="line-height:1.9;">'

                                                . '<div>'
                                                . '<strong style="display:inline-block;width:120px;">'
                                                . 'Attention'
                                                . '</strong>'
                                                . '<span>: '
                                                . e(
                                                    $shipping->attention
                                                )
                                                . '</span>'
                                                . '</div>'

                                                . '<div>'
                                                . '<strong style="display:inline-block;width:120px;">'
                                                . 'Contact Person'
                                                . '</strong>'
                                                . '<span>: '
                                                . e(
                                                    $shipping->contact_person
                                                )
                                                . '</span>'
                                                . '</div>'

                                                . '<div>'
                                                . '<strong style="display:inline-block;width:120px;">'
                                                . 'Phone'
                                                . '</strong>'
                                                . '<span>: '
                                                . e(
                                                    $shipping->phone
                                                )
                                                . '</span>'
                                                . '</div>'

                                                . '<div>'
                                                . '<strong style="display:inline-block;width:120px;">'
                                                . 'Email'
                                                . '</strong>'
                                                . '<span>: '
                                                . e(
                                                    $shipping->email
                                                )
                                                . '</span>'
                                                . '</div>'

                                                . '</div>';
                                        }
                                    )
                                    ->columnSpan(1),

                            ])
                            ->columns(2)
                            ->compact()
                            ->collapsible(false)
                            ->columnSpanFull(),

                    ])
                    ->columns(2)
                    ->columnSpanFull(),


                /*
                |--------------------------------------------------------------------------
                | SUPPORTING DOCUMENTS
                |--------------------------------------------------------------------------
                |
                | Read-only supporting documents for ADM.
                |
                | Edit page owns upload / replace / delete.
                | View page only exposes existing documents.
                |
                */

                Section::make(
                    'Supporting Documents'
                )
                    ->description(
                        'Supplier quotations and other supporting documents for this Assignment Direct Market.'
                    )
                    ->columnSpanFull()
                    ->schema([

                        RepeatableEntry::make(
                            'documents'
                        )
                            ->label('')
                            ->schema([

                                TextEntry::make(
                                    'file_name'
                                )
                                    ->label('Document')
                                    ->weight('bold')
                                    ->placeholder('-'),

                                TextEntry::make(
                                    'document_type'
                                )
                                    ->label('Type')
                                    ->badge()
                                    ->placeholder('-'),

                                TextEntry::make(
                                    'uploader.name'
                                )
                                    ->label('Uploaded By')
                                    ->placeholder('-'),

                                TextEntry::make(
                                    'created_at'
                                )
                                    ->label('Uploaded At')
                                    ->dateTime('d/m/Y H:i')
                                    ->placeholder('-'),

                                TextEntry::make(
                                    'open_document'
                                )
                                    ->label('Open')
                                    ->state('Open Document')
                                    ->icon('heroicon-o-arrow-top-right-on-square')
                                    ->color('primary')
                                    ->url(
                                        fn ($record): ?string =>
                                            filled($record?->file_path)
                                                ? Storage::disk('public')->url(
                                                    $record->file_path
                                                )
                                                : null
                                    )
                                    ->openUrlInNewTab()
                                    ->visible(
                                        fn ($record): bool =>
                                            filled($record?->file_path)
                                    ),

                            ])
                            ->columns(5)
                            ->contained(true)
                            ->columnSpanFull(),

                    ])
                    ->visible(
                        fn ($record): bool =>
                            $record->documents()->exists()
                    ),


            ]);
    }
}
