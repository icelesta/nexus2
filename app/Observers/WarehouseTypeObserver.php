<?php

declare(strict_types=1);

namespace App\Observers;

use App\Models\WarehouseType;

class WarehouseTypeObserver
{
    /**
     * Handle the WarehouseType "creating" event.
     */
    public function creating(WarehouseType $warehouseType): void
    {
        /*
        |--------------------------------------------------------------------------
        | Audit Trail
        |--------------------------------------------------------------------------
        */

        $userId = auth()->id();

        if ($userId) {
            $warehouseType->created_by ??= $userId;
            $warehouseType->updated_by ??= $userId;
        }

        /*
        |--------------------------------------------------------------------------
        | Default Value
        |--------------------------------------------------------------------------
        */

        $warehouseType->allow_receipt ??= false;
        $warehouseType->allow_issue ??= false;
        $warehouseType->allow_transfer ??= false;
        $warehouseType->allow_production ??= false;
        $warehouseType->allow_sales ??= false;
        $warehouseType->allow_adjustment ??= false;

        $warehouseType->sort_order ??= 0;

        $warehouseType->is_default ??= false;
        $warehouseType->is_active ??= true;
    }

    /**
     * Handle the WarehouseType "updating" event.
     */
    public function updating(WarehouseType $warehouseType): void
    {
        /*
        |--------------------------------------------------------------------------
        | Audit Trail
        |--------------------------------------------------------------------------
        */

        if ($userId = auth()->id()) {
            $warehouseType->updated_by = $userId;
        }
    }

    /**
     * Handle the WarehouseType "created" event.
     */
    public function created(WarehouseType $warehouseType): void
    {
        //
    }

    /**
     * Handle the WarehouseType "updated" event.
     */
    public function updated(WarehouseType $warehouseType): void
    {
        //
    }

    /**
     * Handle the WarehouseType "deleted" event.
     */
    public function deleted(WarehouseType $warehouseType): void
    {
        //
    }

    /**
     * Handle the WarehouseType "restored" event.
     */
    public function restored(WarehouseType $warehouseType): void
    {
        //
    }

    /**
     * Handle the WarehouseType "force deleted" event.
     */
    public function forceDeleted(WarehouseType $warehouseType): void
    {
        //
    }
}