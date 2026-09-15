<?php

declare(strict_types=1);

namespace App\Policies;

use App\Models\ShippingAddress;
use App\Models\User;

class ShippingAddressPolicy
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
        return $user->can('ViewAny:ShippingAddress');
    }

    public function view(User $user, ShippingAddress $shippingAddress): bool
    {
        return $user->can('View:ShippingAddress');
    }

    public function create(User $user): bool
    {
        return $user->can('Create:ShippingAddress');
    }

    public function update(User $user, ShippingAddress $shippingAddress): bool
    {
        return $user->can('Update:ShippingAddress');
    }

    public function delete(User $user, ShippingAddress $shippingAddress): bool
    {
        return $user->can('Delete:ShippingAddress');
    }

    public function restore(User $user, ShippingAddress $shippingAddress): bool
    {
        return $user->can('Restore:ShippingAddress');
    }

    public function forceDelete(User $user, ShippingAddress $shippingAddress): bool
    {
        return $user->can('ForceDelete:ShippingAddress');
    }

    public function deleteAny(User $user): bool
    {
        return $user->can('DeleteAny:ShippingAddress');
    }

    public function restoreAny(User $user): bool
    {
        return $user->can('RestoreAny:ShippingAddress');
    }

    public function forceDeleteAny(User $user): bool
    {
        return $user->can('ForceDeleteAny:ShippingAddress');
    }

    public function replicate(User $user, ShippingAddress $shippingAddress): bool
    {
        return $user->can('Replicate:ShippingAddress');
    }

    public function reorder(User $user): bool
    {
        return $user->can('Reorder:ShippingAddress');
    }
}
