<?php

declare(strict_types=1);

namespace App\Observers;

use App\Models\BankAccount;
use Illuminate\Support\Str;

class BankAccountObserver
{
    /**
     * Handle the BankAccount "creating" event.
     */
    public function creating(BankAccount $bankAccount): void
    {
        /*
        |--------------------------------------------------------------------------
        | UUID
        |--------------------------------------------------------------------------
        */

        if (! filled($bankAccount->uuid)) {
            $bankAccount->uuid = (string) Str::uuid();
        }

        /*
        |--------------------------------------------------------------------------
        | Audit Trail
        |--------------------------------------------------------------------------
        */

        $userId = auth()->id();

        if ($userId) {
            $bankAccount->created_by ??= $userId;
            $bankAccount->updated_by ??= $userId;
        }
    }

    /**
     * Handle the BankAccount "updating" event.
     */
    public function updating(BankAccount $bankAccount): void
    {
        /*
        |--------------------------------------------------------------------------
        | Audit Trail
        |--------------------------------------------------------------------------
        */

        if ($userId = auth()->id()) {
            $bankAccount->updated_by = $userId;
        }
    }

    /**
     * Handle the BankAccount "created" event.
     */
    public function created(BankAccount $bankAccount): void
    {
        //
    }

    /**
     * Handle the BankAccount "updated" event.
     */
    public function updated(BankAccount $bankAccount): void
    {
        //
    }

    /**
     * Handle the BankAccount "deleted" event.
     */
    public function deleted(BankAccount $bankAccount): void
    {
        //
    }

    /**
     * Handle the BankAccount "restored" event.
     */
    public function restored(BankAccount $bankAccount): void
    {
        //
    }

    /**
     * Handle the BankAccount "force deleted" event.
     */
    public function forceDeleted(BankAccount $bankAccount): void
    {
        //
    }
}