<?php

declare(strict_types=1);

namespace App\Observers;

use App\Models\PurchaseOrderItem;

class PurchaseOrderItemObserver
{
    /*
    |--------------------------------------------------------------------------
    | Before Events
    |--------------------------------------------------------------------------
    */

    /**
     * Handle the Purchase Order Item "creating" event.
     */
    public function creating(PurchaseOrderItem $model): void
    {
        /*
        |--------------------------------------------------------------------------
        | Default Status
        |--------------------------------------------------------------------------
        */

        if (blank($model->status)) {
            $model->status = PurchaseOrderItem::STATUS_OPEN;
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
     * Handle the Purchase Order Item "updating" event.
     */
    public function updating(PurchaseOrderItem $model): void
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
     * Handle the Purchase Order Item "deleting" event.
     */
    public function deleting(PurchaseOrderItem $model): void
    {
        /*
        |--------------------------------------------------------------------------
        | Business Rule
        |--------------------------------------------------------------------------
        */

        if ($model->isLocked()) {

            throw new \RuntimeException(
                'Purchase Order Item cannot be deleted because it has already been processed.'
            );

        }

        /*
        |--------------------------------------------------------------------------
        | Audit Information
        |--------------------------------------------------------------------------
        */

        if (auth()->check()) {

            $model->deleted_by = auth()->id();

            // Persist deleted_by before Soft Delete.
            $model->saveQuietly();

        }
    }

    /**
     * Handle the Purchase Order Item "restoring" event.
     */
    public function restoring(PurchaseOrderItem $model): void
    {
        //
    }

    /*
    |--------------------------------------------------------------------------
    | After Events
    |--------------------------------------------------------------------------
    */

    /**
     * Handle the Purchase Order Item "created" event.
     */
    public function created(PurchaseOrderItem $model): void
    {
        //
    }

    /**
     * Handle the Purchase Order Item "updated" event.
     */
    public function updated(PurchaseOrderItem $model): void
    {
        //
    }

    /**
     * Handle the Purchase Order Item "deleted" event.
     */
    public function deleted(PurchaseOrderItem $model): void
    {
        //
    }

    /**
     * Handle the Purchase Order Item "restored" event.
     */
    public function restored(PurchaseOrderItem $model): void
    {
        //
    }

    /*
    |--------------------------------------------------------------------------
    | Force Delete Events
    |--------------------------------------------------------------------------
    */

    /**
     * Handle the Purchase Order Item "force deleted" event.
     */
    public function forceDeleted(PurchaseOrderItem $model): void
    {
        //
    }
}