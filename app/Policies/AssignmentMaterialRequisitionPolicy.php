<?php

declare(strict_types=1);

namespace App\Policies;

use App\Models\AssignmentMaterialRequisition;
use App\Models\User;

class AssignmentMaterialRequisitionPolicy
{
    /**
     * Super Administrator bypass.
     */
    public function before(User $user, string $ability): ?bool
    {
        if ($user->isSuperAdmin()) {
            return true;
        }

        return null;
    }

    public function viewAny(User $user): bool
    {
        return $user->can('ViewAny:AssignmentMaterialRequisition');
    }

    public function view(User $user, AssignmentMaterialRequisition $model): bool
    {
        return $user->can('View:AssignmentMaterialRequisition');
    }

    public function create(User $user): bool
    {
        return $user->can('Create:AssignmentMaterialRequisition');
    }

    public function update(User $user, AssignmentMaterialRequisition $model): bool
    {
        return $user->can('Update:AssignmentMaterialRequisition')
            && in_array(
                $model->status,
                [
                    AssignmentMaterialRequisition::STATUS_DRAFT,
                    AssignmentMaterialRequisition::STATUS_UPDATED,
                    AssignmentMaterialRequisition::STATUS_ASSIGNED,
                ],
                true,
            );
    }

    public function delete(User $user, AssignmentMaterialRequisition $model): bool
    {
        return $user->can('Delete:AssignmentMaterialRequisition')
            && $model->status === AssignmentMaterialRequisition::STATUS_DRAFT;
    }

    public function restore(User $user, AssignmentMaterialRequisition $model): bool
    {
        return $user->can('Restore:AssignmentMaterialRequisition');
    }

    public function forceDelete(User $user, AssignmentMaterialRequisition $model): bool
    {
        return $user->can('ForceDelete:AssignmentMaterialRequisition');
    }

    public function submit(User $user, AssignmentMaterialRequisition $model): bool
    {
        return $user->can('Update:AssignmentMaterialRequisition')
            && $model->status === AssignmentMaterialRequisition::STATUS_UPDATED;
    }

    public function requestApproval(User $user, AssignmentMaterialRequisition $model): bool
    {
        return $user->can('Update:AssignmentMaterialRequisition')
            && $model->status === AssignmentMaterialRequisition::STATUS_ASSIGNED;
    }

    public function cancel(User $user, AssignmentMaterialRequisition $model): bool
    {
        return $user->can('Update:AssignmentMaterialRequisition')
            && in_array(
                $model->status,
                [
                    AssignmentMaterialRequisition::STATUS_DRAFT,
                    AssignmentMaterialRequisition::STATUS_ASSIGNED,
                ],
                true,
            );
    }

    public function approve(User $user, AssignmentMaterialRequisition $model): bool
    {
        return $model->status === AssignmentMaterialRequisition::STATUS_WAITING_APPROVAL;
    }

    public function reject(User $user, AssignmentMaterialRequisition $model): bool
    {
        return $model->status === AssignmentMaterialRequisition::STATUS_WAITING_APPROVAL;
    }

    public function complete(User $user, AssignmentMaterialRequisition $model): bool
    {
        return $user->can('Update:AssignmentMaterialRequisition')
            && $model->status === AssignmentMaterialRequisition::STATUS_APPROVED;
    }
}
