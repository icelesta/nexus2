<?php

declare(strict_types=1);

namespace App\Observers;

use App\Models\Currency;
use Illuminate\Support\Str;

class CurrencyObserver
{
    /**
     * Handle the Currency "creating" event.
     */
    public function creating(Currency $currency): void
    {
        /*
        |--------------------------------------------------------------------------
        | UUID
        |--------------------------------------------------------------------------
        */

        if (! filled($currency->uuid)) {
            $currency->uuid = (string) Str::uuid();
        }

        /*
        |--------------------------------------------------------------------------
        | Audit Trail
        |--------------------------------------------------------------------------
        */

        $userId = auth()->id();

        if ($userId) {
            $currency->created_by ??= $userId;
            $currency->updated_by ??= $userId;
        }

        /*
        |--------------------------------------------------------------------------
        | Default Value
        |--------------------------------------------------------------------------
        */

        $currency->is_active ??= true;
        $currency->is_base_currency ??= false;
    }

    /**
     * Handle the Currency "updating" event.
     */
    public function updating(Currency $currency): void
    {
        /*
        |--------------------------------------------------------------------------
        | Audit Trail
        |--------------------------------------------------------------------------
        */

        if ($userId = auth()->id()) {
            $currency->updated_by = $userId;
        }
    }

    /**
     * Handle the Currency "created" event.
     */
    public function created(Currency $currency): void
    {
        //
    }

    /**
     * Handle the Currency "updated" event.
     */
    public function updated(Currency $currency): void
    {
        //
    }

    /**
     * Handle the Currency "deleted" event.
     */
    public function deleted(Currency $currency): void
    {
        //
    }

    /**
     * Handle the Currency "restored" event.
     */
    public function restored(Currency $currency): void
    {
        //
    }

    /**
     * Handle the Currency "force deleted" event.
     */
    public function forceDeleted(Currency $currency): void
    {
        //
    }
}