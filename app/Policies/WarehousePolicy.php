<?php

declare(strict_types=1);

namespace App\Policies;

use App\Models\User;
use App\Models\Warehouse;
use Illuminate\Auth\Access\HandlesAuthorization;

class WarehousePolicy
{
    use HandlesAuthorization;

    /*
    |--------------------------------------------------------------------------
    | Basic Permissions
    |--------------------------------------------------------------------------
    */

    public function viewAny(User $user): bool
    {
        return $user->can('ViewAny:Warehouse');
    }

    public function view(User $user, Warehouse $warehouse): bool
    {
        return $user->can('View:Warehouse');
    }

    public function create(User $user): bool
    {
        return $user->can('Create:Warehouse');
    }

    public function update(User $user, Warehouse $warehouse): bool
    {
        return $user->can('Update:Warehouse');
    }

    public function delete(User $user, Warehouse $warehouse): bool
    {
        return $user->can('Delete:Warehouse');
    }

    /*
    |--------------------------------------------------------------------------
    | Bulk Permissions
    |--------------------------------------------------------------------------
    */

    public function deleteAny(User $user): bool
    {
        return $user->can('DeleteAny:Warehouse');
    }

    public function restoreAny(User $user): bool
    {
        return $user->can('RestoreAny:Warehouse');
    }

    public function forceDeleteAny(User $user): bool
    {
        return $user->can('ForceDeleteAny:Warehouse');
    }

    /*
    |--------------------------------------------------------------------------
    | Advanced Permissions
    |--------------------------------------------------------------------------
    */

    public function restore(User $user, Warehouse $warehouse): bool
    {
        return $user->can('Restore:Warehouse');
    }

    public function forceDelete(User $user, Warehouse $warehouse): bool
    {
        return $user->can('ForceDelete:Warehouse');
    }

    public function replicate(User $user, Warehouse $warehouse): bool
    {
        return $user->can('Replicate:Warehouse');
    }

    public function reorder(User $user): bool
    {
        return $user->can('Reorder:Warehouse');
    }
}