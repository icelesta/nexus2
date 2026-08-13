<?php

declare(strict_types=1);

namespace App\Observers;

use App\Models\Section;
use Illuminate\Support\Str;

class SectionObserver
{
    /**
     * Handle the Section "creating" event.
     */
    public function creating(Section $section): void
    {
        /*
        |--------------------------------------------------------------------------
        | UUID
        |--------------------------------------------------------------------------
        */

        if (! filled($section->uuid)) {
            $section->uuid = (string) Str::uuid();
        }

        /*
        |--------------------------------------------------------------------------
        | Audit Trail
        |--------------------------------------------------------------------------
        */

        $userId = auth()->id();

        if ($userId) {
            $section->created_by ??= $userId;
            $section->updated_by ??= $userId;
        }

        /*
        |--------------------------------------------------------------------------
        | Default Value
        |--------------------------------------------------------------------------
        */

        $section->is_active ??= true;
    }

    /**
     * Handle the Section "updating" event.
     */
    public function updating(Section $section): void
    {
        /*
        |--------------------------------------------------------------------------
        | Audit Trail
        |--------------------------------------------------------------------------
        */

        if ($userId = auth()->id()) {
            $section->updated_by = $userId;
        }
    }

    /**
     * Handle the Section "created" event.
     */
    public function created(Section $section): void
    {
        //
    }

    /**
     * Handle the Section "updated" event.
     */
    public function updated(Section $section): void
    {
        //
    }

    /**
     * Handle the Section "deleted" event.
     */
    public function deleted(Section $section): void
    {
        //
    }

    /**
     * Handle the Section "restored" event.
     */
    public function restored(Section $section): void
    {
        //
    }

    /**
     * Handle the Section "force deleted" event.
     */
    public function forceDeleted(Section $section): void
    {
        //
    }
}