<?php

declare(strict_types=1);

namespace App\Observers;

use App\Models\ExchangeRate;
use Illuminate\Support\Str;

class ExchangeRateObserver
{
    /**
     * Handle the ExchangeRate "creating" event.
     */
    public function creating(ExchangeRate $exchangeRate): void
    {
        /*
        |--------------------------------------------------------------------------
        | UUID
        |--------------------------------------------------------------------------
        */

        if (! filled($exchangeRate->uuid)) {
            $exchangeRate->uuid = (string) Str::uuid();
        }

        /*
        |--------------------------------------------------------------------------
        | Audit Trail
        |--------------------------------------------------------------------------
        */

        $userId = auth()->id();

        if ($userId) {
            $exchangeRate->created_by ??= $userId;
            $exchangeRate->updated_by ??= $userId;
        }

        /*
        |--------------------------------------------------------------------------
        | Default Value
        |--------------------------------------------------------------------------
        */

        $exchangeRate->exchange_rate ??= 1;
        $exchangeRate->is_active ??= true;
    }

    /**
     * Handle the ExchangeRate "updating" event.
     */
    public function updating(ExchangeRate $exchangeRate): void
    {
        /*
        |--------------------------------------------------------------------------
        | Audit Trail
        |--------------------------------------------------------------------------
        */

        if ($userId = auth()->id()) {
            $exchangeRate->updated_by = $userId;
        }
    }

    /**
     * Handle the ExchangeRate "created" event.
     */
    public function created(ExchangeRate $exchangeRate): void
    {
        //
    }

    /**
     * Handle the ExchangeRate "updated" event.
     */
    public function updated(ExchangeRate $exchangeRate): void
    {
        //
    }

    /**
     * Handle the ExchangeRate "deleted" event.
     */
    public function deleted(ExchangeRate $exchangeRate): void
    {
        //
    }

    /**
     * Handle the ExchangeRate "restored" event.
     */
    public function restored(ExchangeRate $exchangeRate): void
    {
        //
    }

    /**
     * Handle the ExchangeRate "force deleted" event.
     */
    public function forceDeleted(ExchangeRate $exchangeRate): void
    {
        //
    }
}