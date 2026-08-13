<?php

declare(strict_types=1);

namespace App\Observers;

use App\Models\ItemBarcode;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

class ItemBarcodeObserver
{
    /*
    |--------------------------------------------------------------------------
    | Creating
    |--------------------------------------------------------------------------
    */

    public function creating(ItemBarcode $record): void
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

    public function created(ItemBarcode $record): void
    {
        //
        // Future:
        // Generate Barcode Image
        // Generate QR Code
        // Activity Log
        //
    }

    /*
    |--------------------------------------------------------------------------
    | Updating
    |--------------------------------------------------------------------------
    */

    public function updating(ItemBarcode $record): void
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

    public function updated(ItemBarcode $record): void
    {
        //
    }

    /*
    |--------------------------------------------------------------------------
    | Deleting
    |--------------------------------------------------------------------------
    */

    public function deleting(ItemBarcode $record): void
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

    public function deleted(ItemBarcode $record): void
    {
        //
    }

    /*
    |--------------------------------------------------------------------------
    | Restoring
    |--------------------------------------------------------------------------
    */

    public function restoring(ItemBarcode $record): void
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

    public function restored(ItemBarcode $record): void
    {
        //
    }

    /*
    |--------------------------------------------------------------------------
    | Force Deleting
    |--------------------------------------------------------------------------
    */

    public function forceDeleting(ItemBarcode $record): void
    {
        //
    }

    /*
    |--------------------------------------------------------------------------
    | Force Deleted
    |--------------------------------------------------------------------------
    */

    public function forceDeleted(ItemBarcode $record): void
    {
        //
    }
}