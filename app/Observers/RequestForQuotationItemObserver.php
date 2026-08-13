<?php

namespace App\Observers;

use App\Models\RequestForQuotationItem;
use Illuminate\Support\Facades\Auth;

class RequestForQuotationItemObserver
{
    /**
     * Handle the Request For Quotation Item "creating" event.
     */
    public function creating(RequestForQuotationItem $requestForQuotationItem): void
    {
        if (Auth::check() && blank($requestForQuotationItem->created_by)) {
            $requestForQuotationItem->created_by = Auth::id();
        }
    }

    /**
     * Handle the Request For Quotation Item "created" event.
     */
    public function created(RequestForQuotationItem $requestForQuotationItem): void
    {
        //
    }

    /**
     * Handle the Request For Quotation Item "updating" event.
     */
    public function updating(RequestForQuotationItem $requestForQuotationItem): void
    {
        if (Auth::check()) {
            $requestForQuotationItem->updated_by = Auth::id();
        }
    }

    /**
     * Handle the Request For Quotation Item "updated" event.
     */
    public function updated(RequestForQuotationItem $requestForQuotationItem): void
    {
        //
    }

    /**
     * Handle the Request For Quotation Item "deleting" event.
     */
    public function deleting(RequestForQuotationItem $requestForQuotationItem): void
    {
        if (Auth::check()) {
            $requestForQuotationItem->deleted_by = Auth::id();

            // Simpan deleted_by sebelum Soft Delete dijalankan.
            $requestForQuotationItem->saveQuietly();
        }
    }

    /**
     * Handle the Request For Quotation Item "deleted" event.
     */
    public function deleted(RequestForQuotationItem $requestForQuotationItem): void
    {
        //
    }

    /**
     * Handle the Request For Quotation Item "restoring" event.
     */
    public function restoring(RequestForQuotationItem $requestForQuotationItem): void
    {
        //
    }

    /**
     * Handle the Request For Quotation Item "restored" event.
     */
    public function restored(RequestForQuotationItem $requestForQuotationItem): void
    {
        //
    }

    /**
     * Handle the Request For Quotation Item "force deleted" event.
     */
    public function forceDeleted(RequestForQuotationItem $requestForQuotationItem): void
    {
        //
    }
}