<?php

declare(strict_types=1);

namespace App\Policies;

use App\Models\TransactionNumbering;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class TransactionNumberingPolicy
{
    use HandlesAuthorization;

    /*
    |--------------------------------------------------------------------------
    | Basic Permissions
    |--------------------------------------------------------------------------
    */

    public function viewAny(User $user): bool
    {
        return $user->can('ViewAny:TransactionNumbering');
    }

    public function view(User $user, TransactionNumbering $transactionNumbering): bool
    {
        return $user->can('View:TransactionNumbering');
    }

    public function create(User $user): bool
    {
        return $user->can('Create:TransactionNumbering');
    }

    public function update(User $user, TransactionNumbering $transactionNumbering): bool
    {
        return $user->can('Update:TransactionNumbering');
    }

    public function delete(User $user, TransactionNumbering $transactionNumbering): bool
    {
        return $user->can('Delete:TransactionNumbering');
    }

    /*
    |--------------------------------------------------------------------------
    | Bulk Permissions
    |--------------------------------------------------------------------------
    */

    public function deleteAny(User $user): bool
    {
        return $user->can('DeleteAny:TransactionNumbering');
    }

    public function restoreAny(User $user): bool
    {
        return $user->can('RestoreAny:TransactionNumbering');
    }

    public function forceDeleteAny(User $user): bool
    {
        return $user->can('ForceDeleteAny:TransactionNumbering');
    }

    /*
    |--------------------------------------------------------------------------
    | Advanced Permissions
    |--------------------------------------------------------------------------
    */

    public function restore(User $user, TransactionNumbering $transactionNumbering): bool
    {
        return $user->can('Restore:TransactionNumbering');
    }

    public function forceDelete(User $user, TransactionNumbering $transactionNumbering): bool
    {
        return $user->can('ForceDelete:TransactionNumbering');
    }

    public function replicate(User $user, TransactionNumbering $transactionNumbering): bool
    {
        return $user->can('Replicate:TransactionNumbering');
    }

    public function reorder(User $user): bool
    {
        return $user->can('Reorder:TransactionNumbering');
    }
}