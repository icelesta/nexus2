<?php

declare(strict_types=1);

namespace App\Policies;

use App\Models\Manufacturer;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class ManufacturerPolicy
{
    use HandlesAuthorization;

    /*
    |--------------------------------------------------------------------------
    | View
    |--------------------------------------------------------------------------
    */

    public function viewAny(User $authUser): bool
    {
        return $authUser->can('ViewAny:Manufacturer');
    }

    public function view(
        User $authUser,
        Manufacturer $manufacturer,
    ): bool {

        return $authUser->can('View:Manufacturer');
    }

    /*
    |--------------------------------------------------------------------------
    | Create
    |--------------------------------------------------------------------------
    */

    public function create(User $authUser): bool
    {
        return $authUser->can('Create:Manufacturer');
    }

    /*
    |--------------------------------------------------------------------------
    | Update
    |--------------------------------------------------------------------------
    */

    public function update(
        User $authUser,
        Manufacturer $manufacturer,
    ): bool {

        return $authUser->can('Update:Manufacturer');
    }

    /*
    |--------------------------------------------------------------------------
    | Delete
    |--------------------------------------------------------------------------
    */

    public function delete(
        User $authUser,
        Manufacturer $manufacturer,
    ): bool {

        return $authUser->can('Delete:Manufacturer');
    }

    public function deleteAny(
        User $authUser,
    ): bool {

        return $authUser->can('DeleteAny:Manufacturer');
    }

    /*
    |--------------------------------------------------------------------------
    | Restore
    |--------------------------------------------------------------------------
    */

    public function restore(
        User $authUser,
        Manufacturer $manufacturer,
    ): bool {

        return $authUser->can('Restore:Manufacturer');
    }

    public function restoreAny(
        User $authUser,
    ): bool {

        return $authUser->can('RestoreAny:Manufacturer');
    }

    /*
    |--------------------------------------------------------------------------
    | Force Delete
    |--------------------------------------------------------------------------
    */

    public function forceDelete(
        User $authUser,
        Manufacturer $manufacturer,
    ): bool {

        return $authUser->can('ForceDelete:Manufacturer');
    }

    public function forceDeleteAny(
        User $authUser,
    ): bool {

        return $authUser->can('ForceDeleteAny:Manufacturer');
    }

    /*
    |--------------------------------------------------------------------------
    | Replicate
    |--------------------------------------------------------------------------
    */

    public function replicate(
        User $authUser,
        Manufacturer $manufacturer,
    ): bool {

        return $authUser->can('Replicate:Manufacturer');
    }

    /*
    |--------------------------------------------------------------------------
    | Reorder
    |--------------------------------------------------------------------------
    */

    public function reorder(
        User $authUser,
    ): bool {

        return $authUser->can('Reorder:Manufacturer');
    }
}