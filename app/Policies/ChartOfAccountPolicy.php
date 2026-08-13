<?php

declare(strict_types=1);

namespace App\Policies;

use App\Models\ChartOfAccount;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class ChartOfAccountPolicy
{
    use HandlesAuthorization;

    /*
    |--------------------------------------------------------------------------
    | Basic Permissions
    |--------------------------------------------------------------------------
    */

    public function viewAny(User $user): bool
    {
        return $user->can('ViewAny:ChartOfAccount');
    }

    public function view(User $user, ChartOfAccount $chartOfAccount): bool
    {
        return $user->can('View:ChartOfAccount');
    }

    public function create(User $user): bool
    {
        return $user->can('Create:ChartOfAccount');
    }

    public function update(User $user, ChartOfAccount $chartOfAccount): bool
    {
        return $user->can('Update:ChartOfAccount');
    }

    public function delete(User $user, ChartOfAccount $chartOfAccount): bool
    {
        return $user->can('Delete:ChartOfAccount');
    }

    /*
    |--------------------------------------------------------------------------
    | Bulk Permissions
    |--------------------------------------------------------------------------
    */

    public function deleteAny(User $user): bool
    {
        return $user->can('DeleteAny:ChartOfAccount');
    }

    public function restoreAny(User $user): bool
    {
        return $user->can('RestoreAny:ChartOfAccount');
    }

    public function forceDeleteAny(User $user): bool
    {
        return $user->can('ForceDeleteAny:ChartOfAccount');
    }

    /*
    |--------------------------------------------------------------------------
    | Advanced Permissions
    |--------------------------------------------------------------------------
    */

    public function restore(User $user, ChartOfAccount $chartOfAccount): bool
    {
        return $user->can('Restore:ChartOfAccount');
    }

    public function forceDelete(User $user, ChartOfAccount $chartOfAccount): bool
    {
        return $user->can('ForceDelete:ChartOfAccount');
    }

    public function replicate(User $user, ChartOfAccount $chartOfAccount): bool
    {
        return $user->can('Replicate:ChartOfAccount');
    }

    public function reorder(User $user): bool
    {
        return $user->can('Reorder:ChartOfAccount');
    }
}