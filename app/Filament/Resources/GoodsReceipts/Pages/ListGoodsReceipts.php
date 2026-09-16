<?php

declare(strict_types=1);

namespace App\Filament\Resources\GoodsReceipts\Pages;

use App\Filament\Resources\GoodsReceipts\GoodsReceiptResource;
use App\Filament\Support\Concerns\HasGlobalTransactionFilters;
use Filament\Resources\Pages\ListRecords;

class ListGoodsReceipts extends ListRecords
{
    use HasGlobalTransactionFilters;

    protected static string $resource =
        GoodsReceiptResource::class;

    /*
    |--------------------------------------------------------------------------
    | Header Actions
    |--------------------------------------------------------------------------
    |
    | RR is created from the approved ADM workflow.
    | Manual creation is intentionally disabled.
    |
    |--------------------------------------------------------------------------
    */

    protected function getHeaderActions(): array
    {
        return [];
    }

    /*
    |--------------------------------------------------------------------------
    | Global Transaction Filter Status — RR
    |--------------------------------------------------------------------------
    */

    public function getGlobalTransactionStatusOptions(): array
    {
        return [

            'Draft' =>
                'Draft',

            'Submitted' =>
                'Submitted',

            'Partially Received' =>
                'Partially Received',

            'Received' =>
                'Received',

            'Completed' =>
                'Completed',

            'Cancelled' =>
                'Cancelled',

            'Closed' =>
                'Closed',

        ];
    }
}