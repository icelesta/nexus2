<?php

declare(strict_types=1);

namespace App\Policies;

use App\Models\GoodsReceipt;
use App\Models\User;

class GoodsReceiptPolicy
{
    public function before(User $user, string $ability): ?bool
    {
        if ($user->isSuperAdmin()) {
            return true;
        }

        return null;
    }

    public function viewAny(User $user): bool { return $user->can('ViewAny:GoodsReceipt'); }
    public function view(User $user, GoodsReceipt $model): bool { return $user->can('View:GoodsReceipt'); }
    public function create(User $user): bool { return $user->can('Create:GoodsReceipt'); }
    public function update(User $user, GoodsReceipt $model): bool { return $user->can('Update:GoodsReceipt'); }
    public function delete(User $user, GoodsReceipt $model): bool { return $user->can('Delete:GoodsReceipt'); }
    public function deleteAny(User $user): bool { return $user->can('DeleteAny:GoodsReceipt'); }
    public function restore(User $user, GoodsReceipt $model): bool { return $user->can('Restore:GoodsReceipt'); }
    public function restoreAny(User $user): bool { return $user->can('RestoreAny:GoodsReceipt'); }
    public function forceDelete(User $user, GoodsReceipt $model): bool { return $user->can('ForceDelete:GoodsReceipt'); }
    public function forceDeleteAny(User $user): bool { return $user->can('ForceDeleteAny:GoodsReceipt'); }
    public function replicate(User $user, GoodsReceipt $model): bool { return $user->can('Replicate:GoodsReceipt'); }
    public function reorder(User $user): bool { return $user->can('Reorder:GoodsReceipt'); }
}
