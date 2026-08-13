<?php

declare(strict_types=1);

namespace App\Policies;

use App\Models\MasterCategory;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class MasterCategoryPolicy
{
    use HandlesAuthorization;

    /*
    |--------------------------------------------------------------------------
    | Basic Permissions
    |--------------------------------------------------------------------------
    */

    public function viewAny(User $user): bool
    {
        return $user->can('ViewAny:MasterCategory');
    }

    public function view(User $user, MasterCategory $masterCategory): bool
    {
        return $user->can('View:MasterCategory');
    }

    public function create(User $user): bool
    {
        return $user->can('Create:MasterCategory');
    }

    public function update(User $user, MasterCategory $masterCategory): bool
    {
        return $user->can('Update:MasterCategory');
    }

    public function delete(User $user, MasterCategory $masterCategory): bool
    {
        return $user->can('Delete:MasterCategory');
    }

    /*
    |--------------------------------------------------------------------------
    | Bulk Permissions
    |--------------------------------------------------------------------------
    */

    public function deleteAny(User $user): bool
    {
        return $user->can('DeleteAny:MasterCategory');
    }

    public function restoreAny(User $user): bool
    {
        return $user->can('RestoreAny:MasterCategory');
    }

    public function forceDeleteAny(User $user): bool
    {
        return $user->can('ForceDeleteAny:MasterCategory');
    }

    /*
    |--------------------------------------------------------------------------
    | Advanced Permissions
    |--------------------------------------------------------------------------
    */

    public function restore(User $user, MasterCategory $masterCategory): bool
    {
        return $user->can('Restore:MasterCategory');
    }

    public function forceDelete(User $user, MasterCategory $masterCategory): bool
    {
        return $user->can('ForceDelete:MasterCategory');
    }

    public function replicate(User $user, MasterCategory $masterCategory): bool
    {
        return $user->can('Replicate:MasterCategory');
    }

    public function reorder(User $user): bool
    {
        return $user->can('Reorder:MasterCategory');
    }
}