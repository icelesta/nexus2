<?php

declare(strict_types=1);

namespace App\Observers;

use App\Models\Branch;
use Illuminate\Support\Str;

class BranchObserver
{
    /**
     * Handle the Branch "creating" event.
     */
    public function creating(Branch $branch): void
    {
        /*
        |--------------------------------------------------------------------------
        | UUID
        |--------------------------------------------------------------------------
        */

        if (! filled($branch->uuid)) {
            $branch->uuid = (string) Str::uuid();
        }

        /*
        |--------------------------------------------------------------------------
        | Audit Trail
        |--------------------------------------------------------------------------
        */

        $userId = auth()->id();

        if ($userId) {
            $branch->created_by ??= $userId;
            $branch->updated_by ??= $userId;
        }
    }

    /**
     * Handle the Branch "updating" event.
     */
    public function updating(Branch $branch): void
    {
        /*
        |--------------------------------------------------------------------------
        | Audit Trail
        |--------------------------------------------------------------------------
        */

        if ($userId = auth()->id()) {
            $branch->updated_by = $userId;
        }
    }

    /**
     * Handle the Branch "created" event.
     */
    public function created(Branch $branch): void
    {
        //
    }

    /**
     * Handle the Branch "updated" event.
     */
    public function updated(Branch $branch): void
    {
        //
    }

    /**
     * Handle the Branch "deleted" event.
     */
    public function deleted(Branch $branch): void
    {
        //
    }

    /**
     * Handle the Branch "restored" event.
     */
    public function restored(Branch $branch): void
    {
        //
    }

    /**
     * Handle the Branch "force deleted" event.
     */
    public function forceDeleted(Branch $branch): void
    {
        //
    }
}