<?php

declare(strict_types=1);

namespace App\Observers;

use App\Models\PaymentTerm;
use Illuminate\Support\Str;

class PaymentTermObserver
{
    /**
     * Handle the PaymentTerm "creating" event.
     */
    public function creating(PaymentTerm $paymentTerm): void
    {
        /*
        |--------------------------------------------------------------------------
        | UUID
        |--------------------------------------------------------------------------
        */

        if (! filled($paymentTerm->uuid)) {
            $paymentTerm->uuid = (string) Str::uuid();
        }

        /*
        |--------------------------------------------------------------------------
        | Audit Trail
        |--------------------------------------------------------------------------
        */

        $userId = auth()->id();

        if ($userId) {
            $paymentTerm->created_by ??= $userId;
            $paymentTerm->updated_by ??= $userId;
        }

        /*
        |--------------------------------------------------------------------------
        | Default Value
        |--------------------------------------------------------------------------
        */

        $paymentTerm->due_days ??= 0;
        $paymentTerm->grace_days ??= 0;

        $paymentTerm->discount_days ??= 0;
        $paymentTerm->discount_percent ??= 0;

        $paymentTerm->is_active ??= true;
    }

    /**
     * Handle the PaymentTerm "updating" event.
     */
    public function updating(PaymentTerm $paymentTerm): void
    {
        /*
        |--------------------------------------------------------------------------
        | Audit Trail
        |--------------------------------------------------------------------------
        */

        if ($userId = auth()->id()) {
            $paymentTerm->updated_by = $userId;
        }
    }

    /**
     * Handle the PaymentTerm "created" event.
     */
    public function created(PaymentTerm $paymentTerm): void
    {
        //
    }

    /**
     * Handle the PaymentTerm "updated" event.
     */
    public function updated(PaymentTerm $paymentTerm): void
    {
        //
    }

    /**
     * Handle the PaymentTerm "deleted" event.
     */
    public function deleted(PaymentTerm $paymentTerm): void
    {
        //
    }

    /**
     * Handle the PaymentTerm "restored" event.
     */
    public function restored(PaymentTerm $paymentTerm): void
    {
        //
    }

    /**
     * Handle the PaymentTerm "force deleted" event.
     */
    public function forceDeleted(PaymentTerm $paymentTerm): void
    {
        //
    }
}