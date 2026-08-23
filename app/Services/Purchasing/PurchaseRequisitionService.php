<?php

declare(strict_types=1);

namespace App\Services\Purchasing;

use App\Models\ApprovalMaster;
use App\Models\PurchaseRequisition;
use App\Services\Approval\ApprovalTransactionService;
use Illuminate\Database\DatabaseManager;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;
use RuntimeException;
use App\Services\Notifications\NotificationService;

class PurchaseRequisitionService
{

    /**
     * Constructor.
     */
    public function __construct(
        protected PurchaseRequisition $model,
        protected PurchaseRequisitionItemService $itemService,
        protected DatabaseManager $db,
        protected ApprovalTransactionService $approvalTransactionService,
        protected NotificationService $notificationService,
    ) {
    }

    /*
    |--------------------------------------------------------------------------
    | CRUD
    |--------------------------------------------------------------------------
    */

    /**
     * Create Purchase Requisition.
     *
     * @throws ValidationException
     */
    public function create(array $data): PurchaseRequisition
    {
        $this->validateCreate($data);

        return $this->db->transaction(function () use ($data) {

            $data['status'] ??= PurchaseRequisition::STATUS_DRAFT;
            $data['created_by'] = $this->currentUserId();

            $purchaseRequisition = $this->model->create($data);

            return $purchaseRequisition->refresh();
        });
    }

    /**
     * Update Purchase Requisition.
     *
     * @throws ValidationException
     */
    public function update(
        int $id,
        array $data,
    ): PurchaseRequisition {

        $purchaseRequisition = $this->findById($id);

        $this->validateUpdate($data);

        $this->validateDocument($purchaseRequisition);

        return $this->db->transaction(function () use (
            $purchaseRequisition,
            $data
        ) {

            $data['updated_by'] = $this->currentUserId();

            $purchaseRequisition->update($data);

            return $this->refreshItems(
                $purchaseRequisition->id
            );
        });
    }

    /**
     * Delete Purchase Requisition.
     */
    public function delete(
        int $id,
    ): bool {

        $purchaseRequisition = $this->findById($id);

        $this->validateDocument($purchaseRequisition);

        return $this->db->transaction(function () use (
            $purchaseRequisition
        ) {

            $purchaseRequisition->update([
                'deleted_by' => $this->currentUserId(),
            ]);

            return (bool) $purchaseRequisition->delete();
        });
    }

    /*
    |--------------------------------------------------------------------------
    | APPROVAL WORKFLOW
    |--------------------------------------------------------------------------
    */

