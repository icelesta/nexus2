<?php

declare(strict_types=1);

namespace App\Observers;

use App\Models\TaxMaster;
use Illuminate\Support\Str;

class TaxMasterObserver
{
    /**
     * Handle the TaxMaster "creating" event.
     */
    public function creating(TaxMaster $taxMaster): void
    {
        /*
        |--------------------------------------------------------------------------
        | UUID
        |--------------------------------------------------------------------------
        */

        if (! filled($taxMaster->uuid)) {
            $taxMaster->uuid = (string) Str::uuid();
        }

        /*
        |--------------------------------------------------------------------------
        | Audit Trail
        |--------------------------------------------------------------------------
        */

        $userId = auth()->id();

        if ($userId) {
            $taxMaster->created_by ??= $userId;
            $taxMaster->updated_by ??= $userId;
        }

        /*
        |--------------------------------------------------------------------------
        | Default Value
        |--------------------------------------------------------------------------
        */

        $taxMaster->tax_rate ??= 0;

        $taxMaster->is_inclusive ??= false;
        $taxMaster->is_active ??= true;
    }

    /**
     * Handle the TaxMaster "updating" event.
     */
    public function updating(TaxMaster $taxMaster): void
    {
        /*
        |--------------------------------------------------------------------------
        | Audit Trail
        |--------------------------------------------------------------------------
        */

        if ($userId = auth()->id()) {
            $taxMaster->updated_by = $userId;
        }
    }

    /**
     * Handle the TaxMaster "created" event.
     */
    public function created(TaxMaster $taxMaster): void
    {
        //
    }

    /**
     * Handle the TaxMaster "updated" event.
     */
    public function updated(TaxMaster $taxMaster): void
    {
        //
    }

    /**
     * Handle the TaxMaster "deleted" event.
     */
    public function deleted(TaxMaster $taxMaster): void
    {
        //
    }

    /**
     * Handle the TaxMaster "restored" event.
     */
    public function restored(TaxMaster $taxMaster): void
    {
        //
    }

    /**
     * Handle the TaxMaster "force deleted" event.
     */
    public function forceDeleted(TaxMaster $taxMaster): void
    {
        //
    }
}