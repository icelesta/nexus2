<?php

declare(strict_types=1);

namespace App\Policies;

use App\Models\ShippingAddress;
use App\Models\User;

class ShippingAddressPolicy
{
    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        return $this->checkPermission(
            $user,
            'shipping-address.view-any',
        );
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(
        User $user,
        ShippingAddress $shippingAddress,
    ): bool {
        return $this->checkPermission(
            $user,
            'shipping-address.view',
        );
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        return $this->checkPermission(
            $user,
            'shipping-address.create',
        );
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(
        User $user,
        ShippingAddress $shippingAddress,
    ): bool {
        return $this->checkPermission(
            $user,
            'shipping-address.update',
        );
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(
        User $user,
        ShippingAddress $shippingAddress,
    ): bool {
        return $this->checkPermission(
            $user,
            'shipping-address.delete',
        );
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(
        User $user,
        ShippingAddress $shippingAddress,
    ): bool {
        return $this->checkPermission(
            $user,
            'shipping-address.restore',
        );
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(
        User $user,
        ShippingAddress $shippingAddress,
    ): bool {
        return $this->checkPermission(
            $user,
            'shipping-address.force-delete',
        );
    }

    /**
     * Check permission.
     *
     * During ERP development we allow access if the
     * permission engine has not yet been implemented.
     *
     * Once the Security Module is completed,
     * replace this implementation with the final
     * authorization logic.
     */
    protected function checkPermission(
        User $user,
        string $permission,
    ): bool {

        /**
         * Future implementation.
         *
         * return $user->can($permission);
         */

        return true;
    }
}