<?php

namespace App\Observers;

use App\Models\PurchaseRequisitionItem;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

class PurchaseRequisitionItemObserver
{
    /**
     * Handle the Purchase Requisition Item "creating" event.
     */
    public function creating(PurchaseRequisitionItem $purchaseRequisitionItem): void
    {
        if (blank($purchaseRequisitionItem->uuid)) {
            $purchaseRequisitionItem->uuid = (string) Str::orderedUuid();
        }

        if (blank($purchaseRequisitionItem->status)) {
            $purchaseRequisitionItem->status = PurchaseRequisitionItem::STATUS_DRAFT;
        }

        if (Auth::check() && blank($purchaseRequisitionItem->created_by)) {
            $purchaseRequisitionItem->created_by = Auth::id();
        }
    }

    /**
     * Handle the Purchase Requisition Item "created" event.
     */
    public function created(PurchaseRequisitionItem $purchaseRequisitionItem): void
    {
        //
    }

    /**
     * Handle the Purchase Requisition Item "updating" event.
     */
    public function updating(PurchaseRequisitionItem $purchaseRequisitionItem): void
    {
        if (Auth::check()) {
            $purchaseRequisitionItem->updated_by = Auth::id();
        }
    }

    /**
     * Handle the Purchase Requisition Item "updated" event.
     */
    public function updated(PurchaseRequisitionItem $purchaseRequisitionItem): void
    {
        //
    }

    /**
     * Handle the Purchase Requisition Item "deleting" event.
     */
    public function deleting(PurchaseRequisitionItem $purchaseRequisitionItem): void
    {
        if (Auth::check()) {
            $purchaseRequisitionItem->deleted_by = Auth::id();

            /*
             | Persist deleted_by sebelum Soft Delete dijalankan.
             */
            $purchaseRequisitionItem->saveQuietly();
        }
    }

    /**
     * Handle the Purchase Requisition Item "deleted" event.
     */
    public function deleted(PurchaseRequisitionItem $purchaseRequisitionItem): void
    {
        //
    }

    /**
     * Handle the Purchase Requisition Item "restoring" event.
     */
    public function restoring(PurchaseRequisitionItem $purchaseRequisitionItem): void
    {
        //
    }

    /**
     * Handle the Purchase Requisition Item "restored" event.
     */
    public function restored(PurchaseRequisitionItem $purchaseRequisitionItem): void
    {
        //
    }

    /**
     * Handle the Purchase Requisition Item "force deleted" event.
     */
    public function forceDeleted(PurchaseRequisitionItem $purchaseRequisitionItem): void
    {
        //
    }
}