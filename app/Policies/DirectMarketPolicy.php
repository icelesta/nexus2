<?php

declare(strict_types=1);

namespace App\Policies;

use App\Models\DirectMarket;
use App\Models\User;

class DirectMarketPolicy
{
    /**
     * Super Administrator bypass.
     */
    public function before(
        User $user,
        string $ability,
    ): ?bool {
        if ($user->isSuperAdmin()) {
            return true;
        }

        return null;
    }

    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        return $user->can('ViewAny:DirectMarket');
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(
        User $user,
        DirectMarket $model,
    ): bool {
        return $user->can('View:DirectMarket');
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        return $user->can('Create:DirectMarket');
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(
        User $user,
        DirectMarket $model,
    ): bool {
        return $user->can('Update:DirectMarket');
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(
        User $user,
        DirectMarket $model,
    ): bool {
        return $user->can('Delete:DirectMarket');
    }

    /**
     * Determine whether the user can delete any models.
     */
    public function deleteAny(User $user): bool
    {
        return $user->can('DeleteAny:DirectMarket');
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(
        User $user,
        DirectMarket $model,
    ): bool {
        return $user->can('Restore:DirectMarket');
    }

    /**
     * Determine whether the user can restore any models.
     */
    public function restoreAny(User $user): bool
    {
        return $user->can('RestoreAny:DirectMarket');
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(
        User $user,
        DirectMarket $model,
    ): bool {
        return $user->can('ForceDelete:DirectMarket');
    }

    /**
     * Determine whether the user can permanently delete any models.
     */
    public function forceDeleteAny(User $user): bool
    {
        return $user->can('ForceDeleteAny:DirectMarket');
    }

    /**
     * Determine whether the user can replicate the model.
     */
    public function replicate(
        User $user,
        DirectMarket $model,
    ): bool {
        return $user->can('Replicate:DirectMarket');
    }

    /**
     * Determine whether the user can reorder models.
     */
    public function reorder(User $user): bool
    {
        return $user->can('Reorder:DirectMarket');
    }

    /*
    |--------------------------------------------------------------------------
    | Direct Market Workflow
    |--------------------------------------------------------------------------
    */

    /**
     * Determine whether the user can submit the Direct Market.
     */
    public function submit(
        User $user,
        DirectMarket $model,
    ): bool {
        return $user->can('Submit:DirectMarket');
    }
}