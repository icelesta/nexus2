<?php

declare(strict_types=1);

namespace App\Policies;

use App\Models\AdmLimitMaster;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class AdmLimitMasterPolicy
{
    use HandlesAuthorization;

    /*
    |--------------------------------------------------------------------------
    | Basic Permissions
    |--------------------------------------------------------------------------
    */

    public function viewAny(User $user): bool
    {
        return $user->can('ViewAny:AdmLimitMaster');
    }

    public function view(
        User $user,
        AdmLimitMaster $admLimitMaster
    ): bool {
        return $user->can('View:AdmLimitMaster');
    }

    public function create(User $user): bool
    {
        return $user->can('Create:AdmLimitMaster');
    }

    public function update(
        User $user,
        AdmLimitMaster $admLimitMaster
    ): bool {
        return $user->can('Update:AdmLimitMaster');
    }

    public function delete(
        User $user,
        AdmLimitMaster $admLimitMaster
    ): bool {
        return $user->can('Delete:AdmLimitMaster');
    }

    /*
    |--------------------------------------------------------------------------
    | Bulk Permissions
    |--------------------------------------------------------------------------
    */

    public function deleteAny(User $user): bool
    {
        return $user->can('DeleteAny:AdmLimitMaster');
    }

    public function restoreAny(User $user): bool
    {
        return $user->can('RestoreAny:AdmLimitMaster');
    }

    public function forceDeleteAny(User $user): bool
    {
        return $user->can('ForceDeleteAny:AdmLimitMaster');
    }

    /*
    |--------------------------------------------------------------------------
    | Advanced Permissions
    |--------------------------------------------------------------------------
    */

    public function restore(
        User $user,
        AdmLimitMaster $admLimitMaster
    ): bool {
        return $user->can('Restore:AdmLimitMaster');
    }

    public function forceDelete(
        User $user,
        AdmLimitMaster $admLimitMaster
    ): bool {
        return $user->can('ForceDelete:AdmLimitMaster');
    }

    public function replicate(
        User $user,
        AdmLimitMaster $admLimitMaster
    ): bool {
        return $user->can('Replicate:AdmLimitMaster');
    }

    public function reorder(User $user): bool
    {
        return $user->can('Reorder:AdmLimitMaster');
    }
}