<?php

declare(strict_types=1);

namespace App\Observers;

use App\Models\Warehouse;

class WarehouseObserver
{
    /**
     * Handle the Warehouse "creating" event.
     */
    public function creating(Warehouse $warehouse): void
    {
        /*
        |--------------------------------------------------------------------------
        | Audit Trail
        |--------------------------------------------------------------------------
        */

        $userId = auth()->id();

        if ($userId) {
            $warehouse->created_by ??= $userId;
            $warehouse->updated_by ??= $userId;
        }

        /*
        |--------------------------------------------------------------------------
        | Default Value
        |--------------------------------------------------------------------------
        */

        $warehouse->is_active ??= true;
        $warehouse->is_default ??= false;
        $warehouse->sort_order ??= 0;

        $warehouse->allow_purchase ??= false;
        $warehouse->allow_sales ??= false;
        $warehouse->allow_transfer ??= false;
        $warehouse->allow_production ??= false;
        $warehouse->allow_negative_stock ??= false;
    }

    /**
     * Handle the Warehouse "updating" event.
     */
    public function updating(Warehouse $warehouse): void
    {
        /*
        |--------------------------------------------------------------------------
        | Audit Trail
        |--------------------------------------------------------------------------
        */

        if ($userId = auth()->id()) {
            $warehouse->updated_by = $userId;
        }
    }

    /**
     * Handle the Warehouse "created" event.
     */
    public function created(Warehouse $warehouse): void
    {
        //
    }

    /**
     * Handle the Warehouse "updated" event.
     */
    public function updated(Warehouse $warehouse): void
    {
        //
    }

    /**
     * Handle the Warehouse "deleted" event.
     */
    public function deleted(Warehouse $warehouse): void
    {
        //
    }

    /**
     * Handle the Warehouse "restored" event.
     */
    public function restored(Warehouse $warehouse): void
    {
        //
    }

    /**
     * Handle the Warehouse "force deleted" event.
     */
    public function forceDeleted(Warehouse $warehouse): void
    {
        //
    }
}