<?php

declare(strict_types=1);

namespace App\Policies;

use App\Models\BankAccount;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class BankAccountPolicy
{
    use HandlesAuthorization;

    /*
    |--------------------------------------------------------------------------
    | Basic Permissions
    |--------------------------------------------------------------------------
    */

    public function viewAny(User $user): bool
    {
        return $user->can('ViewAny:BankAccount');
    }

    public function view(User $user, BankAccount $bankAccount): bool
    {
        return $user->can('View:BankAccount');
    }

    public function create(User $user): bool
    {
        return $user->can('Create:BankAccount');
    }

    public function update(User $user, BankAccount $bankAccount): bool
    {
        return $user->can('Update:BankAccount');
    }

    public function delete(User $user, BankAccount $bankAccount): bool
    {
        return $user->can('Delete:BankAccount');
    }

    /*
    |--------------------------------------------------------------------------
    | Bulk Permissions
    |--------------------------------------------------------------------------
    */

    public function deleteAny(User $user): bool
    {
        return $user->can('DeleteAny:BankAccount');
    }

    public function restoreAny(User $user): bool
    {
        return $user->can('RestoreAny:BankAccount');
    }

    public function forceDeleteAny(User $user): bool
    {
        return $user->can('ForceDeleteAny:BankAccount');
    }

    /*
    |--------------------------------------------------------------------------
    | Advanced Permissions
    |--------------------------------------------------------------------------
    */

    public function restore(User $user, BankAccount $bankAccount): bool
    {
        return $user->can('Restore:BankAccount');
    }

    public function forceDelete(User $user, BankAccount $bankAccount): bool
    {
        return $user->can('ForceDelete:BankAccount');
    }

    public function replicate(User $user, BankAccount $bankAccount): bool
    {
        return $user->can('Replicate:BankAccount');
    }

    public function reorder(User $user): bool
    {
        return $user->can('Reorder:BankAccount');
    }
}