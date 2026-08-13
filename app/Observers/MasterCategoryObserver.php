<?php

declare(strict_types=1);

namespace App\Observers;

use App\Models\MasterCategory;
use Illuminate\Support\Str;

class MasterCategoryObserver
{
    /**
     * Handle the MasterCategory "creating" event.
     */
    public function creating(MasterCategory $masterCategory): void
    {
        /*
        |--------------------------------------------------------------------------
        | UUID
        |--------------------------------------------------------------------------
        */

        if (! filled($masterCategory->uuid)) {
            $masterCategory->uuid = (string) Str::uuid();
        }

        /*
        |--------------------------------------------------------------------------
        | Audit Trail
        |--------------------------------------------------------------------------
        */

        $userId = auth()->id();

        if ($userId) {
            $masterCategory->created_by ??= $userId;
            $masterCategory->updated_by ??= $userId;
        }

        /*
        |--------------------------------------------------------------------------
        | Default Value
        |--------------------------------------------------------------------------
        */

        $masterCategory->is_active ??= true;

        /*
        |--------------------------------------------------------------------------
        | Auto Level
        |--------------------------------------------------------------------------
        */

        if (! filled($masterCategory->level)) {
            $masterCategory->level = $masterCategory->parent_id
                ? 2
                : 1;
        }
    }

    /**
     * Handle the MasterCategory "updating" event.
     */
    public function updating(MasterCategory $masterCategory): void
    {
        /*
        |--------------------------------------------------------------------------
        | Audit Trail
        |--------------------------------------------------------------------------
        */

        if ($userId = auth()->id()) {
            $masterCategory->updated_by = $userId;
        }

        /*
        |--------------------------------------------------------------------------
        | Auto Level
        |--------------------------------------------------------------------------
        */

        if ($masterCategory->isDirty('parent_id')) {
            $masterCategory->level = $masterCategory->parent_id
                ? 2
                : 1;
        }
    }

    /**
     * Handle the MasterCategory "created" event.
     */
    public function created(MasterCategory $masterCategory): void
    {
        //
    }

    /**
     * Handle the MasterCategory "updated" event.
     */
    public function updated(MasterCategory $masterCategory): void
    {
        //
    }

    /**
     * Handle the MasterCategory "deleted" event.
     */
    public function deleted(MasterCategory $masterCategory): void
    {
        //
    }

    /**
     * Handle the MasterCategory "restored" event.
     */
    public function restored(MasterCategory $masterCategory): void
    {
        //
    }

    /**
     * Handle the MasterCategory "force deleted" event.
     */
    public function forceDeleted(MasterCategory $masterCategory): void
    {
        //
    }
}