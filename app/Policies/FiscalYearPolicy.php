<?php

declare(strict_types=1);

namespace App\Policies;

use App\Models\FiscalYear;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class FiscalYearPolicy
{
    use HandlesAuthorization;

    /*
    |--------------------------------------------------------------------------
    | Basic Permissions
    |--------------------------------------------------------------------------
    */

    public function viewAny(User $user): bool
    {
        return $user->can('ViewAny:FiscalYear');
    }

    public function view(User $user, FiscalYear $fiscalYear): bool
    {
        return $user->can('View:FiscalYear');
    }

    public function create(User $user): bool
    {
        return $user->can('Create:FiscalYear');
    }

    public function update(User $user, FiscalYear $fiscalYear): bool
    {
        return $user->can('Update:FiscalYear');
    }

    public function delete(User $user, FiscalYear $fiscalYear): bool
    {
        return $user->can('Delete:FiscalYear');
    }

    /*
    |--------------------------------------------------------------------------
    | Bulk Permissions
    |--------------------------------------------------------------------------
    */

    public function deleteAny(User $user): bool
    {
        return $user->can('DeleteAny:FiscalYear');
    }

    public function restoreAny(User $user): bool
    {
        return $user->can('RestoreAny:FiscalYear');
    }

    public function forceDeleteAny(User $user): bool
    {
        return $user->can('ForceDeleteAny:FiscalYear');
    }

    /*
    |--------------------------------------------------------------------------
    | Advanced Permissions
    |--------------------------------------------------------------------------
    */

    public function restore(User $user, FiscalYear $fiscalYear): bool
    {
        return $user->can('Restore:FiscalYear');
    }

    public function forceDelete(User $user, FiscalYear $fiscalYear): bool
    {
        return $user->can('ForceDelete:FiscalYear');
    }

    public function replicate(User $user, FiscalYear $fiscalYear): bool
    {
        return $user->can('Replicate:FiscalYear');
    }

    public function reorder(User $user): bool
    {
        return $user->can('Reorder:FiscalYear');
    }
}