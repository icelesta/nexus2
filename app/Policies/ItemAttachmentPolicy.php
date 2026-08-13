<?php

declare(strict_types=1);

namespace App\Policies;

use App\Models\ItemAttachment;
use App\Models\User;

use Illuminate\Auth\Access\HandlesAuthorization;

class ItemAttachmentPolicy
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
        return $user->can('ViewAny:ItemAttachment');
    }

    public function view(User $user, ItemAttachment $record): bool
    {
        return $user->can('View:ItemAttachment');
    }

    /*
    |--------------------------------------------------------------------------
    | Create
    |--------------------------------------------------------------------------
    */

    public function create(User $user): bool
    {
        return $user->can('Create:ItemAttachment');
    }

    /*
    |--------------------------------------------------------------------------
    | Update
    |--------------------------------------------------------------------------
    */

    public function update(User $user, ItemAttachment $record): bool
    {
        return $user->can('Update:ItemAttachment');
    }

    /*
    |--------------------------------------------------------------------------
    | Delete
    |--------------------------------------------------------------------------
    */

    public function delete(User $user, ItemAttachment $record): bool
    {
        return $user->can('Delete:ItemAttachment');
    }

    public function deleteAny(User $user): bool
    {
        return $user->can('DeleteAny:ItemAttachment');
    }

    /*
    |--------------------------------------------------------------------------
    | Restore
    |--------------------------------------------------------------------------
    */

    public function restore(User $user, ItemAttachment $record): bool
    {
        return $user->can('Restore:ItemAttachment');
    }

    public function restoreAny(User $user): bool
    {
        return $user->can('RestoreAny:ItemAttachment');
    }

    /*
    |--------------------------------------------------------------------------
    | Force Delete
    |--------------------------------------------------------------------------
    */

    public function forceDelete(User $user, ItemAttachment $record): bool
    {
        return $user->can('ForceDelete:ItemAttachment');
    }

    public function forceDeleteAny(User $user): bool
    {
        return $user->can('ForceDeleteAny:ItemAttachment');
    }

    /*
    |--------------------------------------------------------------------------
    | Replicate
    |--------------------------------------------------------------------------
    */

    public function replicate(User $user, ItemAttachment $record): bool
    {
        return $user->can('Replicate:ItemAttachment');
    }

    /*
    |--------------------------------------------------------------------------
    | Reorder
    |--------------------------------------------------------------------------
    */

    public function reorder(User $user): bool
    {
        return $user->can('Reorder:ItemAttachment');
    }
}