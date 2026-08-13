<?php

declare(strict_types=1);

namespace App\Policies;

use App\Models\ItemSupplier;
use App\Models\User;

use Illuminate\Auth\Access\HandlesAuthorization;

class ItemSupplierPolicy
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
        return $user->can('ViewAny:ItemSupplier');
    }

    public function view(User $user, ItemSupplier $record): bool
    {
        return $user->can('View:ItemSupplier');
    }

    /*
    |--------------------------------------------------------------------------
    | Create
    |--------------------------------------------------------------------------
    */

    public function create(User $user): bool
    {
        return $user->can('Create:ItemSupplier');
    }

    /*
    |--------------------------------------------------------------------------
    | Update
    |--------------------------------------------------------------------------
    */

    public function update(User $user, ItemSupplier $record): bool
    {
        return $user->can('Update:ItemSupplier');
    }

    /*
    |--------------------------------------------------------------------------
    | Delete
    |--------------------------------------------------------------------------
    */

    public function delete(User $user, ItemSupplier $record): bool
    {
        return $user->can('Delete:ItemSupplier');
    }

    public function deleteAny(User $user): bool
    {
        return $user->can('DeleteAny:ItemSupplier');
    }

    /*
    |--------------------------------------------------------------------------
    | Restore
    |--------------------------------------------------------------------------
    */

    public function restore(User $user, ItemSupplier $record): bool
    {
        return $user->can('Restore:ItemSupplier');
    }

    public function restoreAny(User $user): bool
    {
        return $user->can('RestoreAny:ItemSupplier');
    }

    /*
    |--------------------------------------------------------------------------
    | Force Delete
    |--------------------------------------------------------------------------
    */

    public function forceDelete(User $user, ItemSupplier $record): bool
    {
        return $user->can('ForceDelete:ItemSupplier');
    }

    public function forceDeleteAny(User $user): bool
    {
        return $user->can('ForceDeleteAny:ItemSupplier');
    }

    /*
    |--------------------------------------------------------------------------
    | Replicate
    |--------------------------------------------------------------------------
    */

    public function replicate(User $user, ItemSupplier $record): bool
    {
        return $user->can('Replicate:ItemSupplier');
    }

    /*
    |--------------------------------------------------------------------------
    | Reorder
    |--------------------------------------------------------------------------
    */

    public function reorder(User $user): bool
    {
        return $user->can('Reorder:ItemSupplier');
    }
}