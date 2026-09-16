<?php

declare(strict_types=1);

namespace App\Policies;

use App\Models\PurchaseRequisition;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class PurchaseRequisitionPolicy
{
    use HandlesAuthorization;

    /*
    |--------------------------------------------------------------------------
    | Before
    |--------------------------------------------------------------------------
    */

    public function before(User $user): ?bool
    {
        if ($user->isSuperAdmin()) {
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
        return $user->can('ViewAny:PurchaseRequisition');
    }

    public function view(User $user, PurchaseRequisition $record): bool
    {
        return $user->can('View:PurchaseRequisition');
    }

    /*
    |--------------------------------------------------------------------------
    | Create
    |--------------------------------------------------------------------------
    */

    public function create(User $user): bool
    {
        return $user->can('Create:PurchaseRequisition');
    }

    /*
    |--------------------------------------------------------------------------
    | Update
    |--------------------------------------------------------------------------
    */

    public function update(User $user, PurchaseRequisition $record): bool
    {
        return $user->can('Update:PurchaseRequisition');
    }

    /*
    |--------------------------------------------------------------------------
    | Delete
    |--------------------------------------------------------------------------
    */

    public function delete(User $user, PurchaseRequisition $record): bool
    {
        return $user->can('Delete:PurchaseRequisition');
    }

    public function deleteAny(User $user): bool
    {
        return $user->can('DeleteAny:PurchaseRequisition');
    }

    /*
    |--------------------------------------------------------------------------
    | Restore
    |--------------------------------------------------------------------------
    */

    public function restore(User $user, PurchaseRequisition $record): bool
    {
        return $user->can('Restore:PurchaseRequisition');
    }

    public function restoreAny(User $user): bool
    {
        return $user->can('RestoreAny:PurchaseRequisition');
    }

    /*
    |--------------------------------------------------------------------------
    | Force Delete
    |--------------------------------------------------------------------------
    */

    public function forceDelete(User $user, PurchaseRequisition $record): bool
    {
        return $user->can('ForceDelete:PurchaseRequisition');
    }

    public function forceDeleteAny(User $user): bool
    {
        return $user->can('ForceDeleteAny:PurchaseRequisition');
    }

    /*
    |--------------------------------------------------------------------------
    | Replicate
    |--------------------------------------------------------------------------
    */

    public function replicate(User $user, PurchaseRequisition $record): bool
    {
        return $user->can('Replicate:PurchaseRequisition');
    }

    /*
    |--------------------------------------------------------------------------
    | Reorder
    |--------------------------------------------------------------------------
    */

    public function reorder(User $user): bool
    {
        return $user->can('Reorder:PurchaseRequisition');
    }

    /*
    |--------------------------------------------------------------------------
    | ERP Workflow
    |--------------------------------------------------------------------------
    */

    public function submit(User $user): bool
    {
        return $user->can('Submit:PurchaseRequisition');
    }

    public function approve(User $user): bool
    {
        return $user->can('Approve:PurchaseRequisition');
    }

    public function reject(User $user): bool
    {
        return $user->can('Reject:PurchaseRequisition');
    }

    public function cancel(User $user): bool
    {
        return $user->can('Cancel:PurchaseRequisition');
    }

    public function reopen(User $user): bool
    {
        return $user->can('Reopen:PurchaseRequisition');
    }
}