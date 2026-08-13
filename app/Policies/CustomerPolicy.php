<?php

declare(strict_types=1);

namespace App\Policies;

use App\Models\Customer;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class CustomerPolicy
{
    use HandlesAuthorization;

    /*
    |--------------------------------------------------------------------------
    | Basic Permissions
    |--------------------------------------------------------------------------
    */

    public function viewAny(User $user): bool
    {
        return $user->can('ViewAny:Customer');
    }

    public function view(User $user, Customer $customer): bool
    {
        return $user->can('View:Customer');
    }

    public function create(User $user): bool
    {
        return $user->can('Create:Customer');
    }

    public function update(User $user, Customer $customer): bool
    {
        return $user->can('Update:Customer');
    }

    public function delete(User $user, Customer $customer): bool
    {
        return $user->can('Delete:Customer');
    }

    /*
    |--------------------------------------------------------------------------
    | Bulk Permissions
    |--------------------------------------------------------------------------
    */

    public function deleteAny(User $user): bool
    {
        return $user->can('DeleteAny:Customer');
    }

    public function restoreAny(User $user): bool
    {
        return $user->can('RestoreAny:Customer');
    }

    public function forceDeleteAny(User $user): bool
    {
        return $user->can('ForceDeleteAny:Customer');
    }

    /*
    |--------------------------------------------------------------------------
    | Advanced Permissions
    |--------------------------------------------------------------------------
    */

    public function restore(User $user, Customer $customer): bool
    {
        return $user->can('Restore:Customer');
    }

    public function forceDelete(User $user, Customer $customer): bool
    {
        return $user->can('ForceDelete:Customer');
    }

    public function replicate(User $user, Customer $customer): bool
    {
        return $user->can('Replicate:Customer');
    }

    public function reorder(User $user): bool
    {
        return $user->can('Reorder:Customer');
    }
}