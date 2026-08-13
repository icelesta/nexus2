<?php

declare(strict_types=1);

namespace App\Policies;

use App\Models\PurchaseRequisitionItem;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class PurchaseRequisitionItemPolicy
{
    use HandlesAuthorization;

    /*
    |--------------------------------------------------------------------------
    | Before
    |--------------------------------------------------------------------------
    */

    public function before(User $user): ?bool
    {
        if ($user->hasRole('Super Admin')) {
            return true;
        }

        return null;
    }

    /*
    |--------------------------------------------------------------------------
    | View
    |--------------------------------------------------------------------------
    */

    public function viewAny(User $user): bool
    {
        return $user->can('ViewAny:PurchaseRequisitionItem');
    }

    public function view(User $user, PurchaseRequisitionItem $record): bool
    {
        return $user->can('View:PurchaseRequisitionItem');
    }

    /*
    |--------------------------------------------------------------------------
    | Create
    |--------------------------------------------------------------------------
    */

    public function create(User $user): bool
    {
        return $user->can('Create:PurchaseRequisitionItem');
    }

    /*
    |--------------------------------------------------------------------------
    | Update
    |--------------------------------------------------------------------------
    */

    public function update(User $user, PurchaseRequisitionItem $record): bool
    {
        return $user->can('Update:PurchaseRequisitionItem');
    }

    /*
    |--------------------------------------------------------------------------
    | Delete
    |--------------------------------------------------------------------------
    */

    public function delete(User $user, PurchaseRequisitionItem $record): bool
    {
        return $user->can('Delete:PurchaseRequisitionItem');
    }

    public function deleteAny(User $user): bool
    {
        return $user->can('DeleteAny:PurchaseRequisitionItem');
    }

    /*
    |--------------------------------------------------------------------------
    | Restore
    |--------------------------------------------------------------------------
    */

    public function restore(User $user, PurchaseRequisitionItem $record): bool
    {
        return $user->can('Restore:PurchaseRequisitionItem');
    }

    public function restoreAny(User $user): bool
    {
        return $user->can('RestoreAny:PurchaseRequisitionItem');
    }

    /*
    |--------------------------------------------------------------------------
    | Force Delete
    |--------------------------------------------------------------------------
    */

    public function forceDelete(User $user, PurchaseRequisitionItem $record): bool
    {
        return $user->can('ForceDelete:PurchaseRequisitionItem');
    }

    public function forceDeleteAny(User $user): bool
    {
        return $user->can('ForceDeleteAny:PurchaseRequisitionItem');
    }

    /*
    |--------------------------------------------------------------------------
    | Replicate
    |--------------------------------------------------------------------------
    */

    public function replicate(User $user, PurchaseRequisitionItem $record): bool
    {
        return $user->can('Replicate:PurchaseRequisitionItem');
    }

    /*
    |--------------------------------------------------------------------------
    | Reorder
    |--------------------------------------------------------------------------
    */

    public function reorder(User $user): bool
    {
        return $user->can('Reorder:PurchaseRequisitionItem');
    }

    /*
    |--------------------------------------------------------------------------
    | ERP Workflow
    |--------------------------------------------------------------------------
    */

    public function submit(User $user): bool
    {
        return $user->can('Submit:PurchaseRequisitionItem');
    }

    public function approve(User $user): bool
    {
        return $user->can('Approve:PurchaseRequisitionItem');
    }

    public function reject(User $user): bool
    {
        return $user->can('Reject:PurchaseRequisitionItem');
    }

    public function cancel(User $user): bool
    {
        return $user->can('Cancel:PurchaseRequisitionItem');
    }

    public function reopen(User $user): bool
    {
        return $user->can('Reopen:PurchaseRequisitionItem');
    }
}