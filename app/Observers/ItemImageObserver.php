<?php

declare(strict_types=1);

namespace App\Observers;

use App\Models\ItemImage;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

class ItemImageObserver
{
    /*
    |--------------------------------------------------------------------------
    | Creating
    |--------------------------------------------------------------------------
    */

    public function creating(ItemImage $record): void
    {
        /*
        |--------------------------------------------------------------------------
        | UUID
        |--------------------------------------------------------------------------
        */

        if (blank($record->uuid)) {
            $record->uuid = (string) Str::orderedUuid();
        }

        /*
        |--------------------------------------------------------------------------
        | Audit Trail
        |--------------------------------------------------------------------------
        */

        if (Auth::check()) {
            $record->forceFill([
                'created_by' => Auth::id(),
                'updated_by' => Auth::id(),
            ]);
        }
    }


    /*
    |--------------------------------------------------------------------------
    | Created
    |--------------------------------------------------------------------------
    */

    public function created(ItemImage $record): void
    {
        //
        // Future:
        // Generate Thumbnail
        // Optimize Image
        // Generate WebP
        // Activity Log
        //
    }

    /*
    |--------------------------------------------------------------------------
    | Updating
    |--------------------------------------------------------------------------
    */

    public function updating(ItemImage $record): void
    {
        if (Auth::check()) {
            $record->forceFill([
                'updated_by' => Auth::id(),
            ]);
        }
    }

    /*
    |--------------------------------------------------------------------------
    | Updated
    |--------------------------------------------------------------------------
    */

    public function updated(ItemImage $record): void
    {
        //
    }

    /*
    |--------------------------------------------------------------------------
    | Deleting
    |--------------------------------------------------------------------------
    */

    public function deleting(ItemImage $record): void
    {
        if (Auth::check()) {
            $record->forceFill([
                'deleted_by' => Auth::id(),
            ])->saveQuietly();
        }
    }

    /*
    |--------------------------------------------------------------------------
    | Restoring
    |--------------------------------------------------------------------------
    */

    public function restoring(ItemImage $record): void
    {
        $record->forceFill([
            'deleted_by' => null,
        ])->saveQuietly();
    }

    /*
    |--------------------------------------------------------------------------
    | Restored
    |--------------------------------------------------------------------------
    */

    public function restored(ItemImage $record): void
    {
        //
    }

    /*
    |--------------------------------------------------------------------------
    | Force Deleting
    |--------------------------------------------------------------------------
    */

    public function forceDeleting(ItemImage $record): void
    {
        //
    }

    /*
    |--------------------------------------------------------------------------
    | Force Deleted
    |--------------------------------------------------------------------------
    */

    public function deleted(ItemImage $record): void
    {
        if (! $record->is_primary) {
            return;
        }

        $next = ItemImage::query()

            ->where('item_id', $record->item_id)

            ->oldest('sort_order')

            ->first();

        if ($next) {

            $next->update([
                'is_primary' => true,
            ]);
        }
    }


    /*
    |--------------------------------------------------------------------------
    | Business Rules
    |--------------------------------------------------------------------------
    */

    public function saving(ItemImage $record): void
    {
        if (! $record->is_primary) {
            return;
        }

        if (! $record->isDirty('is_primary')) {
            return;
        }

        ItemImage::query()
            ->where('item_id', $record->item_id)
            ->whereKeyNot($record->getKey())
            ->update([
                'is_primary' => false,
            ]);
    }

    

}