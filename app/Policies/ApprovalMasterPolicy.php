<?php

declare(strict_types=1);

namespace App\Policies;

use App\Models\ApprovalMaster;
use App\Models\User;

class ApprovalMasterPolicy
{
    public function before(User $user, string $ability): ?bool
    {
        if ($user->isSuperAdmin()) {
            return true;
        }

        return null;
    }

    public function viewAny(User $user): bool { return $user->can('ViewAny:ApprovalMaster'); }
    public function view(User $user, ApprovalMaster $model): bool { return $user->can('View:ApprovalMaster'); }
    public function create(User $user): bool { return $user->can('Create:ApprovalMaster'); }
    public function update(User $user, ApprovalMaster $model): bool { return $user->can('Update:ApprovalMaster'); }
    public function delete(User $user, ApprovalMaster $model): bool { return $user->can('Delete:ApprovalMaster'); }
    public function deleteAny(User $user): bool { return $user->can('DeleteAny:ApprovalMaster'); }
    public function restore(User $user, ApprovalMaster $model): bool { return $user->can('Restore:ApprovalMaster'); }
    public function restoreAny(User $user): bool { return $user->can('RestoreAny:ApprovalMaster'); }
    public function forceDelete(User $user, ApprovalMaster $model): bool { return $user->can('ForceDelete:ApprovalMaster'); }
    public function forceDeleteAny(User $user): bool { return $user->can('ForceDeleteAny:ApprovalMaster'); }
    public function replicate(User $user, ApprovalMaster $model): bool { return $user->can('Replicate:ApprovalMaster'); }
    public function reorder(User $user): bool { return $user->can('Reorder:ApprovalMaster'); }
}
