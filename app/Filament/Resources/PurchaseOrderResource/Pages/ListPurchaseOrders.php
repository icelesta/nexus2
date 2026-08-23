<?php

declare(strict_types=1);

namespace App\Filament\Resources\PurchaseOrderResource\Pages;

use App\Filament\Resources\PurchaseOrderResource\PurchaseOrderResource;
use App\Filament\Support\Concerns\HasGlobalTransactionFilters;
use Filament\Resources\Pages\ListRecords;

class ListPurchaseOrders extends ListRecords
{
    use HasGlobalTransactionFilters;

    protected static string $resource = PurchaseOrderResource::class;

    /*
    |--------------------------------------------------------------------------
    | Header Actions
    |--------------------------------------------------------------------------
    |
    | Purchase Order is generated from the approved purchasing workflow.
    |
    | Therefore users must NOT create PO manually from this list page.
    |
    */

    protected function getHeaderActions(): array
    {
        return [];
    }

    /*
    |--------------------------------------------------------------------------
    | Global Transaction Filter Status — PO
    |--------------------------------------------------------------------------
    |
    | Global Status Filter for PO uses Approval Status.
    |
    */

    public function getGlobalTransactionStatusOptions(): array
    {
        return [
            'Pending'  => 'Pending',
            'Waiting'  => 'Waiting Approval',
            'Approved' => 'Approved',
            'Rejected' => 'Rejected',
        ];
    }
}