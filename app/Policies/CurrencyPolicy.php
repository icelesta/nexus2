<?php

declare(strict_types=1);

namespace App\Policies;

use App\Models\Currency;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class CurrencyPolicy
{
    use HandlesAuthorization;

    /*
    |--------------------------------------------------------------------------
    | Basic Permissions
    |--------------------------------------------------------------------------
    */

    public function viewAny(User $user): bool
    {
        return $user->can('ViewAny:Currency');
    }

    public function view(User $user, Currency $currency): bool
    {
        return $user->can('View:Currency');
    }

    public function create(User $user): bool
    {
        return $user->can('Create:Currency');
    }

    public function update(User $user, Currency $currency): bool
    {
        return $user->can('Update:Currency');
    }

    public function delete(User $user, Currency $currency): bool
    {
        return $user->can('Delete:Currency');
    }

    /*
    |--------------------------------------------------------------------------
    | Bulk Permissions
    |--------------------------------------------------------------------------
    */

    public function deleteAny(User $user): bool
    {
        return $user->can('DeleteAny:Currency');
    }

    public function restoreAny(User $user): bool
    {
        return $user->can('RestoreAny:Currency');
    }

    public function forceDeleteAny(User $user): bool
    {
        return $user->can('ForceDeleteAny:Currency');
    }

    /*
    |--------------------------------------------------------------------------
    | Advanced Permissions
    |--------------------------------------------------------------------------
    */

    public function restore(User $user, Currency $currency): bool
    {
        return $user->can('Restore:Currency');
    }

    public function forceDelete(User $user, Currency $currency): bool
    {
        return $user->can('ForceDelete:Currency');
    }

    public function replicate(User $user, Currency $currency): bool
    {
        return $user->can('Replicate:Currency');
    }

    public function reorder(User $user): bool
    {
        return $user->can('Reorder:Currency');
    }
}