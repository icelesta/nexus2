<?php

declare(strict_types=1);

namespace App\Observers;

use App\Models\JournalType;
use Illuminate\Support\Str;

class JournalTypeObserver
{
    /**
     * Handle the JournalType "creating" event.
     */
    public function creating(JournalType $journalType): void
    {
        /*
        |--------------------------------------------------------------------------
        | UUID
        |--------------------------------------------------------------------------
        */

        if (! filled($journalType->uuid)) {
            $journalType->uuid = (string) Str::uuid();
        }

        /*
        |--------------------------------------------------------------------------
        | Audit Trail
        |--------------------------------------------------------------------------
        */

        $userId = auth()->id();

        if ($userId) {
            $journalType->created_by ??= $userId;
            $journalType->updated_by ??= $userId;
        }

        /*
        |--------------------------------------------------------------------------
        | Default Value
        |--------------------------------------------------------------------------
        */

        $journalType->is_active ??= true;
        $journalType->is_system ??= false;
    }

    /**
     * Handle the JournalType "updating" event.
     */
    public function updating(JournalType $journalType): void
    {
        /*
        |--------------------------------------------------------------------------
        | Audit Trail
        |--------------------------------------------------------------------------
        */

        if ($userId = auth()->id()) {
            $journalType->updated_by = $userId;
        }
    }

    /**
     * Handle the JournalType "deleting" event.
     */
    public function deleting(JournalType $journalType): void
    {
        /*
        |--------------------------------------------------------------------------
        | Soft Delete Audit
        |--------------------------------------------------------------------------
        */

        if (
            ($userId = auth()->id()) &&
            ! $journalType->isForceDeleting()
        ) {
            $journalType->deleted_by = $userId;

            $journalType->saveQuietly();
        }
    }

    /**
     * Handle the JournalType "restoring" event.
     */
    public function restoring(JournalType $journalType): void
    {
        /*
        |--------------------------------------------------------------------------
        | Restore Audit
        |--------------------------------------------------------------------------
        */

        $journalType->deleted_by = null;
    }

    /**
     * Handle the JournalType "created" event.
     */
    public function created(JournalType $journalType): void
    {
        //
    }

    /**
     * Handle the JournalType "updated" event.
     */
    public function updated(JournalType $journalType): void
    {
        //
    }

    /**
     * Handle the JournalType "deleted" event.
     */
    public function deleted(JournalType $journalType): void
    {
        //
    }

    /**
     * Handle the JournalType "restored" event.
     */
    public function restored(JournalType $journalType): void
    {
        //
    }

    /**
     * Handle the JournalType "force deleted" event.
     */
    public function forceDeleted(JournalType $journalType): void
    {
        //
    }
}