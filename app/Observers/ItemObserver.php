<?php

declare(strict_types=1);

namespace App\Observers;


use App\Services\Item\ItemImageSynchronizationService;
use App\Models\Item;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

class ItemObserver
{


    /*
    |--------------------------------------------------------------------------
    | Creating
    |--------------------------------------------------------------------------
    */

    public function creating(Item $record): void
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

    public function created(Item $record): void
    {
        app(ItemImageSynchronizationService::class)
            ->synchronize($record);

        /*
        |--------------------------------------------------------------------------
        | Future
        |--------------------------------------------------------------------------
        |
        | - Activity Log
        | - Notification
        | - Default Warehouse Stock
        |
        */
    }

    /*
    |--------------------------------------------------------------------------
    | Updating
    |--------------------------------------------------------------------------
    */

    public function updating(Item $record): void
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

    public function updated(Item $record): void
    {
        /*
        |--------------------------------------------------------------------------
        | Synchronize Primary Image
        |--------------------------------------------------------------------------
        */

        if ($record->wasChanged('image')) {
            app(ItemImageSynchronizationService::class)
                ->synchronize($record);
        }

        /*
        |--------------------------------------------------------------------------
        | Future
        |--------------------------------------------------------------------------
        |
        | - Activity Log
        | - Notification
        |
        */
    }

    /*
    |--------------------------------------------------------------------------
    | Deleting
    |--------------------------------------------------------------------------
    */

    public function deleting(Item $record): void
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

    public function restoring(Item $record): void
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

    public function restored(Item $record): void
    {
        //
    }

    /*
    |--------------------------------------------------------------------------
    | Force Deleting
    |--------------------------------------------------------------------------
    */

    public function forceDeleting(Item $record): void
    {
        //
    }

    /*
    |--------------------------------------------------------------------------
    | Force Deleted
    |--------------------------------------------------------------------------
    */

    public function forceDeleted(Item $record): void
    {
        //
    }


    /*
    |--------------------------------------------------------------------------
    | Private Helper
    |--------------------------------------------------------------------------
    */

    private function fillAuditInformation(Item $record): void
    {
        //
    }



}