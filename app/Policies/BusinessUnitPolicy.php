<?php

declare(strict_types=1);

namespace App\Policies;

use App\Models\BusinessUnit;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class BusinessUnitPolicy
{
    use HandlesAuthorization;

    /*
    |--------------------------------------------------------------------------
    | Basic Permissions
    |--------------------------------------------------------------------------
    */

    public function viewAny(User $user): bool
    {
        return $user->can('ViewAny:BusinessUnit');
    }

    public function view(User $user, BusinessUnit $businessUnit): bool
    {
        return $user->can('View:BusinessUnit');
    }

    public function create(User $user): bool
    {
        return $user->can('Create:BusinessUnit');
    }

    public function update(User $user, BusinessUnit $businessUnit): bool
    {
        return $user->can('Update:BusinessUnit');
    }

    public function delete(User $user, BusinessUnit $businessUnit): bool
    {
        return $user->can('Delete:BusinessUnit');
    }

    /*
    |--------------------------------------------------------------------------
    | Bulk Permissions
    |--------------------------------------------------------------------------
    */

    public function deleteAny(User $user): bool
    {
        return $user->can('DeleteAny:BusinessUnit');
    }

    public function restoreAny(User $user): bool
    {
        return $user->can('RestoreAny:BusinessUnit');
    }

    public function forceDeleteAny(User $user): bool
    {
        return $user->can('ForceDeleteAny:BusinessUnit');
    }

    /*
    |--------------------------------------------------------------------------
    | Advanced Permissions
    |--------------------------------------------------------------------------
    */

    public function restore(User $user, BusinessUnit $businessUnit): bool
    {
        return $user->can('Restore:BusinessUnit');
    }

    public function forceDelete(User $user, BusinessUnit $businessUnit): bool
    {
        return $user->can('ForceDelete:BusinessUnit');
    }

    public function replicate(User $user, BusinessUnit $businessUnit): bool
    {
        return $user->can('Replicate:BusinessUnit');
    }

    public function reorder(User $user): bool
    {
        return $user->can('Reorder:BusinessUnit');
    }
}