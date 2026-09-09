<?php

declare(strict_types=1);

namespace App\Filament\Resources\DirectMarkets\Schemas;

use App\Models\DirectMarket;
use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\View;
use Filament\Schemas\Schema;

class DirectMarketView
{
    public static function configure(
        Schema $schema,
    ): Schema {

        return $schema

            ->columns(1)

            ->components([

                /*
                |--------------------------------------------------------------------------
                | DOCUMENT HEADER
                |--------------------------------------------------------------------------
                */

                Section::make('Direct Market')
                    ->schema([

                        View::make(
                            'filament.resources.direct-markets.pages.direct-market-header'
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
                        'Direct Market document information.'
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
                                            ->date('d M Y')
                                            ->placeholder('-'),

                                        TextEntry::make('requester.name')
                                            ->label('Requester')
                                            ->placeholder('-'),

                                        TextEntry::make('currency.display_name')
                                            ->label('Currency')
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

                                        TextEntry::make(
                                            'costCenter.cost_center_name'
                                        )
                                            ->label('Cost Center')
                                            ->placeholder('-'),

                                        TextEntry::make('warehouse.warehouse_name')
                                            ->label('Warehouse')
                                            ->placeholder('-'),

                                        TextEntry::make('dm_no')
                                            ->label('DM Number')
                                            ->weight('bold')
                                            ->copyable(),

                                    ])
                                    ->columns(2),

                            ])
                            ->columnSpanFull(),

                    ])
                    ->columnSpanFull()
                    ->collapsible(false),

                /*
                |--------------------------------------------------------------------------
                | DIRECT MARKET ITEMS
                |--------------------------------------------------------------------------
                |
                | GOLDEN READ ONLY
                |
                | Horizontal table pattern follows Material Requisition.
                |
                */

                Section::make('Direct Market Items')
                    ->description(
                        'Items requested through this Direct Market.'
                    )
                    ->schema([

                        View::make(
                            'filament.resources.direct-markets.pages.direct-market-items-table-view'
                        ),

                    ])
                    ->columnSpanFull()
                    ->collapsible(false),

            ]);
    }
}