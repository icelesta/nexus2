<?php

declare(strict_types=1);

namespace App\Services\Approval;

use App\Models\ApprovalMaster;
use App\Models\ApprovalMasterStep;
use App\Models\ApprovalTransaction;
use App\Models\ApprovalTransactionStep;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use RuntimeException;

use App\Models\PurchaseRequisition;
use App\Services\Purchasing\AssignmentMaterialRequisitionService;
use App\Services\Purchasing\PurchaseRequisitionService;

class ApprovalTransactionService
{
    /*
    |--------------------------------------------------------------------------
    | Create Approval Transaction
    |--------------------------------------------------------------------------
    |
    | Workflow is completely driven by Approval Master configuration.
    |
    | No approval role or approval level is hard-coded here.
    |
    */

    public function create(
        string $documentType,
        int $documentId,
        ?string $documentNo = null,
        ?int $createdBy = null
    ): ApprovalTransaction {
        return DB::transaction(function () use (
            $documentType,
            $documentId,
            $documentNo,
            $createdBy
        ) {

            /*
            |--------------------------------------------------------------------------
            | Find Active Approval Master
            |--------------------------------------------------------------------------
            */

            $master = $this->getActiveMaster(
                $documentType
            );

            /*
            |--------------------------------------------------------------------------
            | Validate Master Configuration
            |--------------------------------------------------------------------------
            */

            $this->validateMaster(
                $master
            );

            /*
            |--------------------------------------------------------------------------
            | Validate Approver Availability
            |--------------------------------------------------------------------------
            */

            $this->validateApproverAvailability(
                $master
            );

            /*
            |--------------------------------------------------------------------------
            | Prevent Duplicate Transaction
            |--------------------------------------------------------------------------
            */

            $existing = ApprovalTransaction::query()
                ->where(
                    'approval_master_id',
                    $master->getKey()
                )
                ->where(
                    'document_type',
                    $documentType
                )
                ->where(
                    'document_id',
                    $documentId
                )
                ->first();

            if ($existing) {
                throw new RuntimeException(
                    "Approval transaction already exists for document [{$documentType}:{$documentId}]."
                );
            }

            /*
            |--------------------------------------------------------------------------
            | Determine First Approval Level
            |--------------------------------------------------------------------------
            |
            | Never hard-code level 1.
            |
            */

            $firstStep = $master->steps
                ->sortBy('approval_level')
                ->first();

            if (! $firstStep) {
                throw new RuntimeException(
                    "Approval Master [{$master->code}] has no approval step configured."
                );
            }

            /*
            |--------------------------------------------------------------------------
            | Create Transaction Header
            |--------------------------------------------------------------------------
            */

            $transaction = ApprovalTransaction::create([
                'approval_master_id' => $master->getKey(),
                'document_type'      => $documentType,
                'document_id'       => $documentId,
                'document_no'       => $documentNo,

                'current_level' =>
                    (int) $firstStep->approval_level,

                'status' => 'PENDING',

                'submitted_at' => now(),

                'created_by' =>
                    $createdBy ?? Auth::id(),
            ]);

            /*
            |--------------------------------------------------------------------------
            | Snapshot Approval Master Steps
            |--------------------------------------------------------------------------
            */

            $this->createTransactionSteps(
                $transaction,
                $master
            );

            return $transaction->load([
                'approvalMaster',
                'steps',
            ]);
        });
    }

    /*
    |--------------------------------------------------------------------------
    | Find Active Approval Master
    |--------------------------------------------------------------------------
    */

    public function getActiveMaster(
        string $module
    ): ApprovalMaster {
        $master = ApprovalMaster::query()
            ->active()
            ->forModule($module)
            ->with([
                'steps' => fn ($query) =>
                    $query
                        ->orderBy('approval_level')
                        ->with('role'),
            ])
            ->first();

        if (! $master) {
            throw new RuntimeException(
                "No active Approval Master configured for module [{$module}]."
            );
        }

        return $master;
    }

    /*
    |--------------------------------------------------------------------------
    | Validate Approval Master
    |--------------------------------------------------------------------------
    */

