<?php

declare(strict_types=1);

namespace App\Policies;

use App\Models\ItemPrice;
use App\Models\User;

use Illuminate\Auth\Access\HandlesAuthorization;

class ItemPricePolicy
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
        return $user->can('ViewAny:ItemPrice');
    }

    public function view(User $user, ItemPrice $record): bool
    {
        return $user->can('View:ItemPrice');
    }

    /*
    |--------------------------------------------------------------------------
    | Create
    |--------------------------------------------------------------------------
    */

    public function create(User $user): bool
    {
        return $user->can('Create:ItemPrice');
    }

    /*
    |--------------------------------------------------------------------------
    | Update
    |--------------------------------------------------------------------------
    */

    public function update(User $user, ItemPrice $record): bool
    {
        return $user->can('Update:ItemPrice');
    }

    /*
    |--------------------------------------------------------------------------
    | Delete
    |--------------------------------------------------------------------------
    */

    public function delete(User $user, ItemPrice $record): bool
    {
        return $user->can('Delete:ItemPrice');
    }

    public function deleteAny(User $user): bool
    {
        return $user->can('DeleteAny:ItemPrice');
    }

    /*
    |--------------------------------------------------------------------------
    | Restore
    |--------------------------------------------------------------------------
    */

    public function restore(User $user, ItemPrice $record): bool
    {
        return $user->can('Restore:ItemPrice');
    }

    public function restoreAny(User $user): bool
    {
        return $user->can('RestoreAny:ItemPrice');
    }

    /*
    |--------------------------------------------------------------------------
    | Force Delete
    |--------------------------------------------------------------------------
    */

    public function forceDelete(User $user, ItemPrice $record): bool
    {
        return $user->can('ForceDelete:ItemPrice');
    }

    public function forceDeleteAny(User $user): bool
    {
        return $user->can('ForceDeleteAny:ItemPrice');
    }

    /*
    |--------------------------------------------------------------------------
    | Replicate
    |--------------------------------------------------------------------------
    */

    public function replicate(User $user, ItemPrice $record): bool
    {
        return $user->can('Replicate:ItemPrice');
    }

    /*
    |--------------------------------------------------------------------------
    | Reorder
    |--------------------------------------------------------------------------
    */

    public function reorder(User $user): bool
    {
        return $user->can('Reorder:ItemPrice');
    }
}