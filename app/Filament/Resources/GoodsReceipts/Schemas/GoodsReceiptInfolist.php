<?php

declare(strict_types=1);

namespace App\Filament\Resources\GoodsReceipts\Schemas;

use App\Models\DirectMarket;
use App\Models\GoodsReceipt;

use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\View;
use Filament\Schemas\Schema;

class GoodsReceiptInfolist
{
    public static function configure(
        Schema $schema
    ): Schema {

        return $schema
            ->components([

                /*
                |--------------------------------------------------------------------------
                | RECEIVING RECORD INFORMATION
                |--------------------------------------------------------------------------
                */

                Section::make('Receiving Record Information')
                    ->icon('heroicon-o-clipboard-document-check')
                    ->schema([

                        /*
                        |--------------------------------------------------------------------------
                        | ROW 1
                        | Supplier | Source Type | Source No
                        |--------------------------------------------------------------------------
                        */

                        Grid::make(12)
                            ->schema([

                                /*
                                |--------------------------------------------------------------------------
                                | SUPPLIER
                                |--------------------------------------------------------------------------
                                */

                                TextEntry::make('supplier_display')
                                    ->label('Supplier')
                                    ->state(
                                        function (
                                            GoodsReceipt $record
                                        ): string {

                                            if (! $record->supplier) {
                                                return '-';
                                            }

                                            return collect([
                                                $record->supplier->supplier_code,
                                                $record->supplier->supplier_name,
                                            ])
                                                ->filter()
                                                ->implode(' - ');
                                        }
                                    )
                                    ->extraAttributes([
                                        'class' =>
                                            'rounded-lg border border-gray-200 bg-gray-50 px-4 py-2.5 shadow-sm dark:border-gray-700 dark:bg-gray-900/40',
                                    ])
                                    ->columnSpan(4),

                                /*
                                |--------------------------------------------------------------------------
                                | SOURCE TYPE
                                |--------------------------------------------------------------------------
                                */

                                TextEntry::make('source_type_display')
                                    ->label('Source Type')
                                    ->state(
                                        function (
                                            GoodsReceipt $record
                                        ): string {

                                            if (
                                                $record->purchase_order_id
                                                !== null
                                            ) {
                                                return 'Purchase Order';
                                            }

                                            if (
                                                $record
                                                    ->assignment_direct_market_id
                                                !== null
                                            ) {
                                                return 'Direct Market';
                                            }

                                            return '-';
                                        }
                                    )
                                    ->extraAttributes([
                                        'class' =>
                                            'rounded-lg border border-gray-200 bg-gray-50 px-4 py-2.5 shadow-sm dark:border-gray-700 dark:bg-gray-900/40',
                                    ])
                                    ->columnSpan(4),

                                /*
                                |--------------------------------------------------------------------------
                                | SOURCE NO
                                |--------------------------------------------------------------------------
                                */

                                TextEntry::make('source_no')
                                    ->label('Source No')
                                    ->state(
                                        function (
                                            GoodsReceipt $record
                                        ): string {

                                            /*
                                            |--------------------------------------------------------------------------
                                            | PURCHASE ORDER
                                            |--------------------------------------------------------------------------
                                            */

                                            if (
                                                $record->purchaseOrder
                                            ) {
                                                return $record
                                                    ->purchaseOrder
                                                    ->document_no
                                                    ?? '-';
                                            }

                                            /*
                                            |--------------------------------------------------------------------------
                                            | DIRECT MARKET
                                            |--------------------------------------------------------------------------
                                            */

                                            if (
                                                $record
                                                    ->assignmentDirectMarket
                                            ) {

                                                $assignment =
                                                    $record
                                                        ->assignmentDirectMarket;

                                                if (
                                                    $assignment
                                                        ->direct_market_id
                                                ) {

                                                    $directMarket =
                                                        DirectMarket::query()
                                                            ->find(
                                                                $assignment
                                                                    ->direct_market_id
                                                            );

                                                    return $directMarket
                                                        ?->dm_no
                                                        ??
                                                        $assignment
                                                            ->document_no
                                                        ??
                                                        '-';
                                                }

                                                return $assignment
                                                    ->document_no
                                                    ?? '-';
                                            }

                                            return '-';
                                        }
                                    )
                                    ->extraAttributes([
                                        'class' =>
                                            'rounded-lg border border-gray-200 bg-gray-50 px-4 py-2.5 shadow-sm dark:border-gray-700 dark:bg-gray-900/40',
                                    ])
                                    ->columnSpan(4),

                            ]),

                        /*
                        |--------------------------------------------------------------------------
                        | ROW 2
                        | Receiving Date | RR No | Received By
                        |--------------------------------------------------------------------------
                        */

                        Grid::make(12)
                            ->schema([

                                /*
                                |--------------------------------------------------------------------------
                                | RECEIVING DATE
                                |--------------------------------------------------------------------------
                                */

                                TextEntry::make('receipt_date')
                                    ->label('Receiving Date')
                                    ->date('d M Y')
                                    ->extraAttributes([
                                        'class' =>
                                            'rounded-lg border border-gray-200 bg-gray-50 px-4 py-2.5 shadow-sm dark:border-gray-700 dark:bg-gray-900/40',
                                    ])
                                    ->columnSpan(4),

                                /*
                                |--------------------------------------------------------------------------
                                | RR NO
                                |--------------------------------------------------------------------------
                                */

                                TextEntry::make('grn_no')
                                    ->label('RR No')
                                    ->extraAttributes([
                                        'class' =>
                                            'rounded-lg border border-gray-200 bg-gray-50 px-4 py-2.5 shadow-sm dark:border-gray-700 dark:bg-gray-900/40',
                                    ])
                                    ->columnSpan(4),

                                /*
                                |--------------------------------------------------------------------------
                                | RECEIVED BY
                                |--------------------------------------------------------------------------
                                */

                                TextEntry::make('received_by_display')
                                    ->label('Received By')
                                    ->state(
                                        function (GoodsReceipt $record): string {
                                            if (blank($record->created_by)) {
                                                return '-';
                                            }

                                            return \App\Models\User::query()
                                                ->whereKey($record->created_by)
                                                ->value('name')
                                                ?? '-';
                                        }
                                    )
                                    ->extraAttributes([
                                        'class' =>
                                            'rounded-lg border border-gray-200 bg-gray-50 px-4 py-2.5 shadow-sm dark:border-gray-700 dark:bg-gray-900/40',
                                    ])
                                    ->columnSpan(4),
                            ]),

                        /*
                        |--------------------------------------------------------------------------
                        | ROW 3
                        | Payment Term | Currency
                        |--------------------------------------------------------------------------
                        */

                        Grid::make(12)
                            ->schema([

                                /*
                                |--------------------------------------------------------------------------
                                | PAYMENT TERM
                                |--------------------------------------------------------------------------
                                */

                                TextEntry::make('payment_term_display')
                                    ->label('Payment Term')
                                    ->state(
                                        function (
                                            GoodsReceipt $record
                                        ): string {

                                            return $record
                                                ->supplier
                                                ?->paymentTerm
                                                ?->term_name
                                                ?? '-';
                                        }
                                    )
                                    ->extraAttributes([
                                        'class' =>
                                            'rounded-lg border border-gray-200 bg-gray-50 px-4 py-2.5 shadow-sm dark:border-gray-700 dark:bg-gray-900/40',
                                    ])
                                    ->columnSpan(4),

                                /*
                                |--------------------------------------------------------------------------
                                | CURRENCY
                                |--------------------------------------------------------------------------
                                */

                                TextEntry::make('currency_display')
                                    ->label('Currency')
                                    ->state(
                                        function (
                                            GoodsReceipt $record
                                        ): string {

                                            /*
                                            |--------------------------------------------------------------------------
                                            | PURCHASE ORDER
                                            |--------------------------------------------------------------------------
                                            */

                                            if (
                                                $record->purchaseOrder
                                            ) {
                                                return $record
                                                    ->purchaseOrder
                                                    ->currency
                                                    ?->currency_code
                                                    ?? '-';
                                            }

                                            /*
                                            |--------------------------------------------------------------------------
                                            | DIRECT MARKET
                                            |--------------------------------------------------------------------------
                                            */

                                            if (
                                                $record
                                                    ->assignmentDirectMarket
                                            ) {

                                                $assignment =
                                                    $record
                                                        ->assignmentDirectMarket;

                                                if (
                                                    $assignment
                                                        ->direct_market_id
                                                ) {

                                                    $directMarket =
                                                        DirectMarket::query()
                                                            ->with('currency')
                                                            ->find(
                                                                $assignment
                                                                    ->direct_market_id
                                                            );

                                                    return $directMarket
                                                        ?->currency
                                                        ?->currency_code
                                                        ?? '-';
                                                }
                                            }

                                            return '-';
                                        }
                                    )
                                    ->extraAttributes([
                                        'class' =>
                                            'rounded-lg border border-gray-200 bg-gray-50 px-4 py-2.5 shadow-sm dark:border-gray-700 dark:bg-gray-900/40',
                                    ])
                                    ->columnSpan(4),

                            ]),

                        /*
                        |--------------------------------------------------------------------------
                        | ROW 4
                        | Document Number | Remark
                        |--------------------------------------------------------------------------
                        */

                        Grid::make(12)
                            ->schema([

                                /*
                                |--------------------------------------------------------------------------
                                | DOCUMENT NUMBER
                                |--------------------------------------------------------------------------
                                */

                                TextEntry::make('document_number')
                                    ->label('Document Number')
                                    ->placeholder('-')
                                    ->extraAttributes([
                                        'class' =>
                                            'rounded-lg border border-gray-200 bg-gray-50 px-4 py-2.5 shadow-sm dark:border-gray-700 dark:bg-gray-900/40',
                                    ])
                                    ->columnSpan(6),

                                /*
                                |--------------------------------------------------------------------------
                                | REMARK
                                |--------------------------------------------------------------------------
                                */

                                TextEntry::make('remarks')
                                    ->label('Remark')
                                    ->placeholder('-')
                                    ->extraAttributes([
                                        'class' =>
                                            'rounded-lg border border-gray-200 bg-gray-50 px-4 py-2.5 shadow-sm dark:border-gray-700 dark:bg-gray-900/40',
                                    ])
                                    ->columnSpan(6),

                            ]),



                    ])
                    ->columnSpanFull(),

                /*
                |--------------------------------------------------------------------------
                | AVAILABLE SOURCE ITEMS
                |--------------------------------------------------------------------------
                */

                View::make(
                    'filament.resources.goods-receipts.view-source-items'
                )
                    ->columnSpanFull(),

            ]);
    }
}