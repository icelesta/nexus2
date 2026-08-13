<?php

declare(strict_types=1);

namespace App\Policies;

use App\Models\JournalType;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class JournalTypePolicy
{
    use HandlesAuthorization;

    /*
    |--------------------------------------------------------------------------
    | Basic Permissions
    |--------------------------------------------------------------------------
    */

    public function viewAny(User $user): bool
    {
        return $user->can('ViewAny:JournalType');
    }

    public function view(User $user, JournalType $journalType): bool
    {
        return $user->can('View:JournalType');
    }

    public function create(User $user): bool
    {
        return $user->can('Create:JournalType');
    }

    public function update(User $user, JournalType $journalType): bool
    {
        return $user->can('Update:JournalType');
    }

    public function delete(User $user, JournalType $journalType): bool
    {
        return $user->can('Delete:JournalType');
    }

    /*
    |--------------------------------------------------------------------------
    | Bulk Permissions
    |--------------------------------------------------------------------------
    */

    public function deleteAny(User $user): bool
    {
        return $user->can('DeleteAny:JournalType');
    }

    public function restoreAny(User $user): bool
    {
        return $user->can('RestoreAny:JournalType');
    }

    public function forceDeleteAny(User $user): bool
    {
        return $user->can('ForceDeleteAny:JournalType');
    }

    /*
    |--------------------------------------------------------------------------
    | Advanced Permissions
    |--------------------------------------------------------------------------
    */

    public function restore(User $user, JournalType $journalType): bool
    {
        return $user->can('Restore:JournalType');
    }

    public function forceDelete(User $user, JournalType $journalType): bool
    {
        return $user->can('ForceDelete:JournalType');
    }

    public function replicate(User $user, JournalType $journalType): bool
    {
        return $user->can('Replicate:JournalType');
    }

    public function reorder(User $user): bool
    {
        return $user->can('Reorder:JournalType');
    }
}