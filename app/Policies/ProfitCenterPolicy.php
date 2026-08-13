<?php

declare(strict_types=1);

namespace App\Policies;

use App\Models\ProfitCenter;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class ProfitCenterPolicy
{
    use HandlesAuthorization;

    /*
    |--------------------------------------------------------------------------
    | Basic Permissions
    |--------------------------------------------------------------------------
    */

    public function viewAny(User $user): bool
    {
        return $user->can('ViewAny:ProfitCenter');
    }

    public function view(User $user, ProfitCenter $profitCenter): bool
    {
        return $user->can('View:ProfitCenter');
    }

    public function create(User $user): bool
    {
        return $user->can('Create:ProfitCenter');
    }

    public function update(User $user, ProfitCenter $profitCenter): bool
    {
        return $user->can('Update:ProfitCenter');
    }

    public function delete(User $user, ProfitCenter $profitCenter): bool
    {
        return $user->can('Delete:ProfitCenter');
    }

    /*
    |--------------------------------------------------------------------------
    | Bulk Permissions
    |--------------------------------------------------------------------------
    */

    public function deleteAny(User $user): bool
    {
        return $user->can('DeleteAny:ProfitCenter');
    }

    public function restoreAny(User $user): bool
    {
        return $user->can('RestoreAny:ProfitCenter');
    }

    public function forceDeleteAny(User $user): bool
    {
        return $user->can('ForceDeleteAny:ProfitCenter');
    }

    /*
    |--------------------------------------------------------------------------
    | Advanced Permissions
    |--------------------------------------------------------------------------
    */

    public function restore(User $user, ProfitCenter $profitCenter): bool
    {
        return $user->can('Restore:ProfitCenter');
    }

    public function forceDelete(User $user, ProfitCenter $profitCenter): bool
    {
        return $user->can('ForceDelete:ProfitCenter');
    }

    public function replicate(User $user, ProfitCenter $profitCenter): bool
    {
        return $user->can('Replicate:ProfitCenter');
    }

    public function reorder(User $user): bool
    {
        return $user->can('Reorder:ProfitCenter');
    }
}