<?php

declare(strict_types=1);

namespace App\Policies;

use App\Models\Section;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class SectionPolicy
{
    use HandlesAuthorization;

    /*
    |--------------------------------------------------------------------------
    | Basic Permissions
    |--------------------------------------------------------------------------
    */

    public function viewAny(User $user): bool
    {
        return $user->can('ViewAny:Section');
    }

    public function view(User $user, Section $section): bool
    {
        return $user->can('View:Section');
    }

    public function create(User $user): bool
    {
        return $user->can('Create:Section');
    }

    public function update(User $user, Section $section): bool
    {
        return $user->can('Update:Section');
    }

    public function delete(User $user, Section $section): bool
    {
        return $user->can('Delete:Section');
    }

    /*
    |--------------------------------------------------------------------------
    | Bulk Permissions
    |--------------------------------------------------------------------------
    */

    public function deleteAny(User $user): bool
    {
        return $user->can('DeleteAny:Section');
    }

    public function restoreAny(User $user): bool
    {
        return $user->can('RestoreAny:Section');
    }

    public function forceDeleteAny(User $user): bool
    {
        return $user->can('ForceDeleteAny:Section');
    }

    /*
    |--------------------------------------------------------------------------
    | Advanced Permissions
    |--------------------------------------------------------------------------
    */

    public function restore(User $user, Section $section): bool
    {
        return $user->can('Restore:Section');
    }

    public function forceDelete(User $user, Section $section): bool
    {
        return $user->can('ForceDelete:Section');
    }

    public function replicate(User $user, Section $section): bool
    {
        return $user->can('Replicate:Section');
    }

    public function reorder(User $user): bool
    {
        return $user->can('Reorder:Section');
    }
}