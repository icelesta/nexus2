<?php

declare(strict_types=1);

namespace App\Policies;

use App\Models\User;
use App\Models\WarehouseType;
use Illuminate\Auth\Access\HandlesAuthorization;

class WarehouseTypePolicy
{
    use HandlesAuthorization;

    /*
    |--------------------------------------------------------------------------
    | Basic Permissions
    |--------------------------------------------------------------------------
    */

    public function viewAny(User $user): bool
    {
        return $user->can('ViewAny:WarehouseType');
    }

    public function view(User $user, WarehouseType $warehouseType): bool
    {
        return $user->can('View:WarehouseType');
    }

    public function create(User $user): bool
    {
        return $user->can('Create:WarehouseType');
    }

    public function update(User $user, WarehouseType $warehouseType): bool
    {
        return $user->can('Update:WarehouseType');
    }

    public function delete(User $user, WarehouseType $warehouseType): bool
    {
        return $user->can('Delete:WarehouseType');
    }

    /*
    |--------------------------------------------------------------------------
    | Bulk Permissions
    |--------------------------------------------------------------------------
    */

    public function deleteAny(User $user): bool
    {
        return $user->can('DeleteAny:WarehouseType');
    }

    public function restoreAny(User $user): bool
    {
        return $user->can('RestoreAny:WarehouseType');
    }

    public function forceDeleteAny(User $user): bool
    {
        return $user->can('ForceDeleteAny:WarehouseType');
    }

    /*
    |--------------------------------------------------------------------------
    | Advanced Permissions
    |--------------------------------------------------------------------------
    */

    public function restore(User $user, WarehouseType $warehouseType): bool
    {
        return $user->can('Restore:WarehouseType');
    }

    public function forceDelete(User $user, WarehouseType $warehouseType): bool
    {
        return $user->can('ForceDelete:WarehouseType');
    }

    public function replicate(User $user, WarehouseType $warehouseType): bool
    {
        return $user->can('Replicate:WarehouseType');
    }

    public function reorder(User $user): bool
    {
        return $user->can('Reorder:WarehouseType');
    }
}