    /**
     * Submit Material Requisition for approval.
     *
     * Workflow:
     *
     * Draft
     *   ↓
     * Submitted
     *   ↓
     * Pending Approval
     *   ↓
     * Approval Transaction
     *
     * Everything is executed inside one database transaction.
     *
     * @throws ValidationException
     * @throws RuntimeException
     */
    public function submit(
        int $id,
    ): PurchaseRequisition {

        $purchaseRequisition = $this->findById($id);

        /*
        |--------------------------------------------------------------------------
        | Validate document before workflow execution.
        |--------------------------------------------------------------------------
        */

        $this->validateSubmit($purchaseRequisition);

        return $this->db->transaction(function () use (
            $purchaseRequisition
        ) {

            /*
            |--------------------------------------------------------------------------
            | 1. Draft → Submitted
            |--------------------------------------------------------------------------
            */

            $this->validateWorkflowTransition(
                $purchaseRequisition,
                PurchaseRequisition::STATUS_SUBMITTED,
            );

            $purchaseRequisition->update([
                'status' => PurchaseRequisition::STATUS_SUBMITTED,
                'updated_by' => $this->currentUserId(),
            ]);

            /*
            |--------------------------------------------------------------------------
            | 2. Resolve active Approval Master.
            |--------------------------------------------------------------------------
            */

            $approvalMaster = $this->approvalTransactionService
                ->getActiveMaster(
                    'MATERIAL_REQUISITION'
                );

            if (! $approvalMaster instanceof ApprovalMaster) {

                throw new RuntimeException(
                    'No active Approval Master is configured for Material Requisition.'
                );
            }

            /*
            |--------------------------------------------------------------------------
            | 3. Validate approver availability.
            |--------------------------------------------------------------------------
            |
            | Do this BEFORE creating the transaction.
            |
            | This prevents a Material Requisition from entering
            | Pending Approval when nobody is available to approve it.
            |
            */

            $this->approvalTransactionService
                ->validateApproverAvailability(
                    $approvalMaster
                );

            /*
            |--------------------------------------------------------------------------
            | 4. Create Approval Transaction.
            |--------------------------------------------------------------------------
            */

            $approvalTransaction =
                $this->approvalTransactionService->create(
                    'MATERIAL_REQUISITION',
                    $purchaseRequisition->getKey(),
                    $purchaseRequisition->pr_no,
                );

            /*
            |--------------------------------------------------------------------------
            | 5. Submitted → Pending Approval
            |--------------------------------------------------------------------------
            */

            $this->validateWorkflowTransition(
                $purchaseRequisition,
                PurchaseRequisition::STATUS_WAITING_APPROVAL,
            );

            $purchaseRequisition->update([
                'status' => PurchaseRequisition::STATUS_WAITING_APPROVAL,
                'updated_by' => $this->currentUserId(),
            ]);

            /*
            |--------------------------------------------------------------------------
            | Approval Notification
            |--------------------------------------------------------------------------
            |
            | Notify users assigned to the current approval role
            | after the Material Requisition has entered
            | Pending Approval state.
            |
            */

            $this->notificationService
                ->notifyApprovalRequired(
                    $approvalTransaction->fresh([
                        'steps',
                    ])
                );

            /*
            |--------------------------------------------------------------------------
            | 6. Return refreshed document.
            |--------------------------------------------------------------------------
            */

            return $purchaseRequisition->refresh();
        });
    }


    /**
     * Reject Material Requisition.
     *
     * AM-5.6.1-D
     *
     * Workflow:
     *
     * Pending Approval → Rejected
     *
     * This method is intentionally kept in the
     * Purchase Requisition service so the document
     * workflow remains centralized.
     */
    public function reject(
        int $id,
    ): PurchaseRequisition {

        $purchaseRequisition = $this->findById($id);

        /*
        |--------------------------------------------------------------------------
        | Validate Current State
        |--------------------------------------------------------------------------
        */

        if (
            $purchaseRequisition->status
            !== PurchaseRequisition::STATUS_WAITING_APPROVAL
        ) {

            throw new RuntimeException(
                'Material Requisition can only be rejected while it is Pending Approval.'
            );
        }

        /*
        |--------------------------------------------------------------------------
        | Workflow Transition
        |--------------------------------------------------------------------------
        */

        $this->validateWorkflowTransition(
            $purchaseRequisition,
            PurchaseRequisition::STATUS_REJECTED,
        );

        /*
        |--------------------------------------------------------------------------
        | Update Document
        |--------------------------------------------------------------------------
        */

        return $this->db->transaction(
            function () use (
                $purchaseRequisition
            ): PurchaseRequisition {

                $purchaseRequisition->update([

                    'status' =>
                        PurchaseRequisition::STATUS_REJECTED,

                    'updated_by' =>
                        $this->currentUserId(),

                ]);

                return $purchaseRequisition->refresh();
            }
        );
    }


    /**
     * Approve Material Requisition.
     *
     * AM-5.5.3
     *
     * This method is exclusively used after the Approval Engine
     * has completed the configured approval workflow.
     *
     * Workflow:
     *
     * Pending Approval → Approved
     */
    public function approve(
        int $id,
    ): PurchaseRequisition {

        $purchaseRequisition = $this->findById($id);

        return $this->db->transaction(function () use (
            $purchaseRequisition
        ) {

            /*
            |--------------------------------------------------------------------------
            | Validate Workflow Transition
            |--------------------------------------------------------------------------
            |
            | Only a Material Requisition currently waiting for approval
            | may be approved.
            |
            */

            $this->validateWorkflowTransition(
                $purchaseRequisition,
                PurchaseRequisition::STATUS_APPROVED,
            );

            /*
            |--------------------------------------------------------------------------
            | Update Material Requisition Status
            |--------------------------------------------------------------------------
            */

            $purchaseRequisition->update([
                'status' =>
                    PurchaseRequisition::STATUS_APPROVED,

                'updated_by' =>
                    $this->currentUserId(),
            ]);

            /*
            |--------------------------------------------------------------------------
            | Return Refreshed Document
            |--------------------------------------------------------------------------
            */

            return $purchaseRequisition->refresh();
        });
    }


