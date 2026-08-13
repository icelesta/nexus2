<?php

declare(strict_types=1);

namespace App\Observers;

use App\Models\ChartOfAccount;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class ChartOfAccountObserver
{

    public function creating(
        ChartOfAccount $chartOfAccount
    ): void {

        Log::info('Before', [
            'attributes' => $chartOfAccount->getAttributes(),
        ]);

                /*
        |--------------------------------------------------------------------------
        | UUID
        |--------------------------------------------------------------------------
        */

        if (! filled($chartOfAccount->uuid)) {
            $chartOfAccount->uuid = (string) Str::uuid();
        }

        /*
        |--------------------------------------------------------------------------
        | Audit Trail
        |--------------------------------------------------------------------------
        */

        $userId = auth()->id();

        if ($userId) {
            $chartOfAccount->created_by ??= $userId;
            $chartOfAccount->updated_by ??= $userId;
        }

        /*
        |--------------------------------------------------------------------------
        | Default Value
        |--------------------------------------------------------------------------
        */

        $chartOfAccount->allow_posting ??= true;

        $chartOfAccount->require_cost_center ??= false;

        $chartOfAccount->require_profit_center ??= false;

        $chartOfAccount->require_department ??= false;

        $chartOfAccount->require_project ??= false;

        $chartOfAccount->is_active ??= true;

        Log::info('ChartOfAccountObserver after defaults', [
            'attributes_after' => $chartOfAccount->getAttributes(),
        ]);
    }



    /**
     * Handle the ChartOfAccount "updating" event.
     */
    public function updating(ChartOfAccount $chartOfAccount): void
    {
        /*
        |--------------------------------------------------------------------------
        | Audit Trail
        |--------------------------------------------------------------------------
        */

        if ($userId = auth()->id()) {
            $chartOfAccount->updated_by = $userId;
        }
    }

    /**
     * Handle the ChartOfAccount "created" event.
     */
    public function created(ChartOfAccount $chartOfAccount): void
    {
        //
    }

    /**
     * Handle the ChartOfAccount "updated" event.
     */
    public function updated(ChartOfAccount $chartOfAccount): void
    {
        //
    }

    /**
     * Handle the ChartOfAccount "deleting" event.
     */
    public function deleting(ChartOfAccount $chartOfAccount): void
    {
        //
    }

    /**
     * Handle the ChartOfAccount "deleted" event.
     */
    public function deleted(ChartOfAccount $chartOfAccount): void
    {
        //
    }

    /**
     * Handle the ChartOfAccount "restoring" event.
     */
    public function restoring(ChartOfAccount $chartOfAccount): void
    {
        //
    }

    /**
     * Handle the ChartOfAccount "restored" event.
     */
    public function restored(ChartOfAccount $chartOfAccount): void
    {
        //
    }

    /**
     * Handle the ChartOfAccount "force deleted" event.
     */
    public function forceDeleted(ChartOfAccount $chartOfAccount): void
    {
        //
    }
}