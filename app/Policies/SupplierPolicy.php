<?php

declare(strict_types=1);

namespace App\Policies;

use App\Models\Supplier;
use App\Models\User;

use Illuminate\Auth\Access\HandlesAuthorization;

class SupplierPolicy
{
    use HandlesAuthorization;

    /*
    |--------------------------------------------------------------------------
    | View Permissions
    |--------------------------------------------------------------------------
    */

    /**
     * Determine whether the user can view any suppliers.
     */
    public function viewAny(
        User $user,
    ): bool {

        return $user->can(
            'ViewAny:Supplier'
        );
    }

    /**
     * Determine whether the user can view the supplier.
     */
    public function view(
        User $user,
        Supplier $supplier,
    ): bool {

        /*
        |--------------------------------------------------------------------------
        | Permission Check
        |--------------------------------------------------------------------------
        */

        if (! $user->can(
            'View:Supplier'
        )) {

            return false;

        }

        /*
        |--------------------------------------------------------------------------
        | Business Rules
        |--------------------------------------------------------------------------
        |
        | Reserved for future business validation.
        | Example:
        | - Multi Company Access
        | - Branch Restriction
        | - Department Restriction
        |
        */

        return true;
    }


    /*
    |--------------------------------------------------------------------------
    | Create Permissions
    |--------------------------------------------------------------------------
    */

    /**
     * Determine whether the user can create suppliers.
     */
    public function create(
        User $user,
    ): bool {

        /*
        |--------------------------------------------------------------------------
        | Permission Check
        |--------------------------------------------------------------------------
        */

        if (! $user->can(
            'Create:Supplier'
        )) {

            return false;

        }

        /*
        |--------------------------------------------------------------------------
        | Business Rules
        |--------------------------------------------------------------------------
        |
        | Reserved for future business validation.
        |
        | Example:
        | - Company is active.
        | - Procurement period is open.
        | - User belongs to the selected company.
        | - User account is active.
        |
        */

        return true;
    }


    /*
    |--------------------------------------------------------------------------
    | Update Permissions
    |--------------------------------------------------------------------------
    */

    /**
     * Determine whether the user can update the supplier.
     */
    public function update(
        User $user,
        Supplier $supplier,
    ): bool {

        /*
        |--------------------------------------------------------------------------
        | Permission Check
        |--------------------------------------------------------------------------
        */

        if (! $user->can(
            'Update:Supplier'
        )) {

            return false;

        }

        /*
        |--------------------------------------------------------------------------
        | Business Rules
        |--------------------------------------------------------------------------
        |
        | Reserved for future business validation.
        |
        | Example:
        | - Supplier is not locked.
        | - Supplier is not archived.
        | - Supplier belongs to user's company.
        |
        */

        return true;
    }


    /*
    |--------------------------------------------------------------------------
    | Delete Permissions
    |--------------------------------------------------------------------------
    */

    /**
     * Determine whether the user can delete the supplier.
     */
    public function delete(
        User $user,
        Supplier $supplier,
    ): bool {

        /*
        |--------------------------------------------------------------------------
        | Permission Check
        |--------------------------------------------------------------------------
        */

        if (! $user->can(
            'Delete:Supplier'
        )) {

            return false;

        }

        /*
        |--------------------------------------------------------------------------
        | Business Rules
        |--------------------------------------------------------------------------
        |
        | Reserved for future business validation.
        |
        | Example:
        | - Supplier has Purchase Orders.
        | - Supplier has Goods Receipt Notes.
        | - Supplier has Vendor Bills.
        | - Supplier has Outstanding Balance.
        |
        */

        return true;
    }

    /*
    |--------------------------------------------------------------------------
    | Restore Permissions
    |--------------------------------------------------------------------------
    */

    /**
     * Determine whether the user can restore the supplier.
     */
    public function restore(
        User $user,
        Supplier $supplier,
    ): bool {

        /*
        |--------------------------------------------------------------------------
        | Permission Check
        |--------------------------------------------------------------------------
        */

        if (! $user->can(
            'Restore:Supplier'
        )) {

            return false;

        }

        /*
        |--------------------------------------------------------------------------
        | Business Rules
        |--------------------------------------------------------------------------
        |
        | Reserved for future business validation.
        */

        return true;
    }

