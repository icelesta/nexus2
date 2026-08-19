<?php

declare(strict_types=1);

namespace App\Services\Purchasing;

use App\Models\ApprovalMasterStep;
use App\Models\ApprovalTransaction;
use App\Models\AssignmentMaterialRequisition;
use App\Models\PurchaseRequisition;

use Illuminate\Database\DatabaseManager;
use RuntimeException;

class GeneratePurchaseOrderEligibilityService
{
    /*
    |--------------------------------------------------------------------------
    | Document Type
    |--------------------------------------------------------------------------
    */

    private const MATERIAL_REQUISITION =
        'MATERIAL_REQUISITION';

    /*
    |--------------------------------------------------------------------------
    | Constructor
    |--------------------------------------------------------------------------
    */

    public function __construct(
        protected DatabaseManager $db,
    ) {
    }

    /*
    |--------------------------------------------------------------------------
    | Evaluate
    |--------------------------------------------------------------------------
    */

    /**
     * Evaluate whether an AMR is eligible
     * for Purchase Order generation.
     *
     * This service DOES NOT generate a Purchase Order.
     *
     * It only evaluates the business and approval gates.
     */
    public function evaluate(
        int|AssignmentMaterialRequisition $assignment,
    ): array {

        /*
        |--------------------------------------------------------------------------
        | Load Assignment
        |--------------------------------------------------------------------------
        */

        if (
            $assignment instanceof AssignmentMaterialRequisition
        ) {
            $assignment->loadMissing([
                'purchaseRequisition',
                'items',
                'purchaseOrder',
            ]);
        } else {
            $assignment =
                AssignmentMaterialRequisition::query()
                    ->with([
                        'purchaseRequisition',
                        'items',
                        'purchaseOrder',
                    ])
                    ->findOrFail($assignment);
        }

        /*
        |--------------------------------------------------------------------------
        | Gate 1 — AMR Status
        |--------------------------------------------------------------------------
        */

        $amrStatusPassed =
            $assignment->status
            === AssignmentMaterialRequisition::STATUS_WAITING_APPROVAL;

        /*
        |--------------------------------------------------------------------------
        | Gate 2 — Material Requisition
        |--------------------------------------------------------------------------
        */

        $purchaseRequisition =
            $assignment->purchaseRequisition;

        $mrStatusPassed =
            $purchaseRequisition
            && $purchaseRequisition->status
                === PurchaseRequisition::STATUS_APPROVED;

        /*
        |--------------------------------------------------------------------------
        | Gate 3 — MR Approval Transaction
        |--------------------------------------------------------------------------
        */

        $approvalTransaction =
            ApprovalTransaction::query()
                ->with([
                    'steps',
                ])
                ->where(
                    'document_type',
                    self::MATERIAL_REQUISITION,
                )
                ->where(
                    'document_id',
                    $assignment->purchase_requisition_id,
                )
                ->first();

        $approvalTransactionPassed =
            $approvalTransaction
            && $approvalTransaction->status === 'APPROVED';

        /*
        |--------------------------------------------------------------------------
        | Gate 4 — Required Approval Steps
        |--------------------------------------------------------------------------
        */

        $approvalStepResult =
            $this->evaluateRequiredApprovalSteps(
                $approvalTransaction,
            );

        /*
        |--------------------------------------------------------------------------
        | Gate 5 — Assignment Items
        |--------------------------------------------------------------------------
        */

        $itemResult =
            $this->evaluateItems(
                $assignment,
            );

        /*
        |--------------------------------------------------------------------------
        | Gate 6 — Supplier Consistency
        |--------------------------------------------------------------------------
        */

        $supplierResult =
            $this->evaluateSupplier(
                $assignment,
            );

        /*
        |--------------------------------------------------------------------------
        | Gate 7 — Existing Purchase Order
        |--------------------------------------------------------------------------
        */

        $existingPurchaseOrder =
            $assignment->purchaseOrder;

        $duplicatePassed =
            ! $existingPurchaseOrder;

        /*
        |--------------------------------------------------------------------------
        | Overall Eligibility
        |--------------------------------------------------------------------------
        */

        $eligible =
            $amrStatusPassed
            && $mrStatusPassed
            && $approvalTransactionPassed
            && $approvalStepResult['passed']
            && $itemResult['passed']
            && $supplierResult['passed']
            && $duplicatePassed;

        /*
        |--------------------------------------------------------------------------
        | Return Evaluation
        |--------------------------------------------------------------------------
        */

        return [

            'eligible' => $eligible,

            /*
            |--------------------------------------------------------------------------
            | Document
            |--------------------------------------------------------------------------
            */

            'assignment' => [
                'id' => $assignment->id,
                'document_no' => $assignment->document_no,
                'status' => $assignment->status,
            ],

            'material_requisition' => [
                'id' =>
                    $assignment->purchase_requisition_id,

                'document_no' =>
                    $assignment->pr_number
                    ?? $purchaseRequisition?->pr_no,

                'status' =>
                    $purchaseRequisition?->status,
            ],

            /*
            |--------------------------------------------------------------------------
            | Approval
            |--------------------------------------------------------------------------
            */

            'approval' => [

                'transaction_id' =>
                    $approvalTransaction?->id,

                'status' =>
                    $approvalTransaction?->status,

                'current_level' =>
                    $approvalTransaction?->current_level,

                'transaction_passed' =>
                    $approvalTransactionPassed,

                'required_steps_passed' =>
                    $approvalStepResult['passed'],

                'required_steps' =>
                    $approvalStepResult['steps'],

            ],

            /*
            |--------------------------------------------------------------------------
            | Item
            |--------------------------------------------------------------------------
            */

            'items' => $itemResult,

            /*
            |--------------------------------------------------------------------------
            | Supplier
            |--------------------------------------------------------------------------
            */

            'supplier' => $supplierResult,

            /*
            |--------------------------------------------------------------------------
            | Existing PO
            |--------------------------------------------------------------------------
            */

            'purchase_order' => [

                'exists' =>
                    $existingPurchaseOrder !== null,

                'id' =>
                    $existingPurchaseOrder?->id,

                'document_no' =>
                    $existingPurchaseOrder?->document_no,

                'passed' =>
                    $duplicatePassed,

            ],

        ];
    }

