<?php

declare(strict_types=1);

namespace App\Observers;

use App\Models\CostCenter;
use Illuminate\Support\Str;

class CostCenterObserver
{
    /**
     * Handle the CostCenter "creating" event.
     */
    public function creating(CostCenter $costCenter): void
    {
        /*
        |--------------------------------------------------------------------------
        | UUID
        |--------------------------------------------------------------------------
        */

        if (! filled($costCenter->uuid)) {
            $costCenter->uuid = (string) Str::uuid();
        }

        /*
        |--------------------------------------------------------------------------
        | Audit Trail
        |--------------------------------------------------------------------------
        */

        $userId = auth()->id();

        if ($userId) {
            $costCenter->created_by ??= $userId;
            $costCenter->updated_by ??= $userId;
        }

        /*
        |--------------------------------------------------------------------------
        | Default Value
        |--------------------------------------------------------------------------
        */

        $costCenter->is_active ??= true;
    }

    /**
     * Handle the CostCenter "updating" event.
     */
    public function updating(CostCenter $costCenter): void
    {
        /*
        |--------------------------------------------------------------------------
        | Audit Trail
        |--------------------------------------------------------------------------
        */

        if ($userId = auth()->id()) {
            $costCenter->updated_by = $userId;
        }
    }

    /**
     * Handle the CostCenter "created" event.
     */
    public function created(CostCenter $costCenter): void
    {
        //
    }

    /**
     * Handle the CostCenter "updated" event.
     */
    public function updated(CostCenter $costCenter): void
    {
        //
    }

    /**
     * Handle the CostCenter "deleted" event.
     */
    public function deleted(CostCenter $costCenter): void
    {
        //
    }

    /**
     * Handle the CostCenter "restored" event.
     */
    public function restored(CostCenter $costCenter): void
    {
        //
    }

    /**
     * Handle the CostCenter "force deleted" event.
     */
    public function forceDeleted(CostCenter $costCenter): void
    {
        //
    }
}