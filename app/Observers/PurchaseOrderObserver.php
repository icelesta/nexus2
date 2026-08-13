<?php

declare(strict_types=1);

namespace App\Observers;

use App\Models\PurchaseOrder;

class PurchaseOrderObserver
{
    /*
    |--------------------------------------------------------------------------
    | Before Events
    |--------------------------------------------------------------------------
    */

    /**
     * Handle the Purchase Order "creating" event.
     */
    public function creating(PurchaseOrder $model): void
    {
        /*
        |--------------------------------------------------------------------------
        | Default Document Status
        |--------------------------------------------------------------------------
        */

        if (blank($model->status)) {
            $model->status = PurchaseOrder::STATUS_DRAFT;
        }

        /*
        |--------------------------------------------------------------------------
        | Default Approval Status
        |--------------------------------------------------------------------------
        */

        if (blank($model->approval_status)) {
            $model->approval_status = PurchaseOrder::APPROVAL_PENDING;
        }

        /*
        |--------------------------------------------------------------------------
        | Audit Information
        |--------------------------------------------------------------------------
        */

        if (auth()->check()) {

            $model->created_by ??= auth()->id();

            $model->updated_by = auth()->id();

        }
    }

    /**
     * Handle the Purchase Order "updating" event.
     */
    public function updating(PurchaseOrder $model): void
    {
        /*
        |--------------------------------------------------------------------------
        | Audit Information
        |--------------------------------------------------------------------------
        */

        if (auth()->check()) {
            $model->updated_by = auth()->id();
        }
    }

    /**
     * Handle the Purchase Order "deleting" event.
     */
    public function deleting(PurchaseOrder $model): void
    {
        /*
        |--------------------------------------------------------------------------
        | Business Rule
        |--------------------------------------------------------------------------
        */

        if ($model->isLocked()) {

            throw new \RuntimeException(
                'Purchase Order cannot be deleted because it has already been processed.'
            );

        }

        /*
        |--------------------------------------------------------------------------
        | Audit Information
        |--------------------------------------------------------------------------
        */

        if (auth()->check()) {
            $model->deleted_by = auth()->id();

            // Persist deleted_by without triggering model events.
            $model->saveQuietly();
        }
    }

    /**
     * Handle the Purchase Order "restoring" event.
     */
    public function restoring(PurchaseOrder $model): void
    {
        //
    }

    /*
    |--------------------------------------------------------------------------
    | After Events
    |--------------------------------------------------------------------------
    */

    /**
     * Handle the Purchase Order "created" event.
     */
    public function created(PurchaseOrder $model): void
    {
        //
    }

    /**
     * Handle the Purchase Order "updated" event.
     */
    public function updated(PurchaseOrder $model): void
    {
        //
    }

    /**
     * Handle the Purchase Order "deleted" event.
     */
    public function deleted(PurchaseOrder $model): void
    {
        //
    }

    /**
     * Handle the Purchase Order "restored" event.
     */
    public function restored(PurchaseOrder $model): void
    {
        //
    }

    /*
    |--------------------------------------------------------------------------
    | Force Delete Events
    |--------------------------------------------------------------------------
    */

    /**
     * Handle the Purchase Order "force deleted" event.
     */
    public function forceDeleted(PurchaseOrder $model): void
    {
        //
    }
}