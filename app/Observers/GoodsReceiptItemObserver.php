<?php

namespace App\Observers;

use App\Models\GoodsReceiptItem;
use Illuminate\Support\Facades\Auth;

class GoodsReceiptItemObserver
{
    /**
     * Handle the Goods Receipt Item "creating" event.
     */
    public function creating(GoodsReceiptItem $goodsReceiptItem): void
    {
        if (Auth::check() && blank($goodsReceiptItem->created_by)) {
            $goodsReceiptItem->created_by = Auth::id();
        }
    }

    /**
     * Handle the Goods Receipt Item "created" event.
     */
    public function created(GoodsReceiptItem $goodsReceiptItem): void
    {
        //
    }

    /**
     * Handle the Goods Receipt Item "updating" event.
     */
    public function updating(GoodsReceiptItem $goodsReceiptItem): void
    {
        if (Auth::check()) {
            $goodsReceiptItem->updated_by = Auth::id();
        }
    }

    /**
     * Handle the Goods Receipt Item "updated" event.
     */
    public function updated(GoodsReceiptItem $goodsReceiptItem): void
    {
        //
    }

    /**
     * Handle the Goods Receipt Item "deleting" event.
     */
    public function deleting(GoodsReceiptItem $goodsReceiptItem): void
    {
        if (Auth::check()) {
            $goodsReceiptItem->deleted_by = Auth::id();

            // Simpan deleted_by sebelum Soft Delete dijalankan.
            $goodsReceiptItem->saveQuietly();
        }
    }

    /**
     * Handle the Goods Receipt Item "deleted" event.
     */
    public function deleted(GoodsReceiptItem $goodsReceiptItem): void
    {
        //
    }

    /**
     * Handle the Goods Receipt Item "restoring" event.
     */
    public function restoring(GoodsReceiptItem $goodsReceiptItem): void
    {
        //
    }

    /**
     * Handle the Goods Receipt Item "restored" event.
     */
    public function restored(GoodsReceiptItem $goodsReceiptItem): void
    {
        //
    }

    /**
     * Handle the Goods Receipt Item "force deleted" event.
     */
    public function forceDeleted(GoodsReceiptItem $goodsReceiptItem): void
    {
        //
    }
}