    /*
    |--------------------------------------------------------------------------
    | Can Generate
    |--------------------------------------------------------------------------
    */

    /**
     * Return TRUE only when all Generate PO gates pass.
     */
    public function canGenerate(
        int|AssignmentMaterialRequisition $assignment,
    ): bool {

        return $this->evaluate(
            $assignment
        )['eligible'];
    }

    /*
    |--------------------------------------------------------------------------
    | Assert Eligible
    |--------------------------------------------------------------------------
    */

    /**
     * Throw RuntimeException when Generate PO
     * is not allowed.
     */
    public function assertEligible(
        int|AssignmentMaterialRequisition $assignment,
    ): AssignmentMaterialRequisition {

        if (
            $assignment instanceof AssignmentMaterialRequisition
        ) {
            $record = $assignment;
        } else {
            $record =
                AssignmentMaterialRequisition::query()
                    ->findOrFail($assignment);
        }

        $evaluation =
            $this->evaluate($record);

        if ($evaluation['eligible']) {
            return $record;
        }

        $messages = [];

        /*
        |--------------------------------------------------------------------------
        | AMR Status
        |--------------------------------------------------------------------------
        */

        if (
            ! $evaluation['assignment']['status']
            === AssignmentMaterialRequisition::STATUS_WAITING_APPROVAL
        ) {
            $messages[] =
                'Assignment Material Requisition is not in Waiting Approval status.';
        }

        /*
        |--------------------------------------------------------------------------
        | MR Status
        |--------------------------------------------------------------------------
        */

        if (
            ! $evaluation['material_requisition']['status']
            || $evaluation['material_requisition']['status']
                !== PurchaseRequisition::STATUS_APPROVED
        ) {
            $messages[] =
                'Material Requisition has not completed approval.';
        }

        /*
        |--------------------------------------------------------------------------
        | Approval Transaction
        |--------------------------------------------------------------------------
        */

        if (
            ! $evaluation['approval']['transaction_passed']
        ) {
            $messages[] =
                'Material Requisition approval transaction is not fully approved.';
        }

        /*
        |--------------------------------------------------------------------------
        | Approval Steps
        |--------------------------------------------------------------------------
        */

        if (
            ! $evaluation['approval']['required_steps_passed']
        ) {

            foreach (
                $evaluation['approval']['required_steps']
                as $step
            ) {

                if (! $step['passed']) {

                    $messages[] =
                        "Approval Level {$step['approval_level']} "
                        . "({$step['role_name']}) "
                        . "is {$step['status']}.";
                }
            }
        }

        /*
        |--------------------------------------------------------------------------
        | Items
        |--------------------------------------------------------------------------
        */

        if (
            ! $evaluation['items']['passed']
        ) {

            foreach (
                $evaluation['items']['errors']
                as $error
            ) {
                $messages[] = $error;
            }
        }

        /*
        |--------------------------------------------------------------------------
        | Supplier
        |--------------------------------------------------------------------------
        */

        if (
            ! $evaluation['supplier']['passed']
        ) {

            foreach (
                $evaluation['supplier']['errors']
                as $error
            ) {
                $messages[] = $error;
            }
        }

        /*
        |--------------------------------------------------------------------------
        | Existing PO
        |--------------------------------------------------------------------------
        */

        if (
            ! $evaluation['purchase_order']['passed']
        ) {
            $messages[] =
                'Purchase Order has already been generated for this Assignment Material Requisition.';
        }

        /*
        |--------------------------------------------------------------------------
        | Throw
        |--------------------------------------------------------------------------
        */

        throw new RuntimeException(
            implode(' ', $messages)
        );
    }

