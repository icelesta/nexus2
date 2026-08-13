<?php

declare(strict_types=1);

namespace App\Policies;

use App\Models\CostCenter;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class CostCenterPolicy
{
    use HandlesAuthorization;

    /*
    |--------------------------------------------------------------------------
    | Basic Permissions
    |--------------------------------------------------------------------------
    */

    public function viewAny(User $user): bool
    {
        return $user->can('ViewAny:CostCenter');
    }

    public function view(User $user, CostCenter $costCenter): bool
    {
        return $user->can('View:CostCenter');
    }

    public function create(User $user): bool
    {
        return $user->can('Create:CostCenter');
    }

    public function update(User $user, CostCenter $costCenter): bool
    {
        return $user->can('Update:CostCenter');
    }

    public function delete(User $user, CostCenter $costCenter): bool
    {
        return $user->can('Delete:CostCenter');
    }

    /*
    |--------------------------------------------------------------------------
    | Bulk Permissions
    |--------------------------------------------------------------------------
    */

    public function deleteAny(User $user): bool
    {
        return $user->can('DeleteAny:CostCenter');
    }

    public function restoreAny(User $user): bool
    {
        return $user->can('RestoreAny:CostCenter');
    }

    public function forceDeleteAny(User $user): bool
    {
        return $user->can('ForceDeleteAny:CostCenter');
    }

    /*
    |--------------------------------------------------------------------------
    | Advanced Permissions
    |--------------------------------------------------------------------------
    */

    public function restore(User $user, CostCenter $costCenter): bool
    {
        return $user->can('Restore:CostCenter');
    }

    public function forceDelete(User $user, CostCenter $costCenter): bool
    {
        return $user->can('ForceDelete:CostCenter');
    }

    public function replicate(User $user, CostCenter $costCenter): bool
    {
        return $user->can('Replicate:CostCenter');
    }

    public function reorder(User $user): bool
    {
        return $user->can('Reorder:CostCenter');
    }
}