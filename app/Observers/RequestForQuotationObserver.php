<?php

namespace App\Observers;

use App\Models\RequestForQuotation;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

class RequestForQuotationObserver
{
    /**
     * Handle the Request For Quotation "creating" event.
     */
    public function creating(RequestForQuotation $requestForQuotation): void
    {
        if (blank($requestForQuotation->uuid)) {
            $requestForQuotation->uuid = (string) Str::orderedUuid();
        }

        if (blank($requestForQuotation->status)) {
            $requestForQuotation->status = RequestForQuotation::STATUS_DRAFT;
        }

        if (Auth::check() && blank($requestForQuotation->created_by)) {
            $requestForQuotation->created_by = Auth::id();
        }
    }

    /**
     * Handle the Request For Quotation "created" event.
     */
    public function created(RequestForQuotation $requestForQuotation): void
    {
        //
    }

    /**
     * Handle the Request For Quotation "updating" event.
     */
    public function updating(RequestForQuotation $requestForQuotation): void
    {
        if (Auth::check()) {
            $requestForQuotation->updated_by = Auth::id();
        }
    }

    /**
     * Handle the Request For Quotation "updated" event.
     */
    public function updated(RequestForQuotation $requestForQuotation): void
    {
        //
    }

    /**
     * Handle the Request For Quotation "deleting" event.
     */
    public function deleting(RequestForQuotation $requestForQuotation): void
    {
        if (Auth::check()) {
            $requestForQuotation->deleted_by = Auth::id();

            // Simpan deleted_by sebelum Soft Delete dijalankan.
            $requestForQuotation->saveQuietly();
        }
    }

    /**
     * Handle the Request For Quotation "deleted" event.
     */
    public function deleted(RequestForQuotation $requestForQuotation): void
    {
        //
    }

    /**
     * Handle the Request For Quotation "restoring" event.
     */
    public function restoring(RequestForQuotation $requestForQuotation): void
    {
        //
    }

    /**
     * Handle the Request For Quotation "restored" event.
     */
    public function restored(RequestForQuotation $requestForQuotation): void
    {
        //
    }

    /**
     * Handle the Request For Quotation "force deleted" event.
     */
    public function forceDeleted(RequestForQuotation $requestForQuotation): void
    {
        //
    }
}