    /**
     * Validate Material Requisition before submission.
     *
     * @throws RuntimeException
     */
    protected function validateSubmit(
        PurchaseRequisition $purchaseRequisition,
    ): void {

        /*
        |--------------------------------------------------------------------------
        | Only Draft can be submitted.
        |--------------------------------------------------------------------------
        */

        if (
            $purchaseRequisition->status
            !== PurchaseRequisition::STATUS_DRAFT
        ) {

            throw new RuntimeException(
                "Material Requisition [{$purchaseRequisition->pr_no}] " .
                "cannot be submitted because its current status is " .
                "[{$purchaseRequisition->status}]."
            );
        }

        /*
        |--------------------------------------------------------------------------
        | Document must be editable.
        |--------------------------------------------------------------------------
        */

        if (! $purchaseRequisition->canEdit()) {

            throw new RuntimeException(
                "Material Requisition [{$purchaseRequisition->pr_no}] " .
                "cannot be submitted because it is not editable."
            );
        }

        /*
        |--------------------------------------------------------------------------
        | At least one item is mandatory.
        |--------------------------------------------------------------------------
        */

        if (! $purchaseRequisition->items()->exists()) {

            throw new RuntimeException(
                'Material Requisition must contain at least one item before it can be submitted.'
            );
        }

        /*
        |--------------------------------------------------------------------------
        | Workflow transition validation.
        |--------------------------------------------------------------------------
        */

        $this->validateWorkflowTransition(
            $purchaseRequisition,
            PurchaseRequisition::STATUS_SUBMITTED,
        );
    }

    /**
     * Get current authenticated user ID.
     */
    protected function currentUserId(): ?int
    {
        return auth()->id();
    }

    /*
    |--------------------------------------------------------------------------
    | Validation
    |--------------------------------------------------------------------------
    */

    /**
     * Validate Create Request.
     *
     * @throws ValidationException
     */
    protected function validateCreate(
        array $data,
    ): void {

        Validator::make($data, [

            'company_id' => [
                'required',
                'integer',
                'exists:companies,id',
            ],

            'business_unit_id' => [
                'required',
                'integer',
                'exists:business_units,id',
            ],

            'branch_id' => [
                'required',
                'integer',
                'exists:branches,id',
            ],

            'department_id' => [
                'required',
                'integer',
                'exists:departments,id',
            ],

            'section_id' => [
                'required',
                'integer',
                'exists:sections,id',
            ],

            'cost_center_id' => [
                'required',
                'integer',
                'exists:cost_centers,id',
            ],

            'warehouse_id' => [
                'required',
                'integer',
                'exists:warehouses,id',
            ],

            'requester_id' => [
                'required',
                'integer',
                'exists:users,id',
            ],

            'request_date' => [
                'required',
                'date',
            ],

            'required_date' => [
                'required',
                'date',
                'after_or_equal:request_date',
            ],

            'priority' => [
                'required',
                'string',
            ],

            'reference_no' => [
                'nullable',
                'string',
                'max:100',
            ],

            'delivery_location' => [
                'required',
                'string',
                'max:255',
            ],

            'remarks' => [
                'nullable',
                'string',
            ],

        ])->validate();
    }