    /*
    |--------------------------------------------------------------------------
    | Force Delete Permissions
    |--------------------------------------------------------------------------
    */

    /**
     * Determine whether the user can permanently delete the supplier.
     */
    public function forceDelete(
        User $user,
        Supplier $supplier,
    ): bool {

        /*
        |--------------------------------------------------------------------------
        | Permission Check
        |--------------------------------------------------------------------------
        */

        if (! $user->can(
            'ForceDelete:Supplier'
        )) {

            return false;

        }

        /*
        |--------------------------------------------------------------------------
        | Business Rules
        |--------------------------------------------------------------------------
        |
        | Reserved for future business validation.
        |
        | Example:
        | - Supplier has no transaction history.
        | - Supplier has no audit records.
        |
        */

        return true;
    }

    /*
    |--------------------------------------------------------------------------
    | Replicate Permissions
    |--------------------------------------------------------------------------
    */

    /**
     * Determine whether the user can replicate the supplier.
     */
    public function replicate(
        User $user,
        Supplier $supplier,
    ): bool {

        /*
        |--------------------------------------------------------------------------
        | Permission Check
        |--------------------------------------------------------------------------
        */

        if (! $user->can(
            'Replicate:Supplier'
        )) {

            return false;

        }

        /*
        |--------------------------------------------------------------------------
        | Business Rules
        |--------------------------------------------------------------------------
        |
        | Reserved for future business validation.
        |
        | Example:
        | - Supplier is active.
        | - Supplier is not blacklisted.
        | - Supplier belongs to current company.
        |
        */

        return true;
    }

    /*
    |--------------------------------------------------------------------------
    | Bulk / Reorder Permissions
    |--------------------------------------------------------------------------
    */

    public function deleteAny(User $user): bool
    {
        return $user->can('DeleteAny:Supplier');
    }

    public function forceDeleteAny(User $user): bool
    {
        return $user->can('ForceDeleteAny:Supplier');
    }

    public function restoreAny(User $user): bool
    {
        return $user->can('RestoreAny:Supplier');
    }

    public function reorder(User $user): bool
    {
        return $user->can('Reorder:Supplier');
    }

    /*
    |--------------------------------------------------------------------------
    | Business Rules
    |--------------------------------------------------------------------------
    */

    /**
     * Determine whether the supplier has transactions.
     *
     * Reserved for future Purchasing Module.
     */
    protected function hasTransaction(
        Supplier $supplier,
    ): bool {

        /*
        |--------------------------------------------------------------------------
        | Future Implementation
        |--------------------------------------------------------------------------
        |
        | Purchase Order
        | Goods Receipt Note
        | Vendor Bill
        | Payment Voucher
        |
        */

        return false;
    }

    /**
     * Determine whether the supplier has outstanding balance.
     *
     * Reserved for future Accounting Module.
     */
    protected function hasOutstandingBalance(
        Supplier $supplier,
    ): bool {

        /*
        |--------------------------------------------------------------------------
        | Future Implementation
        |--------------------------------------------------------------------------
        */

        return false;
    }

    /**
     * Determine whether the supplier belongs to the user's company.
     *
     * Reserved for Multi Company.
     */
    protected function belongsToCompany(
        User $user,
        Supplier $supplier,
    ): bool {

        /*
        |--------------------------------------------------------------------------
        | Future Implementation
        |--------------------------------------------------------------------------
        */

        return true;
    }

    /*
    |--------------------------------------------------------------------------
    | Helper Methods
    |--------------------------------------------------------------------------
    */

    /**
     * Check whether the user can manage suppliers.
     */
    protected function canManage(
        User $user,
    ): bool {

        return

            $user->can('Create:Supplier')

            || $user->can('Update:Supplier');
    }

    /**
     * Check whether the user has administrative access.
     */
    protected function isAdministrator(
        User $user,
    ): bool {

        return

            $user->can('ForceDelete:Supplier');
    }


}