    protected function validateMaster(
        ApprovalMaster $master
    ): void {

        /*
        |--------------------------------------------------------------------------
        | Minimum Level
        |--------------------------------------------------------------------------
        */

        if ($master->steps->isEmpty()) {
            throw new RuntimeException(
                "Approval Master [{$master->code}] has no approval levels configured."
            );
        }

        /*
        |--------------------------------------------------------------------------
        | Required Level
        |--------------------------------------------------------------------------
        */

        if (
            $master->steps
                ->where('is_required', true)
                ->isEmpty()
        ) {
            throw new RuntimeException(
                "Approval Master [{$master->code}] has no required approval level."
            );
        }

        /*
        |--------------------------------------------------------------------------
        | Sequential Level
        |--------------------------------------------------------------------------
        |
        | Approval levels must be:
        |
        | 1
        | 2
        | 3
        | ...
        |
        | according to the number of configured steps.
        |
        */

        $levels = $master->steps
            ->sortBy('approval_level')
            ->pluck('approval_level')
            ->map(
                fn ($level): int => (int) $level
            )
            ->values()
            ->all();

        $expected = range(
            1,
            count($levels)
        );

        if ($levels !== $expected) {
            throw new RuntimeException(
                "Approval Master [{$master->code}] contains an invalid approval level sequence."
            );
        }

        /*
        |--------------------------------------------------------------------------
        | Unique Roles
        |--------------------------------------------------------------------------
        */

        $roleIds = $master->steps
            ->pluck('role_id')
            ->filter()
            ->values()
            ->all();

        if (
            count($roleIds)
            !== count(array_unique($roleIds))
        ) {
            throw new RuntimeException(
                "Approval Master [{$master->code}] contains duplicate approval roles."
            );
        }

        /*
        |--------------------------------------------------------------------------
        | Role Validation
        |--------------------------------------------------------------------------
        */

        foreach ($master->steps as $step) {

            if (! $step->role) {
                throw new RuntimeException(
                    "Approval level [{$step->approval_level}] on Approval Master [{$master->code}] has no valid role."
                );
            }

            if (! $step->role->is_active) {
                throw new RuntimeException(
                    "Role [{$step->role->name}] assigned to approval level [{$step->approval_level}] is inactive."
                );
            }
        }
    }

    /*
    |--------------------------------------------------------------------------
    | Create Transaction Steps
    |--------------------------------------------------------------------------
    */

    protected function createTransactionSteps(
        ApprovalTransaction $transaction,
        ApprovalMaster $master
    ): void {

        foreach (
            $master->steps
                ->sortBy('approval_level')
            as $masterStep
        ) {

            $role = $masterStep->role;

            $transaction->steps()->create([
                /*
                |--------------------------------------------------------------------------
                | Master Reference
                |--------------------------------------------------------------------------
                */

                'approval_master_step_id' =>
                    $masterStep->getKey(),

                /*
                |--------------------------------------------------------------------------
                | Level Snapshot
                |--------------------------------------------------------------------------
                */

                'approval_level' =>
                    (int) $masterStep->approval_level,

                /*
                |--------------------------------------------------------------------------
                | Role Snapshot
                |--------------------------------------------------------------------------
                */

                'role_id' =>
                    $role?->getKey(),

                'role_code' =>
                    $role?->role_code,

                'role_name' =>
                    $role?->name,

                /*
                |--------------------------------------------------------------------------
                | Workflow State
                |--------------------------------------------------------------------------
                */

                'status' => 'PENDING',

                'action' => null,

                'approved_by' => null,

                'acted_at' => null,

                'remarks' => null,
            ]);
        }
    }

    /*
    |--------------------------------------------------------------------------
    | Current Step
    |--------------------------------------------------------------------------
    */

    public function getCurrentStep(
        ApprovalTransaction $transaction
    ): ?ApprovalTransactionStep {
        return $transaction
            ->steps()
            ->where(
                'approval_level',
                $transaction->current_level
            )
            ->first();
    }

    /*
    |--------------------------------------------------------------------------
    | Next Approval Step
    |--------------------------------------------------------------------------
    |
    | The next step is determined from the transaction snapshot.
    |
    | This is important because the Approval Master may change later.
    | Existing transactions must continue using their original snapshot.
    |
    */

    public function getNextStep(
        ApprovalTransaction $transaction
    ): ?ApprovalTransactionStep {
        return $transaction
            ->steps()
            ->where(
                'approval_level',
                '>',
                $transaction->current_level
            )
            ->orderBy('approval_level')
            ->first();
    }

    /*
    |--------------------------------------------------------------------------
    | Current Approvers
    |--------------------------------------------------------------------------
    */

    public function getCurrentApprovers(
        ApprovalTransaction $transaction
    ) {
        $step = $this->getCurrentStep(
            $transaction
        );

        if (! $step) {
            return collect();
        }

        return $this->getApproversForStep(
            $step
        );
    }

