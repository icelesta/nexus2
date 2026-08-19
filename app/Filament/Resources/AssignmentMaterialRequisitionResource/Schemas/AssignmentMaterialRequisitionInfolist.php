<?php

namespace App\Filament\Resources\AssignmentMaterialRequisitionResource\Schemas;

use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

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

                                        TextEntry::make('document_no')
                                            ->label('Assignment Number'),

                                        TextEntry::make('document_date')
                                            ->label('Assignment Date')
                                            ->date(),

                                        TextEntry::make('assignedTo.name')
                                            ->label('Assigned To'),

                                        TextEntry::make('assignedBy.name')
                                            ->label('Assigned By'),

                                        TextEntry::make('status')
                                            ->label('Status')
                                            ->badge(),

                                        TextEntry::make('remarks')
                                            ->label('Remarks')
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
                                            ->since(),

                                        TextEntry::make('updatedBy.name')
                                            ->label('Updated By'),

                                        TextEntry::make('updated_at')
                                            ->label('Updated At')
                                            ->since(),

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