    /**
     * Validate Update Request.
     *
     * @throws ValidationException
     */
    protected function validateUpdate(
        array $data,
    ): void {

        Validator::make($data, [

            'company_id' => [
                'sometimes',
                'integer',
                'exists:companies,id',
            ],

            'business_unit_id' => [
                'sometimes',
                'integer',
                'exists:business_units,id',
            ],

            'branch_id' => [
                'sometimes',
                'integer',
                'exists:branches,id',
            ],

            'department_id' => [
                'sometimes',
                'integer',
                'exists:departments,id',
            ],

            'section_id' => [
                'sometimes',
                'integer',
                'exists:sections,id',
            ],

            'cost_center_id' => [
                'sometimes',
                'integer',
                'exists:cost_centers,id',
            ],

            'warehouse_id' => [
                'sometimes',
                'integer',
                'exists:warehouses,id',
            ],

            'requester_id' => [
                'sometimes',
                'integer',
                'exists:users,id',
            ],

            'request_date' => [
                'sometimes',
                'date',
            ],

            'required_date' => [
                'sometimes',
                'date',
                'after_or_equal:request_date',
            ],

            'priority' => [
                'sometimes',
                'string',
            ],

            'reference_no' => [
                'sometimes',
                'nullable',
                'string',
                'max:100',
            ],

            'delivery_location' => [
                'sometimes',
                'string',
                'max:255',
            ],

            'remarks' => [
                'sometimes',
                'nullable',
                'string',
            ],

        ])->validate();
    }

    /**
     * Validate document before modification.
     */

    protected function validateDocument(
        PurchaseRequisition $purchaseRequisition,
    ): void {

        /*
        |--------------------------------------------------------------------------
        | Document Status
        |--------------------------------------------------------------------------
        |
        | Only Draft Material Requisition may be modified.
        |
        */

        if (! $this->canModify($purchaseRequisition)) {

            throw new RuntimeException(
                'Material Requisition cannot be modified because its current status does not allow this operation.'
            );
        }
    }


    /**
     * Validate workflow transition.
     */
    protected function validateWorkflowTransition(
        PurchaseRequisition $purchaseRequisition,
        string $targetStatus,
    ): void {

        $allowed = $this->getAllowedTransitions();

        $current = $purchaseRequisition->status;

        if (! in_array(
            $targetStatus,
            $allowed[$current] ?? [],
            true,
        )) {

            throw new RuntimeException(
                "Workflow transition from [{$current}] to [{$targetStatus}] is not allowed."
            );
        }
    }

    /**
     * Allowed workflow transitions.
     */
    protected function getAllowedTransitions(): array
    {
        return [

            PurchaseRequisition::STATUS_DRAFT => [

                PurchaseRequisition::STATUS_SUBMITTED,

                PurchaseRequisition::STATUS_CANCELLED,

            ],

            PurchaseRequisition::STATUS_SUBMITTED => [

                PurchaseRequisition::STATUS_WAITING_APPROVAL,

                PurchaseRequisition::STATUS_CANCELLED,

            ],

            PurchaseRequisition::STATUS_WAITING_APPROVAL => [

                PurchaseRequisition::STATUS_APPROVED,

                PurchaseRequisition::STATUS_REJECTED,

            ],

            PurchaseRequisition::STATUS_APPROVED => [

                PurchaseRequisition::STATUS_COMPLETED,

                PurchaseRequisition::STATUS_CANCELLED,

            ],

            PurchaseRequisition::STATUS_COMPLETED => [],

            PurchaseRequisition::STATUS_REJECTED => [],

            PurchaseRequisition::STATUS_CANCELLED => [],
        ];
    }

    /*
    |--------------------------------------------------------------------------
    | Item Integration
    |--------------------------------------------------------------------------
    */

    public function refreshHeader(
        int $id,
    ): PurchaseRequisition {

        return $this->findById($id)
            ->refresh();
    }

    public function refreshItems(
        int $id,
    ): PurchaseRequisition {

        return $this->findById($id)
            ->refresh();
    }

    public function hasItems(
        int $id,
    ): bool {

        return $this->findById($id)
            ->items()
            ->exists();
    }

    public function itemCount(
        int $id,
    ): int {

        return $this->findById($id)
            ->items()
            ->count();
    }

    /*
    |--------------------------------------------------------------------------
    | Helper
    |--------------------------------------------------------------------------
    */

    /**
     * Find Purchase Requisition.
     *
     * @throws ModelNotFoundException
     */
    protected function findById(
        int $id,
    ): PurchaseRequisition {

        return $this->model
            ->newQuery()
            ->findOrFail($id);
    }

    /**
     * Determine whether the Purchase Requisition
     * can be modified based on current business rules.
     */
    protected function canModify(
        PurchaseRequisition $purchaseRequisition,
    ): bool {

        return $purchaseRequisition->canEdit();
    }
}