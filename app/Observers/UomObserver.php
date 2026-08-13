<?php

declare(strict_types=1);

namespace App\Observers;

use App\Models\Uom;
use Illuminate\Support\Str;

class UomObserver
{
    /**
     * Handle the Uom "creating" event.
     */
    public function creating(Uom $uom): void
    {
        /*
        |--------------------------------------------------------------------------
        | UUID
        |--------------------------------------------------------------------------
        */

        if (! filled($uom->uuid)) {
            $uom->uuid = (string) Str::uuid();
        }

        /*
        |--------------------------------------------------------------------------
        | Audit Trail
        |--------------------------------------------------------------------------
        */

        $userId = auth()->id();

        if ($userId) {
            $uom->created_by ??= $userId;
            $uom->updated_by ??= $userId;
        }

        /*
        |--------------------------------------------------------------------------
        | Default Values
        |--------------------------------------------------------------------------
        */

        $uom->category ??= 'Quantity';

        $uom->decimal_places ??= 0;

        $uom->allow_fraction ??= false;

        $uom->is_base ??= false;

        $uom->sort_order ??= 0;

        $uom->is_active ??= true;
    }

    /**
     * Handle the Uom "updating" event.
     */
    public function updating(Uom $uom): void
    {
        /*
        |--------------------------------------------------------------------------
        | Audit Trail
        |--------------------------------------------------------------------------
        */

        if ($userId = auth()->id()) {
            $uom->updated_by = $userId;
        }
    }

    /**
     * Handle the Uom "created" event.
     */
    public function created(Uom $uom): void
    {
        //
    }

    /**
     * Handle the Uom "updated" event.
     */
    public function updated(Uom $uom): void
    {
        //
    }

    /**
     * Handle the Uom "deleted" event.
     */
    public function deleted(Uom $uom): void
    {
        //
    }

    /**
     * Handle the Uom "restored" event.
     */
    public function restored(Uom $uom): void
    {
        //
    }

    /**
     * Handle the Uom "force deleted" event.
     */
    public function forceDeleted(Uom $uom): void
    {
        //
    }
}
