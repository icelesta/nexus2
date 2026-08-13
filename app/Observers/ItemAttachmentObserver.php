<?php

declare(strict_types=1);

namespace App\Observers;

use App\Models\ItemAttachment;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

class ItemAttachmentObserver
{
    /*
    |--------------------------------------------------------------------------
    | Creating
    |--------------------------------------------------------------------------
    */

    public function creating(ItemAttachment $record): void
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

    public function created(ItemAttachment $record): void
    {
        //
        // Future:
        // Generate Preview
        // Generate Thumbnail
        // OCR Index
        // Activity Log
        //
    }

    /*
    |--------------------------------------------------------------------------
    | Updating
    |--------------------------------------------------------------------------
    */

    public function updating(ItemAttachment $record): void
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

    public function updated(ItemAttachment $record): void
    {
        //
    }

    /*
    |--------------------------------------------------------------------------
    | Deleting
    |--------------------------------------------------------------------------
    */

    public function deleting(ItemAttachment $record): void
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

    public function deleted(ItemAttachment $record): void
    {
        //
    }

    /*
    |--------------------------------------------------------------------------
    | Restoring
    |--------------------------------------------------------------------------
    */

    public function restoring(ItemAttachment $record): void
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

    public function restored(ItemAttachment $record): void
    {
        //
    }

    /*
    |--------------------------------------------------------------------------
    | Force Deleting
    |--------------------------------------------------------------------------
    */

    public function forceDeleting(ItemAttachment $record): void
    {
        //
    }

    /*
    |--------------------------------------------------------------------------
    | Force Deleted
    |--------------------------------------------------------------------------
    */

    public function forceDeleted(ItemAttachment $record): void
    {
        //
    }
}