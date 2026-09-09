<?php

declare(strict_types=1);

namespace App\Policies;

use App\Models\AssignmentDirectMarket;
use App\Models\User;

class AssignmentDirectMarketPolicy
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
        return true;
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(
        User $user,
        AssignmentDirectMarket $model,
    ): bool {
        return true;
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        return true;
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(
        User $user,
        AssignmentDirectMarket $model,
    ): bool {
        return in_array(
            $model->status,
            [
                AssignmentDirectMarket::STATUS_DRAFT,
                AssignmentDirectMarket::STATUS_UPDATED,
            ],
            true,
        );
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(
        User $user,
        AssignmentDirectMarket $model,
    ): bool {
        return $model->status === AssignmentDirectMarket::STATUS_DRAFT;
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(
        User $user,
        AssignmentDirectMarket $model,
    ): bool {
        return false;
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(
        User $user,
        AssignmentDirectMarket $model,
    ): bool {
        return false;
    }

    /**
     * Determine whether the user can submit the document.
     */
    public function submit(
        User $user,
        AssignmentDirectMarket $model,
    ): bool {
        return in_array(
            $model->status,
            [
                AssignmentDirectMarket::STATUS_DRAFT,
                AssignmentDirectMarket::STATUS_UPDATED,
            ],
            true,
        );
    }

    /**
     * Determine whether the user can approve the document.
     */
    public function approve(
        User $user,
        AssignmentDirectMarket $model,
    ): bool {
        return $model->status === AssignmentDirectMarket::STATUS_WAITING_APPROVAL;
    }

    /**
     * Determine whether the user can reject the document.
     */
    public function reject(
        User $user,
        AssignmentDirectMarket $model,
    ): bool {
        return $model->status === AssignmentDirectMarket::STATUS_WAITING_APPROVAL;
    }
}