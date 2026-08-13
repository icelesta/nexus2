<?php

declare(strict_types=1);

namespace App\Policies;

use App\Models\ExchangeRate;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class ExchangeRatePolicy
{
    use HandlesAuthorization;

    /*
    |--------------------------------------------------------------------------
    | Basic Permissions
    |--------------------------------------------------------------------------
    */

    public function viewAny(User $user): bool
    {
        return $user->can('ViewAny:ExchangeRate');
    }

    public function view(User $user, ExchangeRate $exchangeRate): bool
    {
        return $user->can('View:ExchangeRate');
    }

    public function create(User $user): bool
    {
        return $user->can('Create:ExchangeRate');
    }

    public function update(User $user, ExchangeRate $exchangeRate): bool
    {
        return $user->can('Update:ExchangeRate');
    }

    public function delete(User $user, ExchangeRate $exchangeRate): bool
    {
        return $user->can('Delete:ExchangeRate');
    }

    /*
    |--------------------------------------------------------------------------
    | Bulk Permissions
    |--------------------------------------------------------------------------
    */

    public function deleteAny(User $user): bool
    {
        return $user->can('DeleteAny:ExchangeRate');
    }

    public function restoreAny(User $user): bool
    {
        return $user->can('RestoreAny:ExchangeRate');
    }

    public function forceDeleteAny(User $user): bool
    {
        return $user->can('ForceDeleteAny:ExchangeRate');
    }

    /*
    |--------------------------------------------------------------------------
    | Advanced Permissions
    |--------------------------------------------------------------------------
    */

    public function restore(User $user, ExchangeRate $exchangeRate): bool
    {
        return $user->can('Restore:ExchangeRate');
    }

    public function forceDelete(User $user, ExchangeRate $exchangeRate): bool
    {
        return $user->can('ForceDelete:ExchangeRate');
    }

    public function replicate(User $user, ExchangeRate $exchangeRate): bool
    {
        return $user->can('Replicate:ExchangeRate');
    }

    public function reorder(User $user): bool
    {
        return $user->can('Reorder:ExchangeRate');
    }
}