<?php

declare(strict_types=1);

namespace App\Services\Notifications;

use App\Models\ApprovalTransaction;
use App\Models\PurchaseRequisition;
use App\Models\User;
use App\Notifications\ApprovalNotification;
use Illuminate\Support\Collection;


class NotificationService
{
    /*
    |--------------------------------------------------------------------------
    | Approval Required
    |--------------------------------------------------------------------------
    |
    | Notify every active user who has the role configured
    | on the current approval step.
    |
    */

    public function notifyApprovalRequired(
        ApprovalTransaction $transaction,
    ): void {

        $transaction->loadMissing([
            'steps',
        ]);

        $step = $transaction
            ->steps
            ->firstWhere(
                'approval_level',
                $transaction->current_level
            );

        if (! $step) {
            return;
        }

        $users = $this->usersForRole(
            $step->role_name
        );

        foreach ($users as $user) {

            $user->notify(
                new ApprovalNotification(
                    event:
                        ApprovalNotification::APPROVAL_REQUIRED,

                    documentType:
                        $transaction->document_type,

                    documentId:
                        (int) $transaction->document_id,

                    documentNo:
                        $transaction->document_no,

                    approvalLevel:
                        (int) $step->approval_level,

                    roleName:
                        $step->role_name,
                )
            );
        }
    }

    /*
    |--------------------------------------------------------------------------
    | Next Approval Level
    |--------------------------------------------------------------------------
    */

    public function notifyNextApprovalLevel(
        ApprovalTransaction $transaction,
    ): void {

        $transaction->loadMissing([
            'steps',
        ]);

        $step = $transaction
            ->steps
            ->firstWhere(
                'approval_level',
                $transaction->current_level
            );

        if (! $step) {
            return;
        }

        $users = $this->usersForRole(
            $step->role_name
        );

        foreach ($users as $user) {

            $user->notify(
                new ApprovalNotification(
                    event:
                        ApprovalNotification::APPROVAL_REQUIRED,

                    documentType:
                        $transaction->document_type,

                    documentId:
                        (int) $transaction->document_id,

                    documentNo:
                        $transaction->document_no,

                    approvalLevel:
                        (int) $step->approval_level,

                    roleName:
                        $step->role_name,
                )
            );
        }
    }

    /*
    |--------------------------------------------------------------------------
    | Notify Level Approved
    |--------------------------------------------------------------------------
    */

    public function notifyLevelApproved(
        ApprovalTransaction $transaction,
        int $level,
        ?string $roleName = null,
    ): void {

        $document = $this->findDocument(
            $transaction
        );

        if (! $document) {
            return;
        }

        $user = $this->documentRequester(
            $document
        );

        if (! $user) {
            return;
        }

        $user->notify(
            new ApprovalNotification(
                event:
                    ApprovalNotification::LEVEL_APPROVED,

                documentType:
                    $transaction->document_type,

                documentId:
                    (int) $transaction->document_id,

                documentNo:
                    $transaction->document_no,

                approvalLevel:
                    $level,

                roleName:
                    $roleName,
            )
        );
    }

    /*
    |--------------------------------------------------------------------------
    | Final Approval
    |--------------------------------------------------------------------------
    */

    public function notifyFinalApproved(
        ApprovalTransaction $transaction,
    ): void {

        $document = $this->findDocument(
            $transaction
        );

        if (! $document) {
            return;
        }

        $user = $this->documentRequester(
            $document
        );

        if (! $user) {
            return;
        }

        $user->notify(
            new ApprovalNotification(
                event:
                    ApprovalNotification::FINAL_APPROVED,

                documentType:
                    $transaction->document_type,

                documentId:
                    (int) $transaction->document_id,

                documentNo:
                    $transaction->document_no,
            )
        );
    }

    /*
    |--------------------------------------------------------------------------
    | Rejected
    |--------------------------------------------------------------------------
    */

    public function notifyRejected(
        ApprovalTransaction $transaction,
        User $approver,
        ?string $remarks = null,
    ): void {

        $document = $this->findDocument(
            $transaction
        );

        if (! $document) {
            return;
        }

        $user = $this->documentRequester(
            $document
        );

        if (! $user) {
            return;
        }

        $user->notify(
            new ApprovalNotification(
                event:
                    ApprovalNotification::REJECTED,

                documentType:
                    $transaction->document_type,

                documentId:
                    (int) $transaction->document_id,

                documentNo:
                    $transaction->document_no,

                actorName:
                    $approver->name,

                remarks:
                    $remarks,
            )
        );
    }

    /*
    |--------------------------------------------------------------------------
    | Users By Approval Role
    |--------------------------------------------------------------------------
    */

    protected function usersForRole(
        ?string $roleName,
    ): Collection {

        if (
            blank($roleName)
        ) {
            return collect();
        }

        return User::query()
            ->active()
            ->role($roleName)
            ->get();
    }

    /*
    |--------------------------------------------------------------------------
    | Find Document
    |--------------------------------------------------------------------------
    */

    protected function findDocument(
        ApprovalTransaction $transaction,
    ): ?PurchaseRequisition {

        if (
            $transaction->document_type
            !== 'MATERIAL_REQUISITION'
        ) {
            return null;
        }

        return PurchaseRequisition::query()
            ->find(
                $transaction->document_id
            );
    }

    /*
    |--------------------------------------------------------------------------
    | Document Requester
    |--------------------------------------------------------------------------
    */

    protected function documentRequester(
        PurchaseRequisition $document,
    ): ?User {

        return User::query()
            ->find(
                $document->requester_id
            );
    }
}