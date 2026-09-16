<?php

declare(strict_types=1);

namespace App\Filament\Resources\GoodsReceipts\Schemas;

use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class GoodsReceiptInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema->components([
            Section::make('Receiving Record')
                ->schema([
                    TextEntry::make('grn_no')
                        ->label('GRN No'),

                    TextEntry::make('assignmentDirectMarket.document_no')
                        ->label('ADM No')
                        ->placeholder('-') ,

                    TextEntry::make('supplier.supplier_name')
                        ->label('Supplier')
                        ->placeholder('-') ,

                    TextEntry::make('receipt_date')
                        ->label('Receipt Date')
                        ->date(),

                    TextEntry::make('status')
                        ->label('Status')
                        ->badge(),

                    TextEntry::make('remarks')
                        ->label('Remarks')
                        ->placeholder('-')
                        ->columnSpanFull(),
                ])
                ->columns(2),
        ]);
    }
}
