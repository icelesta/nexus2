<?php

declare(strict_types=1);

namespace App\Policies;

use App\Models\ItemStock;
use App\Models\User;

use Illuminate\Auth\Access\HandlesAuthorization;

class ItemStockPolicy
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
        return $user->can('ViewAny:ItemStock');
    }

    public function view(User $user, ItemStock $record): bool
    {
        return $user->can('View:ItemStock');
    }

    /*
    |--------------------------------------------------------------------------
    | Create
    |--------------------------------------------------------------------------
    */

    public function create(User $user): bool
    {
        return $user->can('Create:ItemStock');
    }

    /*
    |--------------------------------------------------------------------------
    | Update
    |--------------------------------------------------------------------------
    */

    public function update(User $user, ItemStock $record): bool
    {
        return $user->can('Update:ItemStock');
    }

    /*
    |--------------------------------------------------------------------------
    | Delete
    |--------------------------------------------------------------------------
    */

    public function delete(User $user, ItemStock $record): bool
    {
        return $user->can('Delete:ItemStock');
    }

    public function deleteAny(User $user): bool
    {
        return $user->can('DeleteAny:ItemStock');
    }

    /*
    |--------------------------------------------------------------------------
    | Restore
    |--------------------------------------------------------------------------
    */

    public function restore(User $user, ItemStock $record): bool
    {
        return $user->can('Restore:ItemStock');
    }

    public function restoreAny(User $user): bool
    {
        return $user->can('RestoreAny:ItemStock');
    }

    /*
    |--------------------------------------------------------------------------
    | Force Delete
    |--------------------------------------------------------------------------
    */

    public function forceDelete(User $user, ItemStock $record): bool
    {
        return $user->can('ForceDelete:ItemStock');
    }

    public function forceDeleteAny(User $user): bool
    {
        return $user->can('ForceDeleteAny:ItemStock');
    }

    /*
    |--------------------------------------------------------------------------
    | Replicate
    |--------------------------------------------------------------------------
    */

    public function replicate(User $user, ItemStock $record): bool
    {
        return $user->can('Replicate:ItemStock');
    }

    /*
    |--------------------------------------------------------------------------
    | Reorder
    |--------------------------------------------------------------------------
    */

    public function reorder(User $user): bool
    {
        return $user->can('Reorder:ItemStock');
    }
}