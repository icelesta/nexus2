<?php

declare(strict_types=1);

namespace App\Policies;

use App\Models\AssignmentDirectMarket;
use App\Models\User;

class AssignmentDirectMarketPolicy
{
    public function before(User $user, string $ability): ?bool
    {
        if ($user->isSuperAdmin()) {
            return true;
        }

        return null;
    }

    public function viewAny(User $user): bool
    {
        return $user->can('ViewAny:AssignmentDirectMarket');
    }

    public function view(User $user, AssignmentDirectMarket $model): bool
    {
        return $user->can('View:AssignmentDirectMarket');
    }

    public function create(User $user): bool
    {
        return $user->can('Create:AssignmentDirectMarket');
    }

    public function update(User $user, AssignmentDirectMarket $model): bool
    {
        return $user->can('Update:AssignmentDirectMarket')
            && in_array(
                $model->status,
                [
                    AssignmentDirectMarket::STATUS_DRAFT,
                    AssignmentDirectMarket::STATUS_UPDATED,
                ],
                true,
            );
    }

    public function delete(User $user, AssignmentDirectMarket $model): bool
    {
        return $user->can('Delete:AssignmentDirectMarket')
            && $model->status === AssignmentDirectMarket::STATUS_DRAFT;
    }

    public function restore(User $user, AssignmentDirectMarket $model): bool
    {
        return $user->can('Restore:AssignmentDirectMarket');
    }

    public function forceDelete(User $user, AssignmentDirectMarket $model): bool
    {
        return $user->can('ForceDelete:AssignmentDirectMarket');
    }

    public function submit(User $user, AssignmentDirectMarket $model): bool
    {
        return in_array(
            $model->status,
            [
                AssignmentDirectMarket::STATUS_DRAFT,
                AssignmentDirectMarket::STATUS_UPDATED,
            ],
            true,
        );
    }

    public function approve(User $user, AssignmentDirectMarket $model): bool
    {
        return $model->status === AssignmentDirectMarket::STATUS_WAITING_APPROVAL;
    }

    public function reject(User $user, AssignmentDirectMarket $model): bool
    {
        return $model->status === AssignmentDirectMarket::STATUS_WAITING_APPROVAL;
    }
}
