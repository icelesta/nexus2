<?php

declare(strict_types=1);

namespace App\Policies;

use App\Models\Branch;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class BranchPolicy
{
    use HandlesAuthorization;

    /*
    |--------------------------------------------------------------------------
    | Basic Permissions
    |--------------------------------------------------------------------------
    */

    public function viewAny(User $user): bool
    {
        return $user->can('ViewAny:Branch');
    }

    public function view(User $user, Branch $branch): bool
    {
        return $user->can('View:Branch');
    }

    public function create(User $user): bool
    {
        return $user->can('Create:Branch');
    }

    public function update(User $user, Branch $branch): bool
    {
        return $user->can('Update:Branch');
    }

    public function delete(User $user, Branch $branch): bool
    {
        return $user->can('Delete:Branch');
    }

    /*
    |--------------------------------------------------------------------------
    | Bulk Permissions
    |--------------------------------------------------------------------------
    */

    public function deleteAny(User $user): bool
    {
        return $user->can('DeleteAny:Branch');
    }

    public function restoreAny(User $user): bool
    {
        return $user->can('RestoreAny:Branch');
    }

    public function forceDeleteAny(User $user): bool
    {
        return $user->can('ForceDeleteAny:Branch');
    }

    /*
    |--------------------------------------------------------------------------
    | Advanced Permissions
    |--------------------------------------------------------------------------
    */

    public function restore(User $user, Branch $branch): bool
    {
        return $user->can('Restore:Branch');
    }

    public function forceDelete(User $user, Branch $branch): bool
    {
        return $user->can('ForceDelete:Branch');
    }

    public function replicate(User $user, Branch $branch): bool
    {
        return $user->can('Replicate:Branch');
    }

    public function reorder(User $user): bool
    {
        return $user->can('Reorder:Branch');
    }
}