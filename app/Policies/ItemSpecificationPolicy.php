<?php

declare(strict_types=1);

namespace App\Policies;

use App\Models\ItemSpecification;
use App\Models\User;

use Illuminate\Auth\Access\HandlesAuthorization;

class ItemSpecificationPolicy
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
        return $user->can('ViewAny:ItemSpecification');
    }

    public function view(User $user, ItemSpecification $record): bool
    {
        return $user->can('View:ItemSpecification');
    }

    /*
    |--------------------------------------------------------------------------
    | Create
    |--------------------------------------------------------------------------
    */

    public function create(User $user): bool
    {
        return $user->can('Create:ItemSpecification');
    }

    /*
    |--------------------------------------------------------------------------
    | Update
    |--------------------------------------------------------------------------
    */

    public function update(User $user, ItemSpecification $record): bool
    {
        return $user->can('Update:ItemSpecification');
    }

    /*
    |--------------------------------------------------------------------------
    | Delete
    |--------------------------------------------------------------------------
    */

    public function delete(User $user, ItemSpecification $record): bool
    {
        return $user->can('Delete:ItemSpecification');
    }

    public function deleteAny(User $user): bool
    {
        return $user->can('DeleteAny:ItemSpecification');
    }

    /*
    |--------------------------------------------------------------------------
    | Restore
    |--------------------------------------------------------------------------
    */

    public function restore(User $user, ItemSpecification $record): bool
    {
        return $user->can('Restore:ItemSpecification');
    }

    public function restoreAny(User $user): bool
    {
        return $user->can('RestoreAny:ItemSpecification');
    }

    /*
    |--------------------------------------------------------------------------
    | Force Delete
    |--------------------------------------------------------------------------
    */

    public function forceDelete(User $user, ItemSpecification $record): bool
    {
        return $user->can('ForceDelete:ItemSpecification');
    }

    public function forceDeleteAny(User $user): bool
    {
        return $user->can('ForceDeleteAny:ItemSpecification');
    }

    /*
    |--------------------------------------------------------------------------
    | Replicate
    |--------------------------------------------------------------------------
    */

    public function replicate(User $user, ItemSpecification $record): bool
    {
        return $user->can('Replicate:ItemSpecification');
    }

    /*
    |--------------------------------------------------------------------------
    | Reorder
    |--------------------------------------------------------------------------
    */

    public function reorder(User $user): bool
    {
        return $user->can('Reorder:ItemSpecification');
    }
}