<?php

declare(strict_types=1);

namespace App\Policies;

use App\Models\ItemBarcode;
use App\Models\User;

use Illuminate\Auth\Access\HandlesAuthorization;

class ItemBarcodePolicy
{
    use HandlesAuthorization;

    /*
    |--------------------------------------------------------------------------
    | Before
    |--------------------------------------------------------------------------
    */

    public function before(User $user): ?bool
    {
        if ($user->hasRole('Super Admin')) {
            return true;
        }

        return null;
    }

    /*
    |--------------------------------------------------------------------------
    | View
    |--------------------------------------------------------------------------
    */

    public function viewAny(User $user): bool
    {
        return $user->can('ViewAny:ItemBarcode');
    }

    public function view(User $user, ItemBarcode $record): bool
    {
        return $user->can('View:ItemBarcode');
    }

    /*
    |--------------------------------------------------------------------------
    | Create
    |--------------------------------------------------------------------------
    */

    public function create(User $user): bool
    {
        return $user->can('Create:ItemBarcode');
    }

    /*
    |--------------------------------------------------------------------------
    | Update
    |--------------------------------------------------------------------------
    */

    public function update(User $user, ItemBarcode $record): bool
    {
        return $user->can('Update:ItemBarcode');
    }

    /*
    |--------------------------------------------------------------------------
    | Delete
    |--------------------------------------------------------------------------
    */

    public function delete(User $user, ItemBarcode $record): bool
    {
        return $user->can('Delete:ItemBarcode');
    }

    public function deleteAny(User $user): bool
    {
        return $user->can('DeleteAny:ItemBarcode');
    }

    /*
    |--------------------------------------------------------------------------
    | Restore
    |--------------------------------------------------------------------------
    */

    public function restore(User $user, ItemBarcode $record): bool
    {
        return $user->can('Restore:ItemBarcode');
    }

    public function restoreAny(User $user): bool
    {
        return $user->can('RestoreAny:ItemBarcode');
    }

    /*
    |--------------------------------------------------------------------------
    | Force Delete
    |--------------------------------------------------------------------------
    */

    public function forceDelete(User $user, ItemBarcode $record): bool
    {
        return $user->can('ForceDelete:ItemBarcode');
    }

    public function forceDeleteAny(User $user): bool
    {
        return $user->can('ForceDeleteAny:ItemBarcode');
    }

    /*
    |--------------------------------------------------------------------------
    | Replicate
    |--------------------------------------------------------------------------
    */

    public function replicate(User $user, ItemBarcode $record): bool
    {
        return $user->can('Replicate:ItemBarcode');
    }

    /*
    |--------------------------------------------------------------------------
    | Reorder
    |--------------------------------------------------------------------------
    */

    public function reorder(User $user): bool
    {
        return $user->can('Reorder:ItemBarcode');
    }
}