<?php

declare(strict_types=1);

namespace App\Policies;

use App\Models\AssignmentMaterialRequisitionItem;
use App\Models\User;

class AssignmentMaterialRequisitionItemPolicy
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
        return $user->can('ViewAny:AssignmentMaterialRequisitionItem');
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(
        User $user,
        AssignmentMaterialRequisitionItem $model,
    ): bool {
        return $user->can('View:AssignmentMaterialRequisitionItem');
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        return $user->can('Create:AssignmentMaterialRequisitionItem');
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(
        User $user,
        AssignmentMaterialRequisitionItem $model,
    ): bool {
        return $user->can('Update:AssignmentMaterialRequisitionItem')
            && in_array(
                $model->status,
                [
                    AssignmentMaterialRequisitionItem::STATUS_DRAFT,
                    AssignmentMaterialRequisitionItem::STATUS_ASSIGNED,
                ],
                true,
            );
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(
        User $user,
        AssignmentMaterialRequisitionItem $model,
    ): bool {
        return $user->can('Delete:AssignmentMaterialRequisitionItem')
            && $model->status === AssignmentMaterialRequisitionItem::STATUS_DRAFT;
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(
        User $user,
        AssignmentMaterialRequisitionItem $model,
    ): bool {
        return false;
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(
        User $user,
        AssignmentMaterialRequisitionItem $model,
    ): bool {
        return false;
    }

    public function deleteAny(User $user): bool
    {
        return $user->can('DeleteAny:AssignmentMaterialRequisitionItem');
    }

    public function forceDeleteAny(User $user): bool
    {
        return $user->can('ForceDeleteAny:AssignmentMaterialRequisitionItem');
    }

    public function restoreAny(User $user): bool
    {
        return $user->can('RestoreAny:AssignmentMaterialRequisitionItem');
    }

    public function replicate(
        User $user,
        AssignmentMaterialRequisitionItem $model,
    ): bool {
        return $user->can('Replicate:AssignmentMaterialRequisitionItem');
    }

    public function reorder(User $user): bool
    {
        return $user->can('Reorder:AssignmentMaterialRequisitionItem');
    }

    /**
     * Determine whether the user can assign a supplier.
     */
    public function assignSupplier(
        User $user,
        AssignmentMaterialRequisitionItem $model,
    ): bool {
        return $model->status === AssignmentMaterialRequisitionItem::STATUS_ASSIGNED;
    }

    /**
     * Determine whether the user can approve the item.
     */
    public function approve(
        User $user,
        AssignmentMaterialRequisitionItem $model,
    ): bool {
        return $model->status === AssignmentMaterialRequisitionItem::STATUS_WAITING_APPROVAL;
    }

    /**
     * Determine whether the user can reject the item.
     */
    public function reject(
        User $user,
        AssignmentMaterialRequisitionItem $model,
    ): bool {
        return $model->status === AssignmentMaterialRequisitionItem::STATUS_WAITING_APPROVAL;
    }
}