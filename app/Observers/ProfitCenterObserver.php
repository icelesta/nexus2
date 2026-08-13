<?php

declare(strict_types=1);

namespace App\Observers;

use App\Models\ProfitCenter;
use Illuminate\Support\Str;

class ProfitCenterObserver
{
    /**
     * Handle the ProfitCenter "creating" event.
     */
    public function creating(ProfitCenter $profitCenter): void
    {
        /*
        |--------------------------------------------------------------------------
        | UUID
        |--------------------------------------------------------------------------
        */

        if (! filled($profitCenter->uuid)) {
            $profitCenter->uuid = (string) Str::uuid();
        }

        /*
        |--------------------------------------------------------------------------
        | Audit Trail
        |--------------------------------------------------------------------------
        */

        $userId = auth()->id();

        if ($userId) {
            $profitCenter->created_by ??= $userId;
            $profitCenter->updated_by ??= $userId;
        }

        /*
        |--------------------------------------------------------------------------
        | Default Value
        |--------------------------------------------------------------------------
        */

        $profitCenter->is_active ??= true;
    }

    /**
     * Handle the ProfitCenter "updating" event.
     */
    public function updating(ProfitCenter $profitCenter): void
    {
        /*
        |--------------------------------------------------------------------------
        | Audit Trail
        |--------------------------------------------------------------------------
        */

        if ($userId = auth()->id()) {
            $profitCenter->updated_by = $userId;
        }
    }

    /**
     * Handle the ProfitCenter "created" event.
     */
    public function created(ProfitCenter $profitCenter): void
    {
        //
    }

    /**
     * Handle the ProfitCenter "updated" event.
     */
    public function updated(ProfitCenter $profitCenter): void
    {
        //
    }

    /**
     * Handle the ProfitCenter "deleted" event.
     */
    public function deleted(ProfitCenter $profitCenter): void
    {
        //
    }

    /**
     * Handle the ProfitCenter "restored" event.
     */
    public function restored(ProfitCenter $profitCenter): void
    {
        //
    }

    /**
     * Handle the ProfitCenter "force deleted" event.
     */
    public function forceDeleted(ProfitCenter $profitCenter): void
    {
        //
    }
}