    /*
    |--------------------------------------------------------------------------
    | Approve
    |--------------------------------------------------------------------------
    |
    | AM-5.6.3
    |
    | Approval workflow:
    |
    | Level 1
    |    ↓
    | Approve
    |    ↓
    | Next configured level
    |    ↓
    | PENDING
    |
    | Final level
    |    ↓
    | APPROVED
    |    ↓
    | Material Requisition → Approved
    |    ↓
    | Assignment Material Requisition → Created
    |    ↓
    | FINAL_APPROVED Notification
    |
    */

    public function approve(
        ApprovalTransaction $transaction,
        User $approver,
        ?string $remarks = null
    ): ApprovalTransaction {

        return DB::transaction(function () use (
            $transaction,
            $approver,
            $remarks
        ) {

            /*
            |--------------------------------------------------------------------------
            | Load Required Relations
            |--------------------------------------------------------------------------
            */

            $transaction->loadMissing([
                'approvalMaster',
                'steps',
            ]);

            /*
            |--------------------------------------------------------------------------
            | Validate Transaction
            |--------------------------------------------------------------------------
            */

            $this->validateTransactionForAction(
                $transaction
            );

            /*
            |--------------------------------------------------------------------------
            | Current Step
            |--------------------------------------------------------------------------
            */

            $step = $this->getCurrentStep(
                $transaction
            );

            if (! $step) {

                throw new RuntimeException(
                    'No current approval step found.'
                );
            }

            /*
            |--------------------------------------------------------------------------
            | Validate Approver
            |--------------------------------------------------------------------------
            */

            $this->validateApprover(
                $step,
                $approver
            );

            /*
            |--------------------------------------------------------------------------
            | Approve Current Step
            |--------------------------------------------------------------------------
            */

            $step->update([

                'approved_by' =>
                    $approver->getKey(),

                'status' =>
                    'APPROVED',

                'action' =>
                    'APPROVE',

                'acted_at' =>
                    now(),

                'remarks' =>
                    $remarks,

            ]);

            /*
            |--------------------------------------------------------------------------
            | Find Next Configured Step
            |--------------------------------------------------------------------------
            */

            $nextStep = $this->getNextStep(
                $transaction
            );

            /*
            |--------------------------------------------------------------------------
            | FINAL APPROVAL
            |--------------------------------------------------------------------------
            |
            | No next configured step means the current step
            | is the final approval level.
            |
            */

            if (! $nextStep) {

                /*
                |--------------------------------------------------------------------------
                | Approval Transaction → APPROVED
                |--------------------------------------------------------------------------
                */

                $transaction->update([

                    'status' =>
                        'APPROVED',

                    'completed_at' =>
                        now(),

                    'updated_by' =>
                        $approver->getKey(),

                ]);

                /*
                |--------------------------------------------------------------------------
                | Material Requisition Integration
                |--------------------------------------------------------------------------
                |
                | Only the final approval of a Material Requisition
                | triggers the Purchasing workflow.
                |
                */

                if (
                    $transaction->document_type
                    === 'MATERIAL_REQUISITION'
                ) {

                    /*
                    |--------------------------------------------------------------------------
                    | Approve Material Requisition
                    |--------------------------------------------------------------------------
                    */

                    $purchaseRequisitionService =
                        app(
                            \App\Services\Purchasing\PurchaseRequisitionService::class
                        );

                    $purchaseRequisitionService->approve(
                        (int) $transaction->document_id
                    );

                    /*
                    |--------------------------------------------------------------------------
                    | Create Assignment Material Requisition
                    |--------------------------------------------------------------------------
                    */

                    $assignmentService =
                        app(
                            \App\Services\Purchasing\AssignmentMaterialRequisitionService::class
                        );

                    $assignmentService
                        ->createFromApprovedPurchaseRequisition(
                            (int) $transaction->document_id
                        );
                }

                /*
                |--------------------------------------------------------------------------
                | Final Approval Notification
                |--------------------------------------------------------------------------
                |
                | Notify requester that the complete approval
                | workflow has been successfully completed.
                |
                */

                $transaction = $transaction->fresh([
                    'approvalMaster',
                    'steps',
                ]);

                app(
                    \App\Services\Notifications\NotificationService::class
                )->notifyFinalApproved(
                    $transaction
                );

                /*
                |--------------------------------------------------------------------------
                | Return Final Approved Transaction
                |--------------------------------------------------------------------------
                */

                return $transaction;
            }

            /*
            |--------------------------------------------------------------------------
            | MOVE TO NEXT CONFIGURED LEVEL
            |--------------------------------------------------------------------------
            */

            $transaction->update([

                'current_level' =>
                    (int) $nextStep->approval_level,

                'status' =>
                    'PENDING',

                'updated_by' =>
                    $approver->getKey(),

            ]);

            /*
            |--------------------------------------------------------------------------
            | Level Approval Notification
            |--------------------------------------------------------------------------
            |
            | Notify requester that the current approval
            | level has been completed.
            |
            */

            app(
                \App\Services\Notifications\NotificationService::class
            )->notifyLevelApproved(
                $transaction->fresh([
                    'approvalMaster',
                    'steps',
                ]),
                (int) $step->approval_level,
                $step->role_name,
            );

            /*
            |--------------------------------------------------------------------------
            | Prepare Fresh Transaction
            |--------------------------------------------------------------------------
            |
            | Reload after current_level has moved to the
            | next configured approval level.
            |
            */

            $transaction = $transaction->fresh([
                'approvalMaster',
                'steps',
            ]);

            /*
            |--------------------------------------------------------------------------
            | Next Level Approval Notification
            |--------------------------------------------------------------------------
            |
            | Notify users assigned to the next configured
            | approval role.
            |
            */

            app(
                \App\Services\Notifications\NotificationService::class
            )->notifyNextApprovalLevel(
                $transaction
            );

            /*
            |--------------------------------------------------------------------------
            | Return Pending Transaction
            |--------------------------------------------------------------------------
            */

            return $transaction;
        });
    }


