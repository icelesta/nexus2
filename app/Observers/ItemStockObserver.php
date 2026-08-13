<?php

declare(strict_types=1);

namespace App\Observers;

use App\Models\ItemStock;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

class ItemStockObserver
{
    /*
    |--------------------------------------------------------------------------
    | Creating
    |--------------------------------------------------------------------------
    */

    public function creating(ItemStock $record): void
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

    public function created(ItemStock $record): void
    {
        //
        // Future:
        // Activity Log
        // Inventory Dashboard Refresh
        //
    }

    /*
    |--------------------------------------------------------------------------
    | Updating
    |--------------------------------------------------------------------------
    */

    public function updating(ItemStock $record): void
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

    public function updated(ItemStock $record): void
    {
        //
        // Future:
        // Recalculate Stock Summary
        // Warehouse KPI Refresh
        //
    }

    /*
    |--------------------------------------------------------------------------
    | Deleting
    |--------------------------------------------------------------------------
    */

    public function deleting(ItemStock $record): void
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

    public function deleted(ItemStock $record): void
    {
        //
    }

    /*
    |--------------------------------------------------------------------------
    | Restoring
    |--------------------------------------------------------------------------
    */

    public function restoring(ItemStock $record): void
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

    public function restored(ItemStock $record): void
    {
        //
    }

    /*
    |--------------------------------------------------------------------------
    | Force Deleting
    |--------------------------------------------------------------------------
    */

    public function forceDeleting(ItemStock $record): void
    {
        //
    }

    /*
    |--------------------------------------------------------------------------
    | Force Deleted
    |--------------------------------------------------------------------------
    */

    public function forceDeleted(ItemStock $record): void
    {
        //
    }
}