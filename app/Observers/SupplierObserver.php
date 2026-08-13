<?php

declare(strict_types=1);

namespace App\Observers;

use App\Models\Supplier;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

class SupplierObserver
{
    /**
     * Handle the Supplier "creating" event.
     */
    public function creating(Supplier $supplier): void
    {
        /*
        |--------------------------------------------------------------------------
        | UUID
        |--------------------------------------------------------------------------
        */

        if (blank($supplier->uuid)) {

            $supplier->uuid = (string) Str::orderedUuid();

        }

        /*
        |--------------------------------------------------------------------------
        | Audit Trail
        |--------------------------------------------------------------------------
        */

        if (Auth::check()) {

            $supplier->forceFill([

                'created_by' => $supplier->created_by ?? Auth::id(),

                'updated_by' => $supplier->updated_by ?? Auth::id(),

            ]);

        }

        /*
        |--------------------------------------------------------------------------
        | Default Financial Value
        |--------------------------------------------------------------------------
        */

        $supplier->credit_limit ??= 0;

        $supplier->opening_balance ??= 0;

        $supplier->vendor_rating ??= 0;

        /*
        |--------------------------------------------------------------------------
        | Default Procurement Value
        |--------------------------------------------------------------------------
        */

        $supplier->lead_time ??= 0;

        $supplier->allow_purchase ??= true;

        $supplier->allow_service ??= true;

        /*
        |--------------------------------------------------------------------------
        | Default Status
        |--------------------------------------------------------------------------
        */

        $supplier->is_preferred ??= false;

        $supplier->is_blacklisted ??= false;

        $supplier->is_active ??= true;

    }

    /**
     * Handle the Supplier "created" event.
     */
    public function created(Supplier $supplier): void
    {
        //
    }

    /**
     * Handle the Supplier "updating" event.
     */
    public function updating(Supplier $supplier): void
    {
        /*
        |--------------------------------------------------------------------------
        | Audit Trail
        |--------------------------------------------------------------------------
        */

        if (Auth::check()) {

            $supplier->forceFill([

                'updated_by' => Auth::id(),

            ]);

        }
    }


    /**
     * Handle the Supplier "updated" event.
     */
    public function updated(Supplier $supplier): void
    {
        //
    }

    /**
     * Handle the Supplier "deleting" event.
     */
    public function deleting(Supplier $supplier): void
    {
        /*
        |--------------------------------------------------------------------------
        | Audit Trail
        |--------------------------------------------------------------------------
        */

        if (
            Auth::check()
            && method_exists($supplier, 'forceFill')
        ) {

            $supplier->forceFill([

                'deleted_by' => Auth::id(),

            ]);

            /*
            |--------------------------------------------------------------------------
            | Save deleted_by without firing Observer again
            |--------------------------------------------------------------------------
            */

            $supplier->saveQuietly();
        }
    }

    /**
     * Handle the Supplier "deleted" event.
     */
    public function deleted(Supplier $supplier): void
    {
        //
    }

    /**
     * Handle the Supplier "restoring" event.
     */
    public function restoring(Supplier $supplier): void
    {
        /*
        |--------------------------------------------------------------------------
        | Clear Deleted By
        |--------------------------------------------------------------------------
        */

        $supplier->forceFill([

            'deleted_by' => null,

        ]);
    }

    /**
     * Handle the Supplier "restored" event.
     */
    public function restored(Supplier $supplier): void
    {
        //
    }

    /**
     * Handle the Supplier "force deleted" event.
     */
    public function forceDeleted(Supplier $supplier): void
    {
        //
    }
}