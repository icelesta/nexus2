<?php

namespace App\Observers;

use App\Models\GoodsReceipt;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

class GoodsReceiptObserver
{
    /**
     * Handle the Goods Receipt "creating" event.
     */
    public function creating(GoodsReceipt $goodsReceipt): void
    {
        if (blank($goodsReceipt->uuid)) {
            $goodsReceipt->uuid = (string) Str::orderedUuid();
        }

        if (blank($goodsReceipt->status)) {
            $goodsReceipt->status = GoodsReceipt::STATUS_DRAFT;
        }

        if (Auth::check() && blank($goodsReceipt->created_by)) {
            $goodsReceipt->created_by = Auth::id();
        }
    }

    /**
     * Handle the Goods Receipt "created" event.
     */
    public function created(GoodsReceipt $goodsReceipt): void
    {
        //
    }

    /**
     * Handle the Goods Receipt "updating" event.
     */
    public function updating(GoodsReceipt $goodsReceipt): void
    {
        if (Auth::check()) {
            $goodsReceipt->updated_by = Auth::id();
        }
    }

    /**
     * Handle the Goods Receipt "updated" event.
     */
    public function updated(GoodsReceipt $goodsReceipt): void
    {
        //
    }

    /**
     * Handle the Goods Receipt "deleting" event.
     */
    public function deleting(GoodsReceipt $goodsReceipt): void
    {
        if (Auth::check()) {
            $goodsReceipt->deleted_by = Auth::id();

            // Simpan deleted_by sebelum Soft Delete dijalankan.
            $goodsReceipt->saveQuietly();
        }
    }

    /**
     * Handle the Goods Receipt "deleted" event.
     */
    public function deleted(GoodsReceipt $goodsReceipt): void
    {
        //
    }

    /**
     * Handle the Goods Receipt "restoring" event.
     */
    public function restoring(GoodsReceipt $goodsReceipt): void
    {
        //
    }

    /**
     * Handle the Goods Receipt "restored" event.
     */
    public function restored(GoodsReceipt $goodsReceipt): void
    {
        //
    }

    /**
     * Handle the Goods Receipt "force deleted" event.
     */
    public function forceDeleted(GoodsReceipt $goodsReceipt): void
    {
        //
    }
}