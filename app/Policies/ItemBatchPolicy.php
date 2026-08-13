<?php

declare(strict_types=1);

namespace App\Policies;

use App\Models\ItemBatch;
use App\Models\User;

use Illuminate\Auth\Access\HandlesAuthorization;

class ItemBatchPolicy
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
        return $user->can('ViewAny:ItemBatch');
    }

    public function view(User $user, ItemBatch $record): bool
    {
        return $user->can('View:ItemBatch');
    }

    /*
    |--------------------------------------------------------------------------
    | Create
    |--------------------------------------------------------------------------
    */

    public function create(User $user): bool
    {
        return $user->can('Create:ItemBatch');
    }

    /*
    |--------------------------------------------------------------------------
    | Update
    |--------------------------------------------------------------------------
    */

    public function update(User $user, ItemBatch $record): bool
    {
        return $user->can('Update:ItemBatch');
    }

    /*
    |--------------------------------------------------------------------------
    | Delete
    |--------------------------------------------------------------------------
    */

    public function delete(User $user, ItemBatch $record): bool
    {
        return $user->can('Delete:ItemBatch');
    }

    public function deleteAny(User $user): bool
    {
        return $user->can('DeleteAny:ItemBatch');
    }

    /*
    |--------------------------------------------------------------------------
    | Restore
    |--------------------------------------------------------------------------
    */

    public function restore(User $user, ItemBatch $record): bool
    {
        return $user->can('Restore:ItemBatch');
    }

    public function restoreAny(User $user): bool
    {
        return $user->can('RestoreAny:ItemBatch');
    }

    /*
    |--------------------------------------------------------------------------
    | Force Delete
    |--------------------------------------------------------------------------
    */

    public function forceDelete(User $user, ItemBatch $record): bool
    {
        return $user->can('ForceDelete:ItemBatch');
    }

    public function forceDeleteAny(User $user): bool
    {
        return $user->can('ForceDeleteAny:ItemBatch');
    }

    /*
    |--------------------------------------------------------------------------
    | Replicate
    |--------------------------------------------------------------------------
    */

    public function replicate(User $user, ItemBatch $record): bool
    {
        return $user->can('Replicate:ItemBatch');
    }

    /*
    |--------------------------------------------------------------------------
    | Reorder
    |--------------------------------------------------------------------------
    */

    public function reorder(User $user): bool
    {
        return $user->can('Reorder:ItemBatch');
    }
}