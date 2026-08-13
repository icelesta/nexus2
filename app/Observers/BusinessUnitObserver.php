<?php

declare(strict_types=1);

namespace App\Observers;

use App\Models\BusinessUnit;
use Illuminate\Support\Str;

class BusinessUnitObserver
{
    /**
     * Handle the BusinessUnit "creating" event.
     */
    public function creating(BusinessUnit $businessUnit): void
    {
        /*
        |--------------------------------------------------------------------------
        | UUID
        |--------------------------------------------------------------------------
        */

        if (! filled($businessUnit->uuid)) {
            $businessUnit->uuid = (string) Str::uuid();
        }

        /*
        |--------------------------------------------------------------------------
        | Audit Trail
        |--------------------------------------------------------------------------
        */

        $userId = auth()->id();

        if ($userId) {
            $businessUnit->created_by ??= $userId;
            $businessUnit->updated_by ??= $userId;
        }

        /*
        |--------------------------------------------------------------------------
        | Default Value
        |--------------------------------------------------------------------------
        */

        $businessUnit->is_active ??= true;
        $businessUnit->is_default ??= false;
        $businessUnit->sort_order ??= 0;
    }

    /**
     * Handle the BusinessUnit "updating" event.
     */
    public function updating(BusinessUnit $businessUnit): void
    {
        /*
        |--------------------------------------------------------------------------
        | Audit Trail
        |--------------------------------------------------------------------------
        */

        if ($userId = auth()->id()) {
            $businessUnit->updated_by = $userId;
        }
    }

    /**
     * Handle the BusinessUnit "created" event.
     */
    public function created(BusinessUnit $businessUnit): void
    {
        //
    }

    /**
     * Handle the BusinessUnit "updated" event.
     */
    public function updated(BusinessUnit $businessUnit): void
    {
        //
    }

    /**
     * Handle the BusinessUnit "deleting" event.
     */
    public function deleting(BusinessUnit $businessUnit): void
    {
        //
    }

    /**
     * Handle the BusinessUnit "deleted" event.
     */
    public function deleted(BusinessUnit $businessUnit): void
    {
        //
    }

    /**
     * Handle the BusinessUnit "restoring" event.
     */
    public function restoring(BusinessUnit $businessUnit): void
    {
        //
    }

    /**
     * Handle the BusinessUnit "restored" event.
     */
    public function restored(BusinessUnit $businessUnit): void
    {
        //
    }

    /**
     * Handle the BusinessUnit "force deleted" event.
     */
    public function forceDeleted(BusinessUnit $businessUnit): void
    {
        //
    }
}