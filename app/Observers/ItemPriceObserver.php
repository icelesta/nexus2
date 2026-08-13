<?php

declare(strict_types=1);

namespace App\Observers;

use App\Models\ItemPrice;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

class ItemPriceObserver
{
    /*
    |--------------------------------------------------------------------------
    | Creating
    |--------------------------------------------------------------------------
    */

    public function creating(ItemPrice $record): void
    {
        if (blank($record->uuid)) {
            $record->uuid = (string) Str::uuid();
        }

        if (Auth::check()) {

            if ($record->isFillable('created_by')) {
                $record->created_by = Auth::id();
            }

            if ($record->isFillable('updated_by')) {
                $record->updated_by = Auth::id();
            }
        }
    }

    /*
    |--------------------------------------------------------------------------
    | Created
    |--------------------------------------------------------------------------
    */

    public function created(ItemPrice $record): void
    {
        //
        // Future:
        // Price History
        // Activity Log
        // Notify Purchasing
        //
    }

    /*
    |--------------------------------------------------------------------------
    | Updating
    |--------------------------------------------------------------------------
    */

    public function updating(ItemPrice $record): void
    {
        if (
            Auth::check() &&
            $record->isFillable('updated_by')
        ) {
            $record->updated_by = Auth::id();
        }
    }

    /*
    |--------------------------------------------------------------------------
    | Updated
    |--------------------------------------------------------------------------
    */

    public function updated(ItemPrice $record): void
    {
        //
        // Future:
        // Price Revision History
        // Cost Recalculation
        //
    }

    /*
    |--------------------------------------------------------------------------
    | Deleting
    |--------------------------------------------------------------------------
    */

    public function deleting(ItemPrice $record): void
    {
        if (
            Auth::check() &&
            $record->isFillable('deleted_by')
        ) {
            $record->deleted_by = Auth::id();

            $record->saveQuietly();
        }
    }

    /*
    |--------------------------------------------------------------------------
    | Deleted
    |--------------------------------------------------------------------------
    */

    public function deleted(ItemPrice $record): void
    {
        //
    }

    /*
    |--------------------------------------------------------------------------
    | Restoring
    |--------------------------------------------------------------------------
    */

    public function restoring(ItemPrice $record): void
    {
        if ($record->isFillable('deleted_by')) {
            $record->deleted_by = null;
        }
    }

    /*
    |--------------------------------------------------------------------------
    | Restored
    |--------------------------------------------------------------------------
    */

    public function restored(ItemPrice $record): void
    {
        //
    }

    /*
    |--------------------------------------------------------------------------
    | Force Deleting
    |--------------------------------------------------------------------------
    */

    public function forceDeleting(ItemPrice $record): void
    {
        //
    }

    /*
    |--------------------------------------------------------------------------
    | Force Deleted
    |--------------------------------------------------------------------------
    */

    public function forceDeleted(ItemPrice $record): void
    {
        //
    }
}