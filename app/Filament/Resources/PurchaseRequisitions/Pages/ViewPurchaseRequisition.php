<?php

declare(strict_types=1);

namespace App\Filament\Resources\PurchaseRequisitions\Pages;

use App\Filament\Resources\PurchaseRequisitions\Actions\ApprovePurchaseRequisition;
use App\Filament\Resources\PurchaseRequisitions\Actions\RejectPurchaseRequisition;
use App\Filament\Resources\PurchaseRequisitions\PurchaseRequisitionResource;
use Filament\Resources\Pages\ViewRecord;
use Illuminate\Contracts\Support\Htmlable;

class ViewPurchaseRequisition extends ViewRecord
{
    protected static string $resource = PurchaseRequisitionResource::class;

    /**
     * ============================================================
     * GOLDEN READ-ONLY VIEW
     * ============================================================
     *
     * Header bawaan Filament sengaja dikosongkan.
     *
     * Breadcrumb tetap muncul:
     *
     * Purchase Requisitions > MR/2026/08/000010 > View
     *
     * Sedangkan document header ditampilkan oleh:
     *
     * PurchaseRequisitionHeader
     * ============================================================
     */

    public function getHeading(): string|Htmlable|null
    {
        return null;
    }

    public function getSubheading(): string|Htmlable|null
    {
        return null;
    }

    /**
     * ============================================================
     * HEADER ACTIONS
     * ============================================================
     *
     * Approval dilakukan dari halaman View MR.
     *
     * Approver wajib membuka dan melakukan checking
     * Material Requisition sebelum melakukan approval.
     *
     * Visibility dan authorization tetap dikendalikan
     * oleh masing-masing Approval Action.
     */
    protected function getHeaderActions(): array
    {
        return [

            /*
            |--------------------------------------------------------------------------
            | APPROVE MATERIAL REQUISITION
            |--------------------------------------------------------------------------
            */

            ApprovePurchaseRequisition::make(),

            /*
            |--------------------------------------------------------------------------
            | REJECT MATERIAL REQUISITION
            |--------------------------------------------------------------------------
            */

            RejectPurchaseRequisition::make(),

        ];
    }
}