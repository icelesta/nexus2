<?php

declare(strict_types=1);

namespace App\Policies;

use App\Models\Department;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class DepartmentPolicy
{
    use HandlesAuthorization;

    /*
    |--------------------------------------------------------------------------
    | Basic Permissions
    |--------------------------------------------------------------------------
    */

    public function viewAny(User $user): bool
    {
        return $user->can('ViewAny:Department');
    }

    public function view(User $user, Department $department): bool
    {
        return $user->can('View:Department');
    }

    public function create(User $user): bool
    {
        return $user->can('Create:Department');
    }

    public function update(User $user, Department $department): bool
    {
        return $user->can('Update:Department');
    }

    public function delete(User $user, Department $department): bool
    {
        return $user->can('Delete:Department');
    }

    /*
    |--------------------------------------------------------------------------
    | Bulk Permissions
    |--------------------------------------------------------------------------
    */

    public function deleteAny(User $user): bool
    {
        return $user->can('DeleteAny:Department');
    }

    public function restoreAny(User $user): bool
    {
        return $user->can('RestoreAny:Department');
    }

    public function forceDeleteAny(User $user): bool
    {
        return $user->can('ForceDeleteAny:Department');
    }

    /*
    |--------------------------------------------------------------------------
    | Advanced Permissions
    |--------------------------------------------------------------------------
    */

    public function restore(User $user, Department $department): bool
    {
        return $user->can('Restore:Department');
    }

    public function forceDelete(User $user, Department $department): bool
    {
        return $user->can('ForceDelete:Department');
    }

    public function replicate(User $user, Department $department): bool
    {
        return $user->can('Replicate:Department');
    }

    public function reorder(User $user): bool
    {
        return $user->can('Reorder:Department');
    }
}