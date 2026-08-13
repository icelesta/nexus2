<?php

declare(strict_types=1);

namespace App\Observers;

use App\Models\FiscalYear;
use Illuminate\Support\Str;

class FiscalYearObserver
{
    /**
     * Handle the FiscalYear "creating" event.
     */
    public function creating(FiscalYear $fiscalYear): void
    {
        /*
        |--------------------------------------------------------------------------
        | UUID
        |--------------------------------------------------------------------------
        */

        if (! filled($fiscalYear->uuid)) {
            $fiscalYear->uuid = (string) Str::uuid();
        }

        /*
        |--------------------------------------------------------------------------
        | Audit Trail
        |--------------------------------------------------------------------------
        */

        $userId = auth()->id();

        if ($userId) {
            $fiscalYear->created_by ??= $userId;
            $fiscalYear->updated_by ??= $userId;
        }

        /*
        |--------------------------------------------------------------------------
        | Default Value
        |--------------------------------------------------------------------------
        */

        $fiscalYear->is_closed ??= false;
        $fiscalYear->is_active ??= true;
    }

    /**
     * Handle the FiscalYear "updating" event.
     */
    public function updating(FiscalYear $fiscalYear): void
    {
        /*
        |--------------------------------------------------------------------------
        | Audit Trail
        |--------------------------------------------------------------------------
        */

        if ($userId = auth()->id()) {
            $fiscalYear->updated_by = $userId;
        }
    }

    /**
     * Handle the FiscalYear "created" event.
     */
    public function created(FiscalYear $fiscalYear): void
    {
        //
    }

    /**
     * Handle the FiscalYear "updated" event.
     */
    public function updated(FiscalYear $fiscalYear): void
    {
        //
    }

    /**
     * Handle the FiscalYear "deleted" event.
     */
    public function deleted(FiscalYear $fiscalYear): void
    {
        //
    }

    /**
     * Handle the FiscalYear "restored" event.
     */
    public function restored(FiscalYear $fiscalYear): void
    {
        //
    }

    /**
     * Handle the FiscalYear "force deleted" event.
     */
    public function forceDeleted(FiscalYear $fiscalYear): void
    {
        //
    }
}