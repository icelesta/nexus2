<?php

declare(strict_types=1);

namespace App\Filament\Resources\PurchaseRequisitions\Schemas;

use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\View;
use Filament\Schemas\Schema;

class PurchaseRequisitionView
{
    public static function configure(
        Schema $schema,
    ): Schema {

        return $schema
            ->components([

                /*
                |--------------------------------------------------------------------------
                | DOCUMENT HEADER
                |--------------------------------------------------------------------------
                */

                Section::make('Material Requisition')
                    ->schema([

                        View::make(
                            'filament.resources.purchase-requisitions.pages.purchase-requisition-header'
                        ),

                    ])
                    ->columnSpanFull()
                    ->collapsible(false),

                /*
                |--------------------------------------------------------------------------
                | GENERAL INFORMATION
                |--------------------------------------------------------------------------
                */

                Section::make('General Information')
                    ->description(
                        'Material Requisition document information.'
                    )
                    ->schema([

                        Grid::make(2)
                            ->schema([

                                /*
                                |--------------------------------------------------------------------------
                                | REQUEST INFORMATION
                                |--------------------------------------------------------------------------
                                */

                                Section::make('Request Information')
                                    ->schema([

                                        TextEntry::make('request_date')
                                            ->label('Request Date')
                                            ->date('d M Y'),

                                        TextEntry::make('required_date')
                                            ->label('Required Date')
                                            ->date('d M Y'),

                                        TextEntry::make('priority')
                                            ->label('Priority')
                                            ->formatStateUsing(
                                                fn ($state) =>
                                                    $state
                                                        ? ucfirst((string) $state)
                                                        : '-'
                                            ),

                                        TextEntry::make('requester.name')
                                            ->label('Requester')
                                            ->placeholder('-'),

                                        TextEntry::make('delivery_location')
                                            ->label('Delivery Location')
                                            ->placeholder('-'),

                                        TextEntry::make('reference_no')
                                            ->label('Reference No')
                                            ->placeholder('-'),

                                        TextEntry::make('remarks')
                                            ->label('Remarks')
                                            ->placeholder('-')
                                            ->columnSpanFull(),

                                    ])
                                    ->columns(2),

                                /*
                                |--------------------------------------------------------------------------
                                | ORGANIZATION INFORMATION
                                |--------------------------------------------------------------------------
                                */

                                Section::make('Organization Information')
                                    ->schema([

                                        TextEntry::make('company.company_name')
                                            ->label('Company')
                                            ->placeholder('-'),

                                        TextEntry::make(
                                            'businessUnit.business_unit_name'
                                        )
                                            ->label('Business Unit')
                                            ->placeholder('-'),

                                        TextEntry::make('branch.branch_name')
                                            ->label('Branch')
                                            ->placeholder('-'),

                                        TextEntry::make(
                                            'department.department_name'
                                        )
                                            ->label('Department')
                                            ->placeholder('-'),

                                        TextEntry::make('section.section_name')
                                            ->label('Section')
                                            ->placeholder('-'),

                                        TextEntry::make(
                                            'costCenter.cost_center_name'
                                        )
                                            ->label('Cost Center')
                                            ->placeholder('-'),

                                        TextEntry::make(
                                            'warehouse.warehouse_name'
                                        )
                                            ->label('Warehouse')
                                            ->placeholder('-'),

                                        TextEntry::make(
                                            'currency.currency_code'
                                        )
                                            ->label('Currency')
                                            ->placeholder('-'),

                                    ])
                                    ->columns(2),

                            ])
                            ->columnSpanFull(),

                    ])
                    ->columnSpanFull()
                    ->collapsible(false),

                /*
                |--------------------------------------------------------------------------
                | MATERIAL REQUISITION ITEMS
                |--------------------------------------------------------------------------
                |
                | GOLDEN READ-ONLY
                |
                | Commercial information is synchronized from AMR.
                |
                */

                Section::make('Material Requisition Items')
                    ->description(
                        'Requested items and current commercial assignment from Assignment Material Requisition.'
                    )
                    ->schema([

                        View::make(
                            'filament.resources.purchase-requisitions.pages.purchase-requisition-items-table-view'
                        ),

                    ])
                    ->columnSpanFull()
                    ->collapsible(false),


            ]);
    }
}