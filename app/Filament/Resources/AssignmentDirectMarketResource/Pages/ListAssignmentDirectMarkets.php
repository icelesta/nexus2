<?php

declare(strict_types=1);

namespace App\Filament\Resources\AssignmentDirectMarketResource\Pages;

use App\Filament\Resources\AssignmentDirectMarketResource\AssignmentDirectMarketResource;
use App\Filament\Support\Concerns\HasGlobalTransactionFilters;

use Filament\Resources\Pages\ListRecords;

class ListAssignmentDirectMarkets extends ListRecords
{
    use HasGlobalTransactionFilters;

    protected static string $resource =
        AssignmentDirectMarketResource::class;

    /*
    |--------------------------------------------------------------------------
    | Header Actions
    |--------------------------------------------------------------------------
    |
    | ADM is generated automatically from an Approved Direct Market.
    |
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
    | Global Transaction Filter Status — ADM
    |--------------------------------------------------------------------------
    */

    public function getGlobalTransactionStatusOptions(): array
    {
        return [

            'Draft' =>
                'Draft',

            'Submitted' =>
                'Submitted',

            'Updated' =>
                'Updated',

            'Waiting Approval' =>
                'Waiting Approval',

            'Approved' =>
                'Approved',

            'Completed' =>
                'Completed',

            'Rejected' =>
                'Rejected',

            'Cancelled' =>
                'Cancelled',

        ];
    }
}