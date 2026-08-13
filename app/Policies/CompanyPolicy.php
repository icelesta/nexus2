<?php

declare(strict_types=1);

namespace App\Policies;

use App\Models\Company;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class CompanyPolicy
{
    use HandlesAuthorization;

    /*
    |--------------------------------------------------------------------------
    | Basic Permissions
    |--------------------------------------------------------------------------
    */

    public function viewAny(User $user): bool
    {
        return $user->can('ViewAny:Company');
    }

    public function view(User $user, Company $company): bool
    {
        return $user->can('View:Company');
    }

    public function create(User $user): bool
    {
        return $user->can('Create:Company');
    }

    public function update(User $user, Company $company): bool
    {
        return $user->can('Update:Company');
    }

    public function delete(User $user, Company $company): bool
    {
        return $user->can('Delete:Company');
    }

    /*
    |--------------------------------------------------------------------------
    | Bulk Permissions
    |--------------------------------------------------------------------------
    */

    public function deleteAny(User $user): bool
    {
        return $user->can('DeleteAny:Company');
    }

    public function restoreAny(User $user): bool
    {
        return $user->can('RestoreAny:Company');
    }

    public function forceDeleteAny(User $user): bool
    {
        return $user->can('ForceDeleteAny:Company');
    }

    /*
    |--------------------------------------------------------------------------
    | Advanced Permissions
    |--------------------------------------------------------------------------
    */

    public function restore(User $user, Company $company): bool
    {
        return $user->can('Restore:Company');
    }

    public function forceDelete(User $user, Company $company): bool
    {
        return $user->can('ForceDelete:Company');
    }

    public function replicate(User $user, Company $company): bool
    {
        return $user->can('Replicate:Company');
    }

    public function reorder(User $user): bool
    {
        return $user->can('Reorder:Company');
    }
}