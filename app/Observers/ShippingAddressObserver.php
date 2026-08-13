<?php

declare(strict_types=1);

namespace App\Observers;

use App\Models\ShippingAddress;
use Illuminate\Support\Facades\Auth;

class ShippingAddressObserver
{
    /*
    |--------------------------------------------------------------------------
    | Creating
    |--------------------------------------------------------------------------
    */

    /**
     * Handle the ShippingAddress "creating" event.
     */
    public function creating(
        ShippingAddress $shippingAddress,
    ): void {

        if (
            Auth::check()
            && empty($shippingAddress->created_by)
        ) {

            $shippingAddress->created_by = Auth::id();

        }
    }

    /*
    |--------------------------------------------------------------------------
    | Updating
    |--------------------------------------------------------------------------
    */

    /**
     * Handle the ShippingAddress "updating" event.
     */
    public function updating(
        ShippingAddress $shippingAddress,
    ): void {

        if (Auth::check()) {

            $shippingAddress->updated_by = Auth::id();

        }
    }

    /*
    |--------------------------------------------------------------------------
    | Deleting
    |--------------------------------------------------------------------------
    */

    /**
     * Handle the ShippingAddress "deleting" event.
     */
    public function deleting(
        ShippingAddress $shippingAddress,
    ): void {

        if (
            Auth::check()
            && ! $shippingAddress->isForceDeleting()
        ) {

            $shippingAddress->deleted_by = Auth::id();

            $shippingAddress->saveQuietly();

        }
    }

    /*
    |--------------------------------------------------------------------------
    | Restored
    |--------------------------------------------------------------------------
    */

    /**
     * Handle the ShippingAddress "restored" event.
     */
    public function restored(
        ShippingAddress $shippingAddress,
    ): void {

        $shippingAddress->deleted_by = null;

        $shippingAddress->saveQuietly();

    }

    /*
    |--------------------------------------------------------------------------
    | Force Deleted
    |--------------------------------------------------------------------------
    */

    /**
     * Handle the ShippingAddress "forceDeleted" event.
     */
    public function forceDeleted(
        ShippingAddress $shippingAddress,
    ): void {
        //
    }
}