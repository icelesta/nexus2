<?php

declare(strict_types=1);

namespace App\Observers;

use App\Models\ItemSerial;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

class ItemSerialObserver
{
    /*
    |--------------------------------------------------------------------------
    | Creating
    |--------------------------------------------------------------------------
    */

    public function creating(ItemSerial $record): void
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

    public function created(ItemSerial $record): void
    {
        //
        // Future:
        // Activity Log
        // Generate QR Code
        // Register Serial Traceability
        //
    }

    /*
    |--------------------------------------------------------------------------
    | Updating
    |--------------------------------------------------------------------------
    */

    public function updating(ItemSerial $record): void
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

    public function updated(ItemSerial $record): void
    {
        //
        // Future:
        // Lifecycle History
        // Status Synchronization
        //
    }

    /*
    |--------------------------------------------------------------------------
    | Deleting
    |--------------------------------------------------------------------------
    */

    public function deleting(ItemSerial $record): void
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

    public function deleted(ItemSerial $record): void
    {
        //
    }

    /*
    |--------------------------------------------------------------------------
    | Restoring
    |--------------------------------------------------------------------------
    */

    public function restoring(ItemSerial $record): void
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

    public function restored(ItemSerial $record): void
    {
        //
    }

    /*
    |--------------------------------------------------------------------------
    | Force Deleting
    |--------------------------------------------------------------------------
    */

    public function forceDeleting(ItemSerial $record): void
    {
        //
    }

    /*
    |--------------------------------------------------------------------------
    | Force Deleted
    |--------------------------------------------------------------------------
    */

    public function forceDeleted(ItemSerial $record): void
    {
        //
    }
}