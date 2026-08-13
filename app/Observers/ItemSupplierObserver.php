<?php

declare(strict_types=1);

namespace App\Observers;

use App\Models\ItemSupplier;
use Illuminate\Support\Str;

class ItemSupplierObserver
{
    /**
     * Handle the ItemSupplier "creating" event.
     */
    public function creating(ItemSupplier $record): void
    {
        /*
        |--------------------------------------------------------------------------
        | UUID
        |--------------------------------------------------------------------------
        */

        if (! filled($record->uuid)) {
            $record->uuid = (string) Str::uuid();
        }

        /*
        |--------------------------------------------------------------------------
        | Audit Trail
        |--------------------------------------------------------------------------
        */

        $userId = auth()->id();

        if ($userId) {
            $record->created_by ??= $userId;
            $record->updated_by ??= $userId;
        }

        /*
        |--------------------------------------------------------------------------
        | Default Values
        |--------------------------------------------------------------------------
        */

        $record->supplier_priority ??= 1;

        $record->minimum_order_qty ??= 1;

        $record->purchase_multiple ??= 1;

        $record->default_discount ??= 0;

        $record->lead_time_days ??= 0;

        $record->is_preferred ??= false;

        $record->is_active ??= true;
    }

    /**
     * Handle the ItemSupplier "updating" event.
     */
    public function updating(ItemSupplier $record): void
    {
        if ($userId = auth()->id()) {
            $record->updated_by = $userId;
        }
    }

    /**
     * Handle the ItemSupplier "created" event.
     */
    public function created(ItemSupplier $record): void
    {
        //
    }

    /**
     * Handle the ItemSupplier "updated" event.
     */
    public function updated(ItemSupplier $record): void
    {
        //
    }

    /**
     * Handle the ItemSupplier "deleted" event.
     */
    public function deleted(ItemSupplier $record): void
    {
        //
    }

    /**
     * Handle the ItemSupplier "restored" event.
     */
    public function restored(ItemSupplier $record): void
    {
        //
    }

    /**
     * Handle the ItemSupplier "force deleted" event.
     */
    public function forceDeleted(ItemSupplier $record): void
    {
        //
    }
}