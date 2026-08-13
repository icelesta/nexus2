<?php

declare(strict_types=1);

namespace App\Policies;

use App\Models\PaymentTerm;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class PaymentTermPolicy
{
    use HandlesAuthorization;

    /*
    |--------------------------------------------------------------------------
    | Basic Permissions
    |--------------------------------------------------------------------------
    */

    public function viewAny(User $user): bool
    {
        return $user->can('ViewAny:PaymentTerm');
    }

    public function view(User $user, PaymentTerm $paymentTerm): bool
    {
        return $user->can('View:PaymentTerm');
    }

    public function create(User $user): bool
    {
        return $user->can('Create:PaymentTerm');
    }

    public function update(User $user, PaymentTerm $paymentTerm): bool
    {
        return $user->can('Update:PaymentTerm');
    }

    public function delete(User $user, PaymentTerm $paymentTerm): bool
    {
        return $user->can('Delete:PaymentTerm');
    }

    /*
    |--------------------------------------------------------------------------
    | Bulk Permissions
    |--------------------------------------------------------------------------
    */

    public function deleteAny(User $user): bool
    {
        return $user->can('DeleteAny:PaymentTerm');
    }

    public function restoreAny(User $user): bool
    {
        return $user->can('RestoreAny:PaymentTerm');
    }

    public function forceDeleteAny(User $user): bool
    {
        return $user->can('ForceDeleteAny:PaymentTerm');
    }

    /*
    |--------------------------------------------------------------------------
    | Advanced Permissions
    |--------------------------------------------------------------------------
    */

    public function restore(User $user, PaymentTerm $paymentTerm): bool
    {
        return $user->can('Restore:PaymentTerm');
    }

    public function forceDelete(User $user, PaymentTerm $paymentTerm): bool
    {
        return $user->can('ForceDelete:PaymentTerm');
    }

    public function replicate(User $user, PaymentTerm $paymentTerm): bool
    {
        return $user->can('Replicate:PaymentTerm');
    }

    public function reorder(User $user): bool
    {
        return $user->can('Reorder:PaymentTerm');
    }
}