    /*
    |--------------------------------------------------------------------------
    | Reject
    |--------------------------------------------------------------------------
    |
    | AM-5.6.1-D / AM-5.6.3.5
    |
    | Reject workflow:
    |
    | Current Approval Step
    |        ↓
    | Step = REJECTED
    |        ↓
    | Approval Transaction = REJECTED
    |        ↓
    | Material Requisition = REJECTED
    |        ↓
    | AMR = NOT CREATED
    |        ↓
    | Rejected Notification
    |
    */

    public function reject(
        ApprovalTransaction $transaction,
        User $approver,
        ?string $remarks = null
    ): ApprovalTransaction {

        return DB::transaction(
            function () use (
                $transaction,
                $approver,
                $remarks
            ): ApprovalTransaction {

                /*
                |--------------------------------------------------------------------------
                | Load Required Relations
                |--------------------------------------------------------------------------
                */

                $transaction->loadMissing([
                    'approvalMaster',
                    'steps',
                ]);

                /*
                |--------------------------------------------------------------------------
                | Validate Transaction
                |--------------------------------------------------------------------------
                */

                $this->validateTransactionForAction(
                    $transaction
                );

                /*
                |--------------------------------------------------------------------------
                | Current Step
                |--------------------------------------------------------------------------
                */

                $step = $this->getCurrentStep(
                    $transaction
                );

                if (! $step) {

                    throw new RuntimeException(
                        'No current approval step found.'
                    );
                }

                /*
                |--------------------------------------------------------------------------
                | Validate Approver
                |--------------------------------------------------------------------------
                */

                $this->validateApprover(
                    $step,
                    $approver
                );

                /*
                |--------------------------------------------------------------------------
                | Reject Current Approval Step
                |--------------------------------------------------------------------------
                */

                $step->update([

                    'approved_by' =>
                        $approver->getKey(),

                    'status' =>
                        'REJECTED',

                    'action' =>
                        'REJECT',

                    'acted_at' =>
                        now(),

                    'remarks' =>
                        $remarks,

                ]);

                /*
                |--------------------------------------------------------------------------
                | Reject Approval Transaction
                |--------------------------------------------------------------------------
                */

                $transaction->update([

                    'status' =>
                        'REJECTED',

                    'completed_at' =>
                        now(),

                    'updated_by' =>
                        $approver->getKey(),

                ]);

                /*
                |--------------------------------------------------------------------------
                | Material Requisition Integration
                |--------------------------------------------------------------------------
                |
                | Only MATERIAL_REQUISITION is integrated.
                |
                | Rejection means:
                |
                | Approval Transaction → REJECTED
                | Material Requisition → REJECTED
                | Assignment Material Requisition → NOT CREATED
                |
                */

                if (
                    $transaction->document_type
                    === 'MATERIAL_REQUISITION'
                ) {

                    /*
                    |--------------------------------------------------------------------------
                    | Reject Material Requisition
                    |--------------------------------------------------------------------------
                    */

                    $purchaseRequisitionService =
                        app(
                            \App\Services\Purchasing\PurchaseRequisitionService::class
                        );

                    $purchaseRequisitionService->reject(
                        (int) $transaction->document_id
                    );

                    /*
                    |--------------------------------------------------------------------------
                    | Rejected Notification
                    |--------------------------------------------------------------------------
                    |
                    | Notify the Material Requisition requester
                    | after the underlying document successfully
                    | enters the Rejected state.
                    |
                    */

                    $transaction = $transaction->fresh([
                        'approvalMaster',
                        'steps',
                    ]);

                    app(
                        \App\Services\Notifications\NotificationService::class
                    )->notifyRejected(
                        $transaction,
                        $approver,
                        $remarks,
                    );
                }

                /*
                |--------------------------------------------------------------------------
                | Return Fresh Transaction
                |--------------------------------------------------------------------------
                */

                return $transaction->fresh([
                    'approvalMaster',
                    'steps',
                ]);
            }
        );
    }

