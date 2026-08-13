<?php

declare(strict_types=1);

namespace App\Observers;

use App\Models\Company;
use Illuminate\Support\Str;

class CompanyObserver
{
    /**
     * Handle the Company "creating" event.
     */
    public function creating(Company $company): void
    {
        /*
        |--------------------------------------------------------------------------
        | UUID
        |--------------------------------------------------------------------------
        */

        if (! filled($company->uuid)) {
            $company->uuid = (string) Str::uuid();
        }

        /*
        |--------------------------------------------------------------------------
        | Audit Trail
        |--------------------------------------------------------------------------
        */

        $userId = auth()->id();

        if ($userId) {
            $company->created_by ??= $userId;
            $company->updated_by ??= $userId;
        }

        /*
        |--------------------------------------------------------------------------
        | Default Value
        |--------------------------------------------------------------------------
        */

        $company->is_active ??= true;
    }

    /**
     * Handle the Company "updating" event.
     */
    public function updating(Company $company): void
    {
        /*
        |--------------------------------------------------------------------------
        | Audit Trail
        |--------------------------------------------------------------------------
        */

        if ($userId = auth()->id()) {
            $company->updated_by = $userId;
        }
    }

    /**
     * Handle the Company "created" event.
     */
    public function created(Company $company): void
    {
        //
    }

    /**
     * Handle the Company "updated" event.
     */
    public function updated(Company $company): void
    {
        //
    }

    /**
     * Handle the Company "deleted" event.
     */
    public function deleted(Company $company): void
    {
        //
    }

    /**
     * Handle the Company "restored" event.
     */
    public function restored(Company $company): void
    {
        //
    }

    /**
     * Handle the Company "force deleted" event.
     */
    public function forceDeleted(Company $company): void
    {
        //
    }
}