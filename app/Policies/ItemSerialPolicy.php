<?php

declare(strict_types=1);

namespace App\Policies;

use App\Models\ItemSerial;
use App\Models\User;

use Illuminate\Auth\Access\HandlesAuthorization;

class ItemSerialPolicy
{
    use HandlesAuthorization;

    /*
    |--------------------------------------------------------------------------
    | Before
    |--------------------------------------------------------------------------
    */

    public function before(User $user): ?bool
    {
        if ($user->hasRole('Super Admin')) {
            return true;
        }

        return null;
    }

    /*
    |--------------------------------------------------------------------------
    | View
    |--------------------------------------------------------------------------
    */

    public function viewAny(User $user): bool
    {
        return $user->can('ViewAny:ItemSerial');
    }

    public function view(User $user, ItemSerial $record): bool
    {
        return $user->can('View:ItemSerial');
    }

    /*
    |--------------------------------------------------------------------------
    | Create
    |--------------------------------------------------------------------------
    */

    public function create(User $user): bool
    {
        return $user->can('Create:ItemSerial');
    }

    /*
    |--------------------------------------------------------------------------
    | Update
    |--------------------------------------------------------------------------
    */

    public function update(User $user, ItemSerial $record): bool
    {
        return $user->can('Update:ItemSerial');
    }

    /*
    |--------------------------------------------------------------------------
    | Delete
    |--------------------------------------------------------------------------
    */

    public function delete(User $user, ItemSerial $record): bool
    {
        return $user->can('Delete:ItemSerial');
    }

    public function deleteAny(User $user): bool
    {
        return $user->can('DeleteAny:ItemSerial');
    }

    /*
    |--------------------------------------------------------------------------
    | Restore
    |--------------------------------------------------------------------------
    */

    public function restore(User $user, ItemSerial $record): bool
    {
        return $user->can('Restore:ItemSerial');
    }

    public function restoreAny(User $user): bool
    {
        return $user->can('RestoreAny:ItemSerial');
    }

    /*
    |--------------------------------------------------------------------------
    | Force Delete
    |--------------------------------------------------------------------------
    */

    public function forceDelete(User $user, ItemSerial $record): bool
    {
        return $user->can('ForceDelete:ItemSerial');
    }

    public function forceDeleteAny(User $user): bool
    {
        return $user->can('ForceDeleteAny:ItemSerial');
    }

    /*
    |--------------------------------------------------------------------------
    | Replicate
    |--------------------------------------------------------------------------
    */

    public function replicate(User $user, ItemSerial $record): bool
    {
        return $user->can('Replicate:ItemSerial');
    }

    /*
    |--------------------------------------------------------------------------
    | Reorder
    |--------------------------------------------------------------------------
    */

    public function reorder(User $user): bool
    {
        return $user->can('Reorder:ItemSerial');
    }
}