<?php

declare(strict_types=1);

namespace App\Services\Approval;

use App\Models\ApprovalTransaction;
use App\Models\User;
use Illuminate\Database\Eloquent\Collection;

class ApprovalDashboardService
{
    /*
    |--------------------------------------------------------------------------
    | Dashboard Summary
    |--------------------------------------------------------------------------
    */

    /**
     * Get approval dashboard summary.
     */
    public function summary(
        User $user
    ): array {

        return [

            'pending' =>
                $this->pendingCount($user),

            'approved' =>
                $this->approvedCount(),

            'rejected' =>
                $this->rejectedCount(),

            'unread_notifications' =>
                $user->unreadNotifications()->count(),

        ];
    }

    /*
    |--------------------------------------------------------------------------
    | My Pending Approval
    |--------------------------------------------------------------------------
    */

    /**
     * Count approval transactions currently
     * waiting for the authenticated user's role.
     */
    public function pendingCount(
        User $user
    ): int {

        $roleIds = $user
            ->roles()
            ->pluck('roles.id');

        if ($roleIds->isEmpty()) {
            return 0;
        }

        return ApprovalTransaction::query()

            ->where(
                'status',
                'PENDING'
            )

            ->whereHas(
                'steps',
                function ($query) use ($roleIds) {

                    $query

                        /*
                        |--------------------------------------------------------------------------
                        | Current Approval Level
                        |--------------------------------------------------------------------------
                        */

                        ->whereColumn(
                            'approval_transaction_steps.approval_level',
                            'approval_transactions.current_level'
                        )

                        /*
                        |--------------------------------------------------------------------------
                        | Current Step Must Be Pending
                        |--------------------------------------------------------------------------
                        */

                        ->where(
                            'approval_transaction_steps.status',
                            'PENDING'
                        )

                        /*
                        |--------------------------------------------------------------------------
                        | User Must Have The Configured Role
                        |--------------------------------------------------------------------------
                        */

                        ->whereIn(
                            'approval_transaction_steps.role_id',
                            $roleIds
                        );
                }
            )

            ->count();
    }

    /*
    |--------------------------------------------------------------------------
    | Approved
    |--------------------------------------------------------------------------
    */

    /**
     * Count completed approved transactions.
     */
    public function approvedCount(): int
    {
        return ApprovalTransaction::query()
            ->where(
                'status',
                'APPROVED'
            )
            ->count();
    }

    /*
    |--------------------------------------------------------------------------
    | Rejected
    |--------------------------------------------------------------------------
    */

    /**
     * Count rejected transactions.
     */
    public function rejectedCount(): int
    {
        return ApprovalTransaction::query()
            ->where(
                'status',
                'REJECTED'
            )
            ->count();
    }

    /*
    |--------------------------------------------------------------------------
    | Pending Approval List
    |--------------------------------------------------------------------------
    */

    /**
     * Get transactions currently waiting
     * for the authenticated user's approval.
     */
    public function pendingApprovals(
        User $user,
        int $limit = 10
    ): Collection {

        $roleIds = $user
            ->roles()
            ->pluck('roles.id');

        if ($roleIds->isEmpty()) {
            return new Collection();
        }

        return ApprovalTransaction::query()

            ->with([
                'approvalMaster',
                'steps',
            ])

            ->where(
                'status',
                'PENDING'
            )

            ->whereHas(
                'steps',
                function ($query) use ($roleIds) {

                    $query

                        ->whereColumn(
                            'approval_transaction_steps.approval_level',
                            'approval_transactions.current_level'
                        )

                        ->where(
                            'approval_transaction_steps.status',
                            'PENDING'
                        )

                        ->whereIn(
                            'approval_transaction_steps.role_id',
                            $roleIds
                        );
                }
            )

            ->latest(
                'submitted_at'
            )

            ->limit(
                $limit
            )

            ->get();
    }

    /*
    |--------------------------------------------------------------------------
    | Recent Activity
    |--------------------------------------------------------------------------
    */

    /**
     * Get recently completed approval transactions.
     */
    public function recentActivity(
        int $limit = 10
    ): Collection {

        return ApprovalTransaction::query()

            ->with([
                'approvalMaster',
                'steps',
            ])

            ->whereIn(
                'status',
                [
                    'APPROVED',
                    'REJECTED',
                ]
            )

            ->latest(
                'completed_at'
            )

            ->limit(
                $limit
            )

            ->get();
    }
}