    /*
    |--------------------------------------------------------------------------
    | Last Approval Level
    |--------------------------------------------------------------------------
    */

    public function getLastApprovalLevel(
        ApprovalTransaction $transaction
    ): int {
        return (int) $transaction
            ->steps()
            ->max('approval_level');
    }

    /*
    |--------------------------------------------------------------------------
    | Approvers For Step
    |--------------------------------------------------------------------------
    |
    | Supports:
    | - ApprovalMasterStep
    | - ApprovalTransactionStep
    |
    */

    public function getApproversForStep(
        ApprovalMasterStep|ApprovalTransactionStep $step
    ) {
        if (! $step->role_id) {
            return collect();
        }

        return User::query()
            ->where('is_active', true)
            ->whereHas(
                'roles',
                fn ($query) =>
                    $query
                        ->where(
                            'roles.id',
                            $step->role_id
                        )
                        ->where(
                            'roles.guard_name',
                            'web'
                        )
                        ->where(
                            'roles.is_active',
                            true
                        )
            )
            ->orderBy('name')
            ->get();
    }

    /*
    |--------------------------------------------------------------------------
    | Validate Approver Availability
    |--------------------------------------------------------------------------
    */

    public function validateApproverAvailability(
        ApprovalMaster $master
    ): void {

        foreach (
            $master->steps
                ->sortBy('approval_level')
            as $step
        ) {

            if (! $step->is_required) {
                continue;
            }

            $approvers =
                $this->getApproversForStep(
                    $step
                );

            if ($approvers->isEmpty()) {

                $roleName =
                    $step->role?->name
                    ?? 'Unknown';

                throw new RuntimeException(
                    "No active approver found for role [{$roleName}] on Approval Master [{$master->code}] at level [{$step->approval_level}]."
                );
            }
        }
    }

    /*
    |--------------------------------------------------------------------------
    | Validate Transaction For Action
    |--------------------------------------------------------------------------
    */

    protected function validateTransactionForAction(
        ApprovalTransaction $transaction
    ): void {

        if (! $transaction->isPending()) {
            throw new RuntimeException(
                "Approval transaction [{$transaction->id}] is not pending."
            );
        }

        if (
            $transaction->current_level < 1
        ) {
            throw new RuntimeException(
                'Invalid current approval level.'
            );
        }
    }

    /*
    |--------------------------------------------------------------------------
    | Validate Approver
    |--------------------------------------------------------------------------
    */

    protected function validateApprover(
        ApprovalTransactionStep $step,
        User $approver
    ): void {

        if (! $approver->is_active) {
            throw new RuntimeException(
                "User [{$approver->name}] is inactive and cannot approve this transaction."
            );
        }

        if (! $step->role_id) {
            throw new RuntimeException(
                "Approval level [{$step->approval_level}] has no assigned role."
            );
        }

        $hasRole = $approver
            ->roles()
            ->where(
                'roles.id',
                $step->role_id
            )
            ->where(
                'roles.guard_name',
                'web'
            )
            ->where(
                'roles.is_active',
                true
            )
            ->exists();

        if (! $hasRole) {
            throw new RuntimeException(
                "User [{$approver->name}] is not authorized to approve level [{$step->approval_level}]. Required role: [{$step->role_name}]."
            );
        }
    }

    /*
    |--------------------------------------------------------------------------
    | Can Approve
    |--------------------------------------------------------------------------
    */

    public function canApprove(
        ApprovalTransaction $transaction,
        User $user
    ): bool {

        if (! $transaction->isPending()) {
            return false;
        }

        $step = $this->getCurrentStep(
            $transaction
        );

        if (! $step) {
            return false;
        }

        if (! $user->is_active) {
            return false;
        }

        if (! $step->role_id) {
            return false;
        }

        return $user
            ->roles()
            ->where(
                'roles.id',
                $step->role_id
            )
            ->where(
                'roles.guard_name',
                'web'
            )
            ->where(
                'roles.is_active',
                true
            )
            ->exists();
    }
}