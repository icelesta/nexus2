<?php

declare(strict_types=1);

namespace App\Policies;

use App\Models\TaxMaster;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class TaxMasterPolicy
{
    use HandlesAuthorization;

    /*
    |--------------------------------------------------------------------------
    | Basic Permissions
    |--------------------------------------------------------------------------
    */

    public function viewAny(User $user): bool
    {
        return $user->can('ViewAny:TaxMaster');
    }

    public function view(User $user, TaxMaster $taxMaster): bool
    {
        return $user->can('View:TaxMaster');
    }

    public function create(User $user): bool
    {
        return $user->can('Create:TaxMaster');
    }

    public function update(User $user, TaxMaster $taxMaster): bool
    {
        return $user->can('Update:TaxMaster');
    }

    public function delete(User $user, TaxMaster $taxMaster): bool
    {
        return $user->can('Delete:TaxMaster');
    }

    /*
    |--------------------------------------------------------------------------
    | Bulk Permissions
    |--------------------------------------------------------------------------
    */

    public function deleteAny(User $user): bool
    {
        return $user->can('DeleteAny:TaxMaster');
    }

    public function restoreAny(User $user): bool
    {
        return $user->can('RestoreAny:TaxMaster');
    }

    public function forceDeleteAny(User $user): bool
    {
        return $user->can('ForceDeleteAny:TaxMaster');
    }

    /*
    |--------------------------------------------------------------------------
    | Advanced Permissions
    |--------------------------------------------------------------------------
    */

    public function restore(User $user, TaxMaster $taxMaster): bool
    {
        return $user->can('Restore:TaxMaster');
    }

    public function forceDelete(User $user, TaxMaster $taxMaster): bool
    {
        return $user->can('ForceDelete:TaxMaster');
    }

    public function replicate(User $user, TaxMaster $taxMaster): bool
    {
        return $user->can('Replicate:TaxMaster');
    }

    public function reorder(User $user): bool
    {
        return $user->can('Reorder:TaxMaster');
    }
}