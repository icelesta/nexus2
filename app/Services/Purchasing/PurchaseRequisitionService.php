<?php

declare(strict_types=1);

namespace App\Services\Purchasing;

use App\Models\PurchaseRequisition;
use Illuminate\Database\DatabaseManager;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;
use RuntimeException;

class PurchaseRequisitionService
{
    /**
     * Constructor.
     */
    public function __construct(
        protected PurchaseRequisition $model,
        protected PurchaseRequisitionItemService $itemService,
        protected DatabaseManager $db,
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
            $data['created_by'] = auth()->id();

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

            $data['updated_by'] = auth()->id();

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
                'deleted_by' => auth()->id(),
            ]);

            return (bool) $purchaseRequisition->delete();

        });
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
     * Validate document before workflow execution.
     */
    protected function validateDocument(
        PurchaseRequisition $purchaseRequisition,
    ): void {

        if (! $this->canModify($purchaseRequisition)) {

            throw new RuntimeException(
                'Purchase Requisition cannot be modified because its current status does not allow this operation.'
            );
        }

        if (! $purchaseRequisition
            ->items()
            ->exists()) {

            throw new RuntimeException(
                'Purchase Requisition must contain at least one item before it can be submitted.'
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

        return $this->findById($id)->refresh();

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
     *
     * Business rules remain inside the model so the
     * service stays independent from workflow logic.
     */
    protected function canModify(
        PurchaseRequisition $purchaseRequisition,
    ): bool {

        return $purchaseRequisition->canEdit();
    }

}