<?php

declare(strict_types=1);

namespace App\ERP\DataProviders;

use App\Models\PurchaseRequisition;
use Illuminate\Database\Eloquent\Collection;

class PurchaseRequisitionItemDataProvider
{
    /**
     * Load Material Requisition Items.
     */
    public function rows(
        PurchaseRequisition $purchaseRequisition,
    ): Collection {

        return $purchaseRequisition
            ->items()
            ->with([
                'item',
                'uom',
                'warehouse',
            ])
            ->orderBy('id')
            ->get();

    }

    /**
     * Grid Summary.
     *
     * @return array<string,mixed>
     */
    public function summary(
        PurchaseRequisition $purchaseRequisition,
    ): array {

        $items = $purchaseRequisition
            ->items()
            ->get();

        return [

            'total_lines' => $items->count(),

            'total_qty' => $items->sum('quantity'),

            'estimated_amount' => $items->sum(
                fn ($item) => $item->quantity * $item->estimated_unit_price
            ),

        ];

    }
}