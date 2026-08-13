<?php

declare(strict_types=1);

namespace App\Policies;

use App\Models\ItemImage;
use App\Models\User;

use Illuminate\Auth\Access\HandlesAuthorization;

class ItemImagePolicy
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


    /**
     * Determine whether the user can view any item images.
     */

    public function viewAny(User $user): bool
    {
        return $user->can('ViewAny:ItemImage');
    }



    public function view(User $user, ItemImage $record): bool
    {
        return $user->can('View:ItemImage');
    }

    /*
    |--------------------------------------------------------------------------
    | Create
    |--------------------------------------------------------------------------
    */

    public function create(User $user): bool
    {
        return $user->can('Create:ItemImage');
    }

    /*
    |--------------------------------------------------------------------------
    | Update
    |--------------------------------------------------------------------------
    */

    public function update(User $user, ItemImage $record): bool
    {
        return $user->can('Update:ItemImage');
    }

    /*
    |--------------------------------------------------------------------------
    | Delete
    |--------------------------------------------------------------------------
    */

    public function delete(User $user, ItemImage $record): bool
    {
        return $user->can('Delete:ItemImage');
    }

    public function deleteAny(User $user): bool
    {
        return $user->can('DeleteAny:ItemImage');
    }

    /*
    |--------------------------------------------------------------------------
    | Restore
    |--------------------------------------------------------------------------
    */

    public function restore(User $user, ItemImage $record): bool
    {
        return $user->can('Restore:ItemImage');
    }

    public function restoreAny(User $user): bool
    {
        return $user->can('RestoreAny:ItemImage');
    }

    /*
    |--------------------------------------------------------------------------
    | Force Delete
    |--------------------------------------------------------------------------
    */

    public function forceDelete(User $user, ItemImage $record): bool
    {
        return $user->can('ForceDelete:ItemImage');
    }

    public function forceDeleteAny(User $user): bool
    {
        return $user->can('ForceDeleteAny:ItemImage');
    }

    /*
    |--------------------------------------------------------------------------
    | Replicate
    |--------------------------------------------------------------------------
    */

    public function replicate(User $user, ItemImage $record): bool
    {
        return $user->can('Replicate:ItemImage');
    }

    /*
    |--------------------------------------------------------------------------
    | Reorder
    |--------------------------------------------------------------------------
    */

    public function reorder(User $user): bool
    {
        return $user->can('Reorder:ItemImage');
    }

    /*
    |--------------------------------------------------------------------------
    | Future Business Rules
    |--------------------------------------------------------------------------
    |
    | Upload Approval
    | Download Original
    | Watermark Permission
    | Image Verification
    |
    */

}