    /*
    |--------------------------------------------------------------------------
    | Required Approval Steps
    |--------------------------------------------------------------------------
    */

    protected function evaluateRequiredApprovalSteps(
        ?ApprovalTransaction $transaction,
    ): array {

        /*
        |--------------------------------------------------------------------------
        | No Transaction
        |--------------------------------------------------------------------------
        */

        if (! $transaction) {

            return [

                'passed' => false,

                'steps' => [],

            ];
        }

        /*
        |--------------------------------------------------------------------------
        | Required Levels
        |--------------------------------------------------------------------------
        |
        | Read requirement from Approval Master.
        |
        | Never hard-code Supervisor / Manager.
        |
        */

        $requiredLevels =
            ApprovalMasterStep::query()
                ->where(
                    'approval_master_id',
                    $transaction->approval_master_id,
                )
                ->where(
                    'is_required',
                    true,
                )
                ->orderBy(
                    'approval_level',
                )
                ->get();

        /*
        |--------------------------------------------------------------------------
        | Transaction Steps
        |--------------------------------------------------------------------------
        */

        $transactionSteps =
            $transaction
                ->steps
                ->keyBy(
                    fn ($step) =>
                        (int) $step->approval_level
                );

        /*
        |--------------------------------------------------------------------------
        | Evaluate
        |--------------------------------------------------------------------------
        */

        $steps = [];

        $allPassed = true;

        foreach (
            $requiredLevels as $masterStep
        ) {

            $level =
                (int) $masterStep->approval_level;

            $transactionStep =
                $transactionSteps->get($level);

            $status =
                $transactionStep?->status
                ?? 'NOT ACTIVATED';

            $passed =
                $transactionStep !== null
                && $transactionStep->status === 'APPROVED';

            if (! $passed) {
                $allPassed = false;
            }

            $steps[] = [

                'approval_level' =>
                    $level,

                'role_id' =>
                    $masterStep->role_id,

                'role_name' =>
                    $transactionStep?->role_name
                    ?? $masterStep->role?->name
                    ?? 'Unknown',

                'status' =>
                    $status,

                'approved_by' =>
                    $transactionStep?->approved_by,

                'acted_at' =>
                    $transactionStep?->acted_at,

                'passed' =>
                    $passed,

            ];
        }

        return [

            'passed' =>
                $allPassed
                && $requiredLevels->isNotEmpty(),

            'steps' =>
                $steps,

        ];
    }

    /*
    |--------------------------------------------------------------------------
    | Items
    |--------------------------------------------------------------------------
    */

    protected function evaluateItems(
        AssignmentMaterialRequisition $assignment,
    ): array {

        $errors = [];

        if ($assignment->items->isEmpty()) {

            return [

                'passed' => false,

                'count' => 0,

                'errors' => [
                    'Assignment has no items.',
                ],

            ];
        }

        foreach (
            $assignment->items as $item
        ) {

            $itemCode =
                $item->item_code
                ?? ('#' . $item->id);

            if (! $item->supplier_id) {

                $errors[] =
                    "Supplier is required for item {$itemCode}.";
            }

            if (
                (float) $item->assigned_qty <= 0
            ) {

                $errors[] =
                    "Assigned Quantity must be greater than zero for {$itemCode}.";
            }

            if (
                (float) $item->unit_price <= 0
            ) {

                $errors[] =
                    "Unit Price must be greater than zero for {$itemCode}.";
            }
        }

        return [

            'passed' =>
                empty($errors),

            'count' =>
                $assignment->items->count(),

            'errors' =>
                $errors,

        ];
    }

    /*
    |--------------------------------------------------------------------------
    | Supplier
    |--------------------------------------------------------------------------
    */

    protected function evaluateSupplier(
        AssignmentMaterialRequisition $assignment,
    ): array {

        $supplierIds =
            $assignment
                ->items
                ->pluck('supplier_id')
                ->filter()
                ->unique()
                ->values();

        $errors = [];

        if ($supplierIds->isEmpty()) {

            $errors[] =
                'No supplier has been assigned to the Assignment items.';
        }

        if ($supplierIds->count() > 1) {

            $errors[] =
                'Purchase Order can only be generated when all items use the same supplier.';
        }

        return [

            'passed' =>
                $supplierIds->count() === 1,

            'supplier_id' =>
                $supplierIds->first(),

            'supplier_count' =>
                $supplierIds->count(),

            'errors' =>
                $errors,

        ];
    }
}