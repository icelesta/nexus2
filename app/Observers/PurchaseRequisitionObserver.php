<?php

declare(strict_types=1);

namespace App\Observers;

use App\Models\PurchaseRequisition;
use App\Services\Numbering\NumberingService;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

class PurchaseRequisitionObserver
{
    public function __construct(
        protected NumberingService $numberingService,
    ) {}

    /**
     * Handle the Purchase Requisition "creating" event.
     */
    public function creating(
        PurchaseRequisition $purchaseRequisition,
    ): void {

        /*
        |--------------------------------------------------------------------------
        | UUID
        |--------------------------------------------------------------------------
        */

        if (blank($purchaseRequisition->uuid)) {

            $purchaseRequisition->uuid = (string) Str::orderedUuid();

        }

        /*
        |--------------------------------------------------------------------------
        | Document Number
        |--------------------------------------------------------------------------
        */

        if (blank($purchaseRequisition->pr_no)) {

            $purchaseRequisition->pr_no = $this->numberingService->generate(
                documentType: 'PURCHASE_REQUISITION',
                companyId: $purchaseRequisition->company_id,
                businessUnitId: $purchaseRequisition->business_unit_id,
                branchId: $purchaseRequisition->branch_id,
            );

        }

        /*
        |--------------------------------------------------------------------------
        | Status
        |--------------------------------------------------------------------------
        */

        if (blank($purchaseRequisition->status)) {

            $purchaseRequisition->status =
                PurchaseRequisition::STATUS_DRAFT;

        }

        /*
        |--------------------------------------------------------------------------
        | Audit Trail
        |--------------------------------------------------------------------------
        */

        if (Auth::check()) {

            $purchaseRequisition->forceFill([

                'created_by' => Auth::id(),

            ]);

        }
    }

    /**
     * Handle the Purchase Requisition "created" event.
     */
    public function created(
        PurchaseRequisition $purchaseRequisition,
    ): void
    {
        //
    }

    /**
     * Handle the Purchase Requisition "updating" event.
     */
    public function updating(
        PurchaseRequisition $purchaseRequisition,
    ): void {

        if (! Auth::check()) {
            return;
        }

        $purchaseRequisition->forceFill([

            'updated_by' => Auth::id(),

        ]);
    }

    /**
     * Handle the Purchase Requisition "updated" event.
     */
    public function updated(
        PurchaseRequisition $purchaseRequisition,
    ): void
    {
        //
    }

    /**
     * Handle the Purchase Requisition "deleting" event.
     */
    public function deleting(
        PurchaseRequisition $purchaseRequisition,
    ): void {

        if (! Auth::check()) {
            return;
        }

        $purchaseRequisition->forceFill([

            'deleted_by' => Auth::id(),

        ]);

        /*
        |--------------------------------------------------------------------------
        | Save Audit Before Soft Delete
        |--------------------------------------------------------------------------
        */

        $purchaseRequisition->saveQuietly();
    }

    /**
     * Handle the Purchase Requisition "deleted" event.
     */
    public function deleted(
        PurchaseRequisition $purchaseRequisition,
    ): void
    {
        //
    }

    /**
     * Handle the Purchase Requisition "restoring" event.
     */
    public function restoring(
        PurchaseRequisition $purchaseRequisition,
    ): void
    {
        //
    }

    /**
     * Handle the Purchase Requisition "restored" event.
     */
    public function restored(
        PurchaseRequisition $purchaseRequisition,
    ): void
    {
        //
    }

    /**
     * Handle the Purchase Requisition "force deleted" event.
     */
    public function forceDeleted(
        PurchaseRequisition $purchaseRequisition,
    ): void
    {
        //
    }
}