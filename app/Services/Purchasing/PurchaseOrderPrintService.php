<?php

declare(strict_types=1);

namespace App\Services\Purchasing;

use App\Models\PurchaseOrder;

class PurchaseOrderPrintService
{

    /**
     * Build all data required by Preview / Print / PDF.
     */
    public function build(
        PurchaseOrder $purchaseOrder,
    ): array {

        $purchaseOrder->loadMissing([
            'supplier',
            'company',
            'currency',
            'requester',
            'warehouse',
            'businessUnit',
            'branch',
            'department',
            'section',
            'costCenter',
            'approvedBy',
            'generatedBy',
            'items',
        ]);

        return [

            /*
            |--------------------------------------------------------------------------
            | Header
            |--------------------------------------------------------------------------
            */

            'purchaseOrder' => $purchaseOrder,

            /*
            |--------------------------------------------------------------------------
            | Company
            |--------------------------------------------------------------------------
            */

            'company' => $purchaseOrder->company,

            /*
            |--------------------------------------------------------------------------
            | Supplier
            |--------------------------------------------------------------------------
            */

            'supplier' => $purchaseOrder->supplier,

            /*
            |--------------------------------------------------------------------------
            | Organization
            |--------------------------------------------------------------------------
            */

            'businessUnit' => $purchaseOrder->businessUnit,

            'branch' => $purchaseOrder->branch,

            'department' => $purchaseOrder->department,

            'section' => $purchaseOrder->section,

            'costCenter' => $purchaseOrder->costCenter,

            'warehouse' => $purchaseOrder->warehouse,

            /*
            |--------------------------------------------------------------------------
            | Currency
            |--------------------------------------------------------------------------
            */

            'currency' => $purchaseOrder->currency,

            /*
            |--------------------------------------------------------------------------
            | Request Information
            |--------------------------------------------------------------------------
            */

            'requester' => $purchaseOrder->requester,

            /*
            |--------------------------------------------------------------------------
            | Approval
            |--------------------------------------------------------------------------
            */

            'approvedBy' => $purchaseOrder->approvedBy,

            'generatedBy' => $purchaseOrder->generatedBy,

            /*
            |--------------------------------------------------------------------------
            | Detail Items
            |--------------------------------------------------------------------------
            */

            'items' => $purchaseOrder->items,

            /*
            |--------------------------------------------------------------------------
            | Financial Summary
            |--------------------------------------------------------------------------
            */

            'subtotal' => $purchaseOrder->subtotal,

            'discountAmount' => $purchaseOrder->discount_amount,

            'taxAmount' => $purchaseOrder->tax_amount,

            'grandTotal' => $purchaseOrder->grand_total,

            /*
            |--------------------------------------------------------------------------
            | Preview Options
            |--------------------------------------------------------------------------
            */

            'autoPrint' => false,

        ];
    }
}