<?php

declare(strict_types=1);

namespace App\Filament\Resources\GoodsReceipts\Pages;

use App\Filament\Resources\GoodsReceipts\GoodsReceiptResource;
use App\Filament\Resources\GoodsReceipts\Pages\ListGoodsReceipts;
use App\Filament\Support\Concerns\HasGlobalTransactionFilters;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;


class ListGoodsReceipts extends ListRecords
{
    use HasGlobalTransactionFilters;

    protected static string $resource = GoodsReceiptResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make('createReceivingRecord')
                ->label('Create Receiving Record')
                ->icon('heroicon-o-plus')
                ->url(
                    fn (): string =>
                        GoodsReceiptResource::getUrl('create')
                ),
        ];
    }

    public function getGlobalTransactionStatusOptions(): array
    {
        return [
            'Draft' => 'Draft',
            'Submitted' => 'Submitted',
            'Partially Received' => 'Partially Received',
            'Received' => 'Received',
            'Completed' => 'Completed',
            'Cancelled' => 'Cancelled',
            'Closed' => 'Closed',
        ];
    }
}
