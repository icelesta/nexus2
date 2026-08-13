<?php

declare(strict_types=1);

namespace App\Policies;

use App\Models\Brand;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class BrandPolicy
{
    use HandlesAuthorization;

    /*
    |--------------------------------------------------------------------------
    | View
    |--------------------------------------------------------------------------
    */

    public function viewAny(User $authUser): bool
    {
        return $authUser->can('ViewAny:Brand');
    }

    public function view(User $authUser, Brand $brand): bool
    {
        return $authUser->can('View:Brand');
    }

    /*
    |--------------------------------------------------------------------------
    | Create
    |--------------------------------------------------------------------------
    */

    public function create(User $authUser): bool
    {
        return $authUser->can('Create:Brand');
    }

    /*
    |--------------------------------------------------------------------------
    | Update
    |--------------------------------------------------------------------------
    */

    public function update(User $authUser, Brand $brand): bool
    {
        return $authUser->can('Update:Brand');
    }

    /*
    |--------------------------------------------------------------------------
    | Delete
    |--------------------------------------------------------------------------
    */

    public function delete(User $authUser, Brand $brand): bool
    {
        return $authUser->can('Delete:Brand');
    }

    public function deleteAny(User $authUser): bool
    {
        return $authUser->can('DeleteAny:Brand');
    }

    /*
    |--------------------------------------------------------------------------
    | Restore
    |--------------------------------------------------------------------------
    */

    public function restore(User $authUser, Brand $brand): bool
    {
        return $authUser->can('Restore:Brand');
    }

    public function restoreAny(User $authUser): bool
    {
        return $authUser->can('RestoreAny:Brand');
    }

    /*
    |--------------------------------------------------------------------------
    | Force Delete
    |--------------------------------------------------------------------------
    */

    public function forceDelete(User $authUser, Brand $brand): bool
    {
        return $authUser->can('ForceDelete:Brand');
    }

    public function forceDeleteAny(User $authUser): bool
    {
        return $authUser->can('ForceDeleteAny:Brand');
    }

    /*
    |--------------------------------------------------------------------------
    | Replicate
    |--------------------------------------------------------------------------
    */

    public function replicate(User $authUser, Brand $brand): bool
    {
        return $authUser->can('Replicate:Brand');
    }

    /*
    |--------------------------------------------------------------------------
    | Reorder
    |--------------------------------------------------------------------------
    */

    public function reorder(User $authUser): bool
    {
        return $authUser->can('Reorder:Brand');
    }
}