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
use App\Models\AssignmentMaterialRequisition;

use App\Models\PurchaseRequisition;
use App\Models\PurchaseOrder;

use App\Services\Purchasing\AssignmentMaterialRequisitionService;
use App\Services\Purchasing\PurchaseRequisitionService;

class ApprovalTransactionService
{

    public function create(
        string $documentType,
        int $documentId,
        ?string $documentNo = null,
        ?int $createdBy = null,
        ?int $startingLevel = null,
        ?string $approvalModule = null,
    ): ApprovalTransaction {
        return DB::transaction(function () use (
            $documentType,
            $documentId,
            $documentNo,
            $createdBy,
            $startingLevel,
            $approvalModule
        ) {

            /*
            |--------------------------------------------------------------------------
            | Find Active Approval Master
            |--------------------------------------------------------------------------
            */

            $master = $this->getActiveMaster(
                $approvalModule ?? $documentType
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

            $this->validateApproverAvailabilityForDocument(
                $master,
                $documentType,
                $startingLevel
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
            | Determine Starting Approval Level
            |--------------------------------------------------------------------------
            |
            | Default behavior:
            | Use the first configured approval level.
            |
            | AMR behavior:
            | Starting level may explicitly be supplied as Level 2.
            |
            */

            $steps = $master->steps
                ->sortBy('approval_level');

            $firstStep = $startingLevel !== null
                ? $steps->firstWhere(
                    'approval_level',
                    $startingLevel
                )
                : $steps->first();

            if (! $firstStep) {

                throw new RuntimeException(
                    "Approval level [{$startingLevel}] is not configured on Approval Master [{$master->code}]."
                );
            }

            /*
            |--------------------------------------------------------------------------
            | Create Transaction Header
            |--------------------------------------------------------------------------
            */

            $transaction = ApprovalTransaction::create([
                'approval_master_id' =>
                    $master->getKey(),

                'document_type' =>
                    $documentType,

                'document_id' =>
                    $documentId,

                'document_no' =>
                    $documentNo,

                'current_level' =>
                    (int) $firstStep->approval_level,

                'status' =>
                    'PENDING',

                'submitted_at' =>
                    now(),

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
                $master,
                $startingLevel
            );

            /*
            |--------------------------------------------------------------------------
            | Notify Initial Approval Level
            |--------------------------------------------------------------------------
            |
            | The approval transaction and its snapshot steps
            | must exist before notifying the first approver.
            |
            */

            $transaction = $transaction->fresh([
                'approvalMaster',
                'steps',
            ]);

            app(
                \App\Services\Notifications\NotificationService::class
            )->notifyApprovalRequired(
                $transaction
            );

            /*
            |--------------------------------------------------------------------------
            | Return
            |--------------------------------------------------------------------------
            */

            return $transaction;
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

        /*
        |--------------------------------------------------------------------------
        | Material Requisition Special Rule
        |--------------------------------------------------------------------------
        |
        | MR uses only the first configured approval level.
        |
        | Direct Market also uses only the first configured approval level.
        |
        | Assignment Direct Market is different:
        | Level 1 is inherited from the approved Direct Market transaction,
        | while Level 2 becomes the active ADM approval level.
        |
        */

        $steps = $master->steps
            ->sortBy('approval_level');

        if (
            in_array(
                $transaction->document_type,
                [
                    'MATERIAL_REQUISITION',
                    'DIRECT_MARKET',
                ],
                true
            )
        ) {
            $steps = $steps->take(1);
        }

        /*
        |--------------------------------------------------------------------------
        | ADM: Resolve Approved Direct Market Level 1
        |--------------------------------------------------------------------------
        |
        | ADM has its own approval transaction.
        |
        | However, Approval Level 1 / Dept Head has already been completed
        | during Direct Market approval.
        |
        | Therefore ADM Level 1 is snapshotted as APPROVED from the
        | original Direct Market approval step.
        |
        */

        $directMarketApprovalStep = null;

        if (
            $transaction->document_type
            === 'ASSIGNMENT_DIRECT_MARKET'
        ) {

            $assignment =
                \App\Models\AssignmentDirectMarket::query()
                    ->findOrFail(
                        (int) $transaction->document_id
                    );

            $directMarketTransaction =
                \App\Models\ApprovalTransaction::query()
                    ->with([
                        'steps' => fn ($query) =>
                            $query
                                ->where(
                                    'approval_level',
                                    1
                                )
                                ->where(
                                    'status',
                                    'APPROVED'
                                )
                                ->latest('acted_at'),
                    ])
                    ->where(
                        'document_type',
                        'DIRECT_MARKET'
                    )
                    ->where(
                        'document_id',
                        (int) $assignment->direct_market_id
                    )
                    ->where(
                        'status',
                        'APPROVED'
                    )
                    ->latest('id')
                    ->first();

            $directMarketApprovalStep =
                $directMarketTransaction
                    ?->steps
                    ?->first();

            if (! $directMarketApprovalStep) {
                throw new \RuntimeException(
                    "Approved Direct Market approval Level 1 "
                    . "was not found for Assignment Direct Market "
                    . "[{$transaction->document_id}]."
                );
            }
        }

        foreach ($steps as $masterStep) {

            $role = $masterStep->role;

            $isInheritedAdmLevelOne =
                $transaction->document_type
                === 'ASSIGNMENT_DIRECT_MARKET'
                && (int) $masterStep->approval_level === 1;

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

                'status' =>
                    $isInheritedAdmLevelOne
                        ? 'APPROVED'
                        : 'PENDING',

                'action' =>
                    $isInheritedAdmLevelOne
                        ? $directMarketApprovalStep->action
                        : null,

                'approved_by' =>
                    $isInheritedAdmLevelOne
                        ? $directMarketApprovalStep->approved_by
                        : null,

                'acted_at' =>
                    $isInheritedAdmLevelOne
                        ? $directMarketApprovalStep->acted_at
                        : null,

                'remarks' =>
                    $isInheritedAdmLevelOne
                        ? $directMarketApprovalStep->remarks
                        : null,
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

    /**
     * Activate next approval level for a Material Requisition.
     *
     * Workflow:
     *
     * MR Approval 1
     *      ↓
     * AMR Buyer Update
     *      ↓
     * AMR Submit
     *      ↓
     * Activate MR Approval 2
     *
     * The existing approval transaction is reused.
     * No new approval transaction is created.
     */
    public function activateNextMaterialRequisitionLevel(
        int $purchaseRequisitionId,
    ): ApprovalTransaction {

        return DB::transaction(
            function () use ($purchaseRequisitionId): ApprovalTransaction {

                /*
                |--------------------------------------------------------------------------
                | Find Existing MR Approval Transaction
                |--------------------------------------------------------------------------
                */

                $transaction = ApprovalTransaction::query()
                    ->with([
                        'approvalMaster.steps' => fn ($query) =>
                            $query
                                ->orderBy('approval_level')
                                ->with('role'),

                        'steps',
                    ])
                    ->where(
                        'document_type',
                        'MATERIAL_REQUISITION'
                    )
                    ->where(
                        'document_id',
                        $purchaseRequisitionId
                    )
                    ->latest('id')
                    ->first();

                if (! $transaction) {

                    throw new RuntimeException(
                        "No approval transaction found for Material Requisition [{$purchaseRequisitionId}]."
                    );
                }

                /*
                |--------------------------------------------------------------------------
                | Transaction Must Still Be Pending
                |--------------------------------------------------------------------------
                |
                | Level 1 has been approved, but the complete MR approval
                | workflow is intentionally not finished yet.
                |
                */

                if (! $transaction->isPending()) {

                    throw new RuntimeException(
                        "Material Requisition approval transaction [{$transaction->id}] is not pending."
                    );
                }

                /*
                |--------------------------------------------------------------------------
                | Determine Next Approval Level
                |--------------------------------------------------------------------------
                */

                $currentLevel = (int) $transaction->current_level;

                $nextMasterStep = $transaction
                    ->approvalMaster
                    ->steps
                    ->sortBy('approval_level')
                    ->first(
                        fn ($step): bool =>
                            (int) $step->approval_level > $currentLevel
                    );

                if (! $nextMasterStep) {

                    throw new RuntimeException(
                        "No next approval level configured for Material Requisition approval transaction [{$transaction->id}]."
                    );
                }

                /*
                |--------------------------------------------------------------------------
                | Prevent Duplicate Activation
                |--------------------------------------------------------------------------
                */

                $existingStep = $transaction
                    ->steps()
                    ->where(
                        'approval_level',
                        (int) $nextMasterStep->approval_level
                    )
                    ->first();

                if ($existingStep) {

                    /*
                    |--------------------------------------------------------------------------
                    | Already Activated
                    |--------------------------------------------------------------------------
                    |
                    | Idempotent behavior:
                    | simply move transaction to this level if necessary.
                    |
                    */

                    $transaction->update([
                        'current_level' =>
                            (int) $nextMasterStep->approval_level,

                        'status' =>
                            'PENDING',

                        'updated_by' =>
                            Auth::id(),
                    ]);

                    return $transaction->fresh([
                        'approvalMaster',
                        'steps',
                    ]);
                }

                /*
                |--------------------------------------------------------------------------
                | Validate Approver Availability
                |--------------------------------------------------------------------------
                */

                $approvers = $this->getApproversForStep(
                    $nextMasterStep
                );

                if ($approvers->isEmpty()) {

                    $roleName =
                        $nextMasterStep->role?->name
                        ?? 'Unknown';

                    throw new RuntimeException(
                        "No active approver found for role [{$roleName}] on Material Requisition approval level [{$nextMasterStep->approval_level}]."
                    );
                }

                /*
                |--------------------------------------------------------------------------
                | Create Approval Transaction Step
                |--------------------------------------------------------------------------
                */

                $role = $nextMasterStep->role;

                $transaction->steps()->create([

                    'approval_master_step_id' =>
                        $nextMasterStep->getKey(),

                    'approval_level' =>
                        (int) $nextMasterStep->approval_level,

                    'role_id' =>
                        $role?->getKey(),

                    'role_code' =>
                        $role?->role_code,

                    'role_name' =>
                        $role?->name,

                    'status' =>
                        'PENDING',

                    'action' =>
                        null,

                    'approved_by' =>
                        null,

                    'acted_at' =>
                        null,

                    'remarks' =>
                        null,
                ]);

                /*
                |--------------------------------------------------------------------------
                | Move Transaction To Next Level
                |--------------------------------------------------------------------------
                */

                $transaction->update([

                    'current_level' =>
                        (int) $nextMasterStep->approval_level,

                    'status' =>
                        'PENDING',

                    'updated_by' =>
                        Auth::id(),
                ]);

                /*
                |--------------------------------------------------------------------------
                | Notify Next Approval Level
                |--------------------------------------------------------------------------
                */

                $transaction = $transaction->fresh([
                    'approvalMaster',
                    'steps',
                ]);

                app(
                    \App\Services\Notifications\NotificationService::class
                )->notifyNextApprovalLevel(
                    $transaction
                );

                return $transaction;
            }
        );
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

    /**
     * Approve Approval Transaction.
     *
     * Workflow:
     *
     * MATERIAL_REQUISITION
     *
     * Level 1
     *     ↓
     * Approve
     *     ↓
     * MR = Approved
     *     ↓
     * AMR = Created
     *     ↓
     * Approval Transaction = PENDING
     *     ↓
     * AMR Submit
     *     ↓
     * Activate next MR approval level
     *     ↓
     * Level 2 / Level 3 / ...
     *     ↓
     * Final Approval
     *     ↓
     * Approval Transaction = APPROVED
     *
     * Other document types:
     *
     * Current Level
     *     ↓
     * Approve
     *     ↓
     * Next configured transaction step
     *     ↓
     * Final Approval
     */
    public function approve(
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
                | Current Step Must Be Pending
                |--------------------------------------------------------------------------
                */

                if ($step->status !== 'PENDING') {

                    throw new RuntimeException(
                        "Approval level [{$step->approval_level}] is already [{$step->status}]."
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
                | MATERIAL REQUISITION SPECIAL WORKFLOW
                |--------------------------------------------------------------------------
                |
                | Level 1 is released by MR Approval.
                | The next level is activated only by AMR Submit.
                |
                | Final approval must be state-aware:
                |
                | - Never attempt Approved -> Approved.
                | - Never create a duplicate AMR.
                |
                */

                if (
                    $transaction->document_type
                    === 'MATERIAL_REQUISITION'
                ) {

                    /*
                    |--------------------------------------------------------------------------
                    | Determine Whether Another Approval Master Level Exists
                    |--------------------------------------------------------------------------
                    */

                    $nextMasterStep = $transaction
                        ->approvalMaster
                        ->steps
                        ->sortBy('approval_level')
                        ->first(
                            fn ($masterStep): bool =>
                                (int) $masterStep->approval_level
                                >
                                (int) $transaction->current_level
                        );

                    /*
                    |--------------------------------------------------------------------------
                    | MR HAS NEXT APPROVAL LEVEL
                    |--------------------------------------------------------------------------
                    |
                    | Do NOT create or activate it here.
                    | AMR Submit owns that transition.
                    |
                    */

                    if ($nextMasterStep) {

                        /*
                         * --------------------------------------------------------------------------
                         * Release MR to AMR after Level 1 approval
                         * --------------------------------------------------------------------------
                         *
                         * Level 1 approval is the business gate that releases the
                         * Material Requisition into the AMR workflow.
                         *
                         * IMPORTANT:
                         * - Do NOT create/activate Approval Level 2 here.
                         * - Level 2 is created later by AMR Submit through
                         *   activateNextMaterialRequisitionLevel().
                         * - The MR business status becomes Approved so that the
                         *   AMR service can safely create its snapshot.
                         * - The approval transaction itself remains PENDING because
                         *   the overall approval workflow is not yet complete.
                         */

                        $purchaseRequisition =
                            PurchaseRequisition::query()
                                ->find(
                                    (int) $transaction->document_id
                                );

                        if (! $purchaseRequisition) {
                            throw new RuntimeException(
                                "Material Requisition [{$transaction->document_id}] not found."
                            );
                        }

                        /*
                         * --------------------------------------------------------------------------
                         * Approve MR business document
                         * --------------------------------------------------------------------------
                         *
                         * PurchaseRequisitionService is state-aware, therefore
                         * this is safe for the normal Level 1 release path.
                         */

                        if ($purchaseRequisition->status !== 'Approved') {
                            $purchaseRequisitionService =
                                app(
                                    PurchaseRequisitionService::class
                                );

                            $purchaseRequisitionService->approve(
                                (int) $purchaseRequisition->getKey()
                            );
                        }

                        /*
                         * --------------------------------------------------------------------------
                         * Create AMR exactly once
                         * --------------------------------------------------------------------------
                         */

                        $assignment =
                            AssignmentMaterialRequisition::query()
                                ->where(
                                    'purchase_requisition_id',
                                    $purchaseRequisition->getKey()
                                )
                                ->latest('id')
                                ->first();

                        if (! $assignment) {

                            $assignmentService =
                                app(
                                    AssignmentMaterialRequisitionService::class
                                );

                            $assignment =
                                $assignmentService
                                    ->createFromApprovedPurchaseRequisition(
                                        (int) $purchaseRequisition->getKey()
                                    );
                        }

                        /*
                         * --------------------------------------------------------------------------
                         * Keep Approval Transaction open
                         * --------------------------------------------------------------------------
                         *
                         * Step 1 is already APPROVED above.
                         * Level 2 is intentionally NOT created here.
                         */

                        $transaction->update([
                            'status' =>
                                'PENDING',

                            'completed_at' =>
                                null,

                            'updated_by' =>
                                $approver->getKey(),
                        ]);



                        $transaction = $transaction->fresh([
                            'approvalMaster',
                            'steps',
                        ]);

                        app(
                            \App\Services\Notifications\NotificationService::class
                        )->notifyLevelApproved(
                            $transaction,
                            (int) $step->approval_level,
                            $step->role_name,
                        );

                        return $transaction;
                    }

                    /*
                    |--------------------------------------------------------------------------
                    | FINAL MR APPROVAL
                    |--------------------------------------------------------------------------
                    |
                    | The MR may already be Approved because Level 1
                    | released it into the AMR workflow.
                    |
                    | Therefore PurchaseRequisitionService::approve()
                    | is called only when the MR is not already Approved.
                    |--------------------------------------------------------------------------
                    */

                    $purchaseRequisition =
                        PurchaseRequisition::query()
                            ->find(
                                (int) $transaction->document_id
                            );

                    if (! $purchaseRequisition) {

                        throw new RuntimeException(
                            "Material Requisition [{$transaction->document_id}] not found."
                        );
                    }

                    if (
                        $purchaseRequisition->status
                        !== 'Approved'
                    ) {

                        $purchaseRequisitionService =
                            app(
                                PurchaseRequisitionService::class
                            );

                        $purchaseRequisitionService->approve(
                            (int) $purchaseRequisition->getKey()
                        );
                    }

                    /*
                    |--------------------------------------------------------------------------
                    | Ensure AMR Exists
                    |--------------------------------------------------------------------------
                    |
                    | In the normal multi-level workflow AMR already exists.
                    | Only create it for a genuine single-level/legacy case.
                    |--------------------------------------------------------------------------
                    */

                    $assignment =
                        AssignmentMaterialRequisition::query()
                            ->where(
                                'purchase_requisition_id',
                                $purchaseRequisition->getKey()
                            )
                            ->latest('id')
                            ->first();

                    if (! $assignment) {

                        $assignmentService =
                            app(
                                AssignmentMaterialRequisitionService::class
                            );

                        $assignment =
                            $assignmentService
                                ->createFromApprovedPurchaseRequisition(
                                    (int) $purchaseRequisition->getKey()
                                );
                    }

                    /*
                    |--------------------------------------------------------------------------
                    | Final MR Approval Transaction
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
                    | Final Approval Notification
                    |--------------------------------------------------------------------------
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

                    return $transaction;
                }

                if (
                    $transaction->document_type === 'DIRECT_MARKET'
                ) {
                    $directMarket = \App\Models\DirectMarket::query()
                        ->lockForUpdate()
                        ->find(
                            (int) $transaction->document_id
                        );

                    if (! $directMarket) {
                        throw new RuntimeException(
                            "Direct Market [{$transaction->document_id}] not found."
                        );
                    }

                    if (
                        $directMarket->status
                        !== \App\Models\DirectMarket::STATUS_SUBMITTED
                    ) {
                        throw new RuntimeException(
                            "Direct Market [{$directMarket->dm_no}] "
                            . 'is not in Submitted status for approval.'
                        );
                    }

                    /*
                    |--------------------------------------------------------------------------
                    | Approve Direct Market
                    |--------------------------------------------------------------------------
                    */

                    $directMarket->update([
                        'status' =>
                            \App\Models\DirectMarket::STATUS_APPROVED,

                        'updated_by' =>
                            $approver->getKey(),
                    ]);

                    /*
                    |--------------------------------------------------------------------------
                    | Create Assignment Direct Market
                    |--------------------------------------------------------------------------
                    */

                    app(
                        \App\Services\Purchasing\AssignmentDirectMarketService::class
                    )->createFromApprovedDirectMarket(
                        (int) $directMarket->getKey()
                    );

                    /*
                    |--------------------------------------------------------------------------
                    | Finalize DM Approval Transaction
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

                    $transaction = $transaction->fresh([
                        'approvalMaster',
                        'steps',
                    ]);

                    app(
                        \App\Services\Notifications\NotificationService::class
                    )->notifyFinalApproved(
                        $transaction
                    );

                    return $transaction;
                }

                if ($transaction->document_type === 'ASSIGNMENT_DIRECT_MARKET') {
                    $assignment = \App\Models\AssignmentDirectMarket::query()
                        ->lockForUpdate()
                        ->find(
                            (int) $transaction->document_id
                        );

                    if (! $assignment) {
                        throw new RuntimeException(
                            "Assignment Direct Market [{$transaction->document_id}] not found."
                        );
                    }

                    if (
                        $assignment->status
                        !== \App\Models\AssignmentDirectMarket::STATUS_WAITING_APPROVAL
                    ) {
                        throw new RuntimeException(
                            "Assignment Direct Market [{$assignment->document_no}] is not in Waiting Approval status for approval."
                        );
                    }

                    $assignment->update([
                        'status' =>
                            \App\Models\AssignmentDirectMarket::STATUS_APPROVED,

                        'updated_by' =>
                            $approver->getKey(),
                    ]);

                    $transaction->update([
                        'status' =>
                            'APPROVED',

                        'completed_at' =>
                            now(),

                        'updated_by' =>
                            $approver->getKey(),
                    ]);

                    $transaction = $transaction->fresh([
                        'approvalMaster',
                        'steps',
                    ]);

                    app(
                        \App\Services\Notifications\NotificationService::class
                    )->notifyFinalApproved(
                        $transaction
                    );

                    return $transaction;
                }

                $nextStep = $this->getNextStep(
                    $transaction
                );

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
                */

                if (
                    $transaction->document_type
                    === 'MATERIAL_REQUISITION'
                ) {

                    $purchaseRequisitionService =
                        app(
                            PurchaseRequisitionService::class
                        );

                    $purchaseRequisitionService->approve(
                        (int) $transaction->document_id
                    );

                    $assignmentService =
                        app(
                            AssignmentMaterialRequisitionService::class
                        );

                    $assignmentService
                        ->createFromApprovedPurchaseRequisition(
                            (int) $transaction->document_id
                        );
                }


                    /*
                    |--------------------------------------------------------------------------
                    | Purchase Order Integration
                    |--------------------------------------------------------------------------
                    |
                    | Final approval of PO:
                    |
                    | Approval Status = Approved
                    | Document Status = On Progress
                    |
                    */

                    if (
                        $transaction->document_type
                        === 'PURCHASE_ORDER'
                    ) {

                        $purchaseOrder =
                            PurchaseOrder::query()
                                ->lockForUpdate()
                                ->find(
                                    (int) $transaction->document_id
                                );

                        if (! $purchaseOrder) {
                            throw new RuntimeException(
                                "Purchase Order [{$transaction->document_id}] not found."
                            );
                        }

                        /*
                        |--------------------------------------------------------------------------
                        | Validate PO Approval State
                        |--------------------------------------------------------------------------
                        */

                        if (
                            ! $purchaseOrder->isSubmit()
                            || ! $purchaseOrder->isWaitingApproval()
                        ) {
                            throw new RuntimeException(
                                "Purchase Order [{$purchaseOrder->document_no}] is not in a valid state for final approval."
                            );
                        }

                        /*
                        |--------------------------------------------------------------------------
                        | Final PO Approval
                        |--------------------------------------------------------------------------
                        */

                        $purchaseOrder->update([
                            'status' =>
                                PurchaseOrder::STATUS_ON_PROGRESS,

                            'approval_status' =>
                                PurchaseOrder::APPROVAL_APPROVED,

                            'approved_by' =>
                                $approver->getKey(),

                            'approved_at' =>
                                now(),

                            'updated_by' =>
                                $approver->getKey(),
                        ]);
                    }

                    /*
                    |--------------------------------------------------------------------------
                    | Final Approval Notification
                    |--------------------------------------------------------------------------
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
                | MOVE TO NEXT CONFIGURED TRANSACTION LEVEL
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
                */

                $transaction = $transaction->fresh([
                    'approvalMaster',
                    'steps',
                ]);

                app(
                    \App\Services\Notifications\NotificationService::class
                )->notifyLevelApproved(
                    $transaction,
                    (int) $step->approval_level,
                    $step->role_name,
                );

                /*
                |--------------------------------------------------------------------------
                | Next Level Approval Notification
                |--------------------------------------------------------------------------
                */

                app(
                    \App\Services\Notifications\NotificationService::class
                )->notifyNextApprovalLevel(
                    $transaction
                );

                return $transaction;
            }
        );
    }

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
                | Get Current Approval Step
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
                | Current Step Must Be Pending
                |--------------------------------------------------------------------------
                */

                if ($step->status !== 'PENDING') {

                    throw new RuntimeException(
                        "Approval level [{$step->approval_level}] is already [{$step->status}]."
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
                | Purchase Order Integration
                |--------------------------------------------------------------------------
                |
                | PO rejection is a final document decision.
                |
                | Submit + Waiting Approval
                |          ↓
                | Rejected + Rejected
                |
                |--------------------------------------------------------------------------
                */

                if (
                    $transaction->document_type
                    === 'PURCHASE_ORDER'
                ) {

                    $purchaseOrder =
                        \App\Models\PurchaseOrder::query()
                            ->lockForUpdate()
                            ->find(
                                (int) $transaction->document_id
                            );

                    if (! $purchaseOrder) {

                        throw new RuntimeException(
                            "Purchase Order [{$transaction->document_id}] not found."
                        );
                    }

                    /*
                    |--------------------------------------------------------------------------
                    | Validate PO Approval State
                    |--------------------------------------------------------------------------
                    */

                    if (
                        ! $purchaseOrder->isSubmit()
                        || ! $purchaseOrder->isWaitingApproval()
                    ) {

                        throw new RuntimeException(
                            "Purchase Order [{$purchaseOrder->document_no}] is not in a valid state for rejection."
                        );
                    }

                    /*
                    |--------------------------------------------------------------------------
                    | Final PO Rejection
                    |--------------------------------------------------------------------------
                    */

                    $purchaseOrder->update([
                        'status' =>
                            \App\Models\PurchaseOrder::STATUS_REJECTED,

                        'approval_status' =>
                            \App\Models\PurchaseOrder::APPROVAL_REJECTED,

                        'updated_by' =>
                            $approver->getKey(),
                    ]);

                    /*
                    |--------------------------------------------------------------------------
                    | Refresh PO
                    |--------------------------------------------------------------------------
                    */

                    $purchaseOrder->refresh();

                    /*
                    |--------------------------------------------------------------------------
                    | Refresh Approval Transaction
                    |--------------------------------------------------------------------------
                    */

                    $transaction = $transaction->fresh([
                        'approvalMaster',
                        'steps',
                    ]);

                    /*
                    |--------------------------------------------------------------------------
                    | Rejected Notification
                    |--------------------------------------------------------------------------
                    |
                    | Notify the PO requester after the PO has successfully
                    | entered the Rejected state.
                    |
                    */

                    app(
                        \App\Services\Notifications\NotificationService::class
                    )->notifyRejected(
                        $transaction,
                        $approver,
                        $remarks,
                    );

                    /*
                    |--------------------------------------------------------------------------
                    | Return Fresh Transaction
                    |--------------------------------------------------------------------------
                    */

                    return $transaction;
                }

                /*
                |--------------------------------------------------------------------------
                | Material Requisition Integration
                |--------------------------------------------------------------------------
                |
                | Existing MR rejection behavior remains untouched.
                |
                |--------------------------------------------------------------------------
                */

                if (
                    $transaction->document_type
                    !== 'MATERIAL_REQUISITION'
                ) {

                    return $transaction->fresh([
                        'approvalMaster',
                        'steps',
                    ]);
                }

                /*
                |--------------------------------------------------------------------------
                | Load Material Requisition
                |--------------------------------------------------------------------------
                */

                $purchaseRequisition =
                    \App\Models\PurchaseRequisition::query()
                        ->find(
                            (int) $transaction->document_id
                        );

                if (! $purchaseRequisition) {

                    throw new RuntimeException(
                        "Material Requisition [{$transaction->document_id}] not found."
                    );
                }

                /*
                |--------------------------------------------------------------------------
                | Find Existing AMR
                |--------------------------------------------------------------------------
                |
                | AMR is linked to the approved Material Requisition.
                |
                |--------------------------------------------------------------------------
                */

                $assignment =
                    \App\Models\AssignmentMaterialRequisition::query()
                        ->where(
                            'purchase_requisition_id',
                            $purchaseRequisition->getKey()
                        )
                        ->latest('id')
                        ->first();

                /*
                |--------------------------------------------------------------------------
                | Reject Material Requisition
                |--------------------------------------------------------------------------
                |
                | State-aware.
                |
                | If MR is already Rejected:
                |     do nothing.
                |
                | If MR is still active:
                |     use the existing MR Service.
                |
                |--------------------------------------------------------------------------
                */

                if (
                    $purchaseRequisition->status
                    !== \App\Models\PurchaseRequisition::STATUS_REJECTED
                ) {

                    if (
                        $purchaseRequisition->status
                        === \App\Models\PurchaseRequisition::STATUS_WAITING_APPROVAL
                    ) {

                        $purchaseRequisitionService =
                            app(
                                \App\Services\Purchasing\PurchaseRequisitionService::class
                            );

                        $purchaseRequisitionService->reject(
                            (int) $purchaseRequisition->getKey()
                        );

                    } elseif (
                        $purchaseRequisition->status
                        === \App\Models\PurchaseRequisition::STATUS_APPROVED
                    ) {

                        $purchaseRequisition->update([
                            'status' =>
                                \App\Models\PurchaseRequisition::STATUS_REJECTED,

                            'updated_by' =>
                                $approver->getKey(),
                        ]);
                    }
                }

                /*
                |--------------------------------------------------------------------------
                | Reject Existing AMR
                |--------------------------------------------------------------------------
                |
                | AMR must only be rejected when it is currently
                | waiting for approval.
                |
                | We deliberately DO NOT delete the AMR.
                |
                |--------------------------------------------------------------------------
                */

                if ($assignment) {

                    if (
                        $assignment->status
                        === \App\Models\AssignmentMaterialRequisition::STATUS_WAITING_APPROVAL
                    ) {

                        $assignmentService =
                            app(
                                \App\Services\Purchasing\AssignmentMaterialRequisitionService::class
                            );

                        $assignmentService->reject(
                            (int) $assignment->getKey()
                        );
                    }
                }

                /*
                |--------------------------------------------------------------------------
                | Refresh Business Documents
                |--------------------------------------------------------------------------
                */

                $purchaseRequisition->refresh();

                if ($assignment) {
                    $assignment->refresh();
                }

                /*
                |--------------------------------------------------------------------------
                | Refresh Approval Transaction
                |--------------------------------------------------------------------------
                */

                $transaction = $transaction->fresh([
                    'approvalMaster',
                    'steps',
                ]);

                /*
                |--------------------------------------------------------------------------
                | Rejected Notification
                |--------------------------------------------------------------------------
                |
                | Notify requester only after the business documents have
                | successfully entered their rejected states.
                |
                |--------------------------------------------------------------------------
                */

                app(
                    \App\Services\Notifications\NotificationService::class
                )->notifyRejected(
                    $transaction,
                    $approver,
                    $remarks,
                );

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
    |
    | Standard behavior:
    | Validate all required approval levels.
    |
    | Material Requisition special behavior:
    | Validate only the first approval level because the next level
    | is activated later when AMR is submitted.
    |
    */

    public function validateApproverAvailability(
        ApprovalMaster $master
    ): void {
        $this->validateApproverAvailabilityForDocument(
            $master,
            null
        );
    }

    public function validateApproverAvailabilityForDocument(
        ApprovalMaster $master,
        ?string $documentType
    ): void {

        $steps = $master->steps
            ->sortBy('approval_level');

        /*
        |--------------------------------------------------------------------------
        | MR = First Approval Only
        |--------------------------------------------------------------------------
        */

        if ($documentType === 'MATERIAL_REQUISITION') {
            $steps = $steps->take(1);
        }

        foreach ($steps as $step) {

            if (! $step->is_required) {
                continue;
            }

            $approvers = $this->getApproversForStep(
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

        if ($step->status !== 'PENDING') {
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