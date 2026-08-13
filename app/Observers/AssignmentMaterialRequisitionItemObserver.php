<?php

declare(strict_types=1);

namespace App\Observers;

use App\Models\AssignmentMaterialRequisitionItem;

class AssignmentMaterialRequisitionItemObserver
{
    /*
    |--------------------------------------------------------------------------
    | Before Events
    |--------------------------------------------------------------------------
    */

    public function creating(AssignmentMaterialRequisitionItem $model): void
    {
        //
    }
    public function updating(AssignmentMaterialRequisitionItem $model): void
    {
        //
    }

    public function deleting(AssignmentMaterialRequisitionItem $model): void
    {
        //
    }

    public function restoring(AssignmentMaterialRequisitionItem $model): void
    {
    }

    /*
    |--------------------------------------------------------------------------
    | After Events
    |--------------------------------------------------------------------------
    */

    public function created(AssignmentMaterialRequisitionItem $model): void
    {
        //
    }

    public function updated(AssignmentMaterialRequisitionItem $model): void
    {
        //
    }

    public function deleted(AssignmentMaterialRequisitionItem $model): void
    {
        //
    }

    public function restored(AssignmentMaterialRequisitionItem $model): void
    {
    }

    /*
    |--------------------------------------------------------------------------
    | Force Delete Events
    |--------------------------------------------------------------------------
    */

    public function forceDeleted(AssignmentMaterialRequisitionItem $model): void
    {
    }
}