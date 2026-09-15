<?php

declare(strict_types=1);

namespace App\Policies;

use App\Models\PurchaseOrder;
use App\Models\User;

class PurchaseOrderPolicy
{
    public function before(User $user, string $ability): ?bool
    {
        if ($user->isSuperAdmin()) {
            return true;
        }

        return null;
    }

    public function viewAny(User $user): bool { return $user->can('ViewAny:PurchaseOrder'); }
    public function view(User $user, PurchaseOrder $model): bool { return $user->can('View:PurchaseOrder'); }
    public function create(User $user): bool { return $user->can('Create:PurchaseOrder'); }
    public function update(User $user, PurchaseOrder $model): bool { return $user->can('Update:PurchaseOrder'); }
    public function delete(User $user, PurchaseOrder $model): bool { return $user->can('Delete:PurchaseOrder'); }
    public function deleteAny(User $user): bool { return $user->can('DeleteAny:PurchaseOrder'); }
    public function restore(User $user, PurchaseOrder $model): bool { return $user->can('Restore:PurchaseOrder'); }
    public function restoreAny(User $user): bool { return $user->can('RestoreAny:PurchaseOrder'); }
    public function forceDelete(User $user, PurchaseOrder $model): bool { return $user->can('ForceDelete:PurchaseOrder'); }
    public function forceDeleteAny(User $user): bool { return $user->can('ForceDeleteAny:PurchaseOrder'); }
    public function replicate(User $user, PurchaseOrder $model): bool { return $user->can('Replicate:PurchaseOrder'); }
    public function reorder(User $user): bool { return $user->can('Reorder:PurchaseOrder'); }
}
