<?php

declare(strict_types=1);

namespace App\Policies;

use App\Models\Uom;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class UomPolicy
{
    use HandlesAuthorization;

    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        return $user->can('ViewAny:Uom');
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, Uom $uom): bool
    {
        return $user->can('View:Uom');
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        return $user->can('Create:Uom');
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, Uom $uom): bool
    {
        return $user->can('Update:Uom');
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, Uom $uom): bool
    {
        return $user->can('Delete:Uom');
    }

    /**
     * Determine whether the user can delete any models.
     */
    public function deleteAny(User $user): bool
    {
        return $user->can('DeleteAny:Uom');
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, Uom $uom): bool
    {
        return $user->can('Restore:Uom');
    }

    /**
     * Determine whether the user can restore any models.
     */
    public function restoreAny(User $user): bool
    {
        return $user->can('RestoreAny:Uom');
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, Uom $uom): bool
    {
        return $user->can('ForceDelete:Uom');
    }

    /**
     * Determine whether the user can permanently delete any models.
     */
    public function forceDeleteAny(User $user): bool
    {
        return $user->can('ForceDeleteAny:Uom');
    }

    /**
     * Determine whether the user can replicate the model.
     */
    public function replicate(User $user, Uom $uom): bool
    {
        return $user->can('Replicate:Uom');
    }

    /**
     * Determine whether the user can reorder models.
     */
    public function reorder(User $user): bool
    {
        return $user->can('Reorder:Uom');
    }
}

