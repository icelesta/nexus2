<?php

declare(strict_types=1);

namespace App\Observers;

use App\Models\AssignmentMaterialRequisition;

class AssignmentMaterialRequisitionObserver
{
    /*
    |--------------------------------------------------------------------------
    | Before Events
    |--------------------------------------------------------------------------
    */

    public function creating(AssignmentMaterialRequisition $model): void
    {
        if (blank($model->status)) {
            $model->status = AssignmentMaterialRequisition::STATUS_DRAFT;
        }

        if (auth()->check()) {

            $model->created_by ??= auth()->id();

            $model->updated_by = auth()->id();

        }
    }

    public function updating(AssignmentMaterialRequisition $model): void
    {
        if (auth()->check()) {
            $model->updated_by = auth()->id();
        }
    }

    public function deleting(AssignmentMaterialRequisition $model): void
    {
        //
    }

    public function restoring(AssignmentMaterialRequisition $model): void
    {
        //
    }

    /*
    |--------------------------------------------------------------------------
    | After Events
    |--------------------------------------------------------------------------
    */

    public function created(AssignmentMaterialRequisition $model): void
    {
        //
    }

    public function updated(AssignmentMaterialRequisition $model): void
    {
        //
    }

    public function deleted(AssignmentMaterialRequisition $model): void
    {
        //
    }

    public function restored(AssignmentMaterialRequisition $model): void
    {
        //
    }

    /*
    |--------------------------------------------------------------------------
    | Force Delete Events
    |--------------------------------------------------------------------------
    */

    public function forceDeleted(AssignmentMaterialRequisition $model): void
    {
        //
    }
}