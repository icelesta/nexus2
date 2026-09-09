<?php

declare(strict_types=1);

namespace App\Services\Purchasing;

use App\Models\DirectMarket;
use App\Models\DirectMarketItem;
use App\Models\TransactionNumbering;
use App\Services\Numbering\NumberingService;
use Illuminate\Database\DatabaseManager;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use RuntimeException;

class DirectMarketService
{
    /**
     * Constructor.
     */
    public function __construct(
        protected DirectMarket $model,
        protected NumberingService $numberingService,
        protected DatabaseManager $db,
        protected \App\Services\Approval\ApprovalTransactionService $approvalTransactionService,
    ) {
    }

    /*
    |--------------------------------------------------------------------------
    | Create
    |--------------------------------------------------------------------------
    */

    /**
     * Create Direct Market.
     *
     * DM starts in Draft state.
     *
     * No Approval Transaction is created here.
     * No AMR is created here.
     * No PO is created here.
     */
    public function create(array $data): DirectMarket
    {
        $this->validateCreate($data);

        return $this->db->transaction(function () use ($data) {

            $userId = Auth::id();

            if ($userId === null) {
                throw new RuntimeException(
                    'Authenticated user is required to create a Direct Market.'
                );
            }

            /*
            |--------------------------------------------------------------------------
            | Generate DM Number
            |--------------------------------------------------------------------------
            */

            $documentNumber = $this->numberingService->generate(
                documentType: TransactionNumbering::DOC_DIRECT_MARKET,
                companyId: (int) $data['company_id'],
                businessUnitId: isset($data['business_unit_id'])
                    ? (int) $data['business_unit_id']
                    : null,
                branchId: isset($data['branch_id'])
                    ? (int) $data['branch_id']
                    : null,
                departmentId: isset($data['department_id'])
                    ? (int) $data['department_id']
                    : null,
            );

            /*
            |--------------------------------------------------------------------------
            | Create Header
            |--------------------------------------------------------------------------
            */

            $directMarket = $this->model->create([
                'uuid' => (string) Str::uuid(),

                'dm_no' => $documentNumber,

                'company_id' => $data['company_id'],

                'business_unit_id' =>
                    $data['business_unit_id'] ?? null,

                'branch_id' =>
                    $data['branch_id'] ?? null,

                'department_id' =>
                    $data['department_id'] ?? null,

                'cost_center_id' =>
                    $data['cost_center_id'] ?? null,

                'warehouse_id' =>
                    $data['warehouse_id'] ?? null,

                'currency_id' =>
                    $data['currency_id'],
                    

                'delivery_location' =>
                    $data['delivery_location'] ?? null,

                'reference_no' =>
                    $data['reference_no'] ?? null,

                'remarks' =>
                    $data['remarks'] ?? null,

                'request_date' =>
                    $data['request_date'] ?? today(),

                'required_date' =>
                    $data['required_date'] ?? null,

                'requester_id' =>
                    $data['requester_id'] ?? $userId,

                'status' =>
                    DirectMarket::STATUS_DRAFT,

                'submitted_by' =>
                    null,

                'created_by' =>
                    $userId,
            ]);

            /*
            |--------------------------------------------------------------------------
            | Create Items
            |--------------------------------------------------------------------------
            */

            foreach ($data['items'] ?? [] as $itemData) {

                $this->createItem(
                    $directMarket,
                    $itemData,
                    $userId,
                );
            }

            return $directMarket->refresh();
        });
    }

    /*
    |--------------------------------------------------------------------------
    | Submit
    |--------------------------------------------------------------------------
    */

    /**
     * Submit Direct Market for approval.
     *
     * Workflow:
     *
     * Draft
     *   ↓
     * Submitted
     *   ↓
     * Approval Transaction
     *   ↓
     * DM Approval
     *
     * DM does NOT create:
     * - AMR
     * - PO
     * - Generate PO
     */
    public function submit(
        int $id,
    ): DirectMarket {

        return $this->db->transaction(function () use ($id) {

            /*
            |--------------------------------------------------------------------------
            | Load DM
            |--------------------------------------------------------------------------
            */

            $directMarket = $this->model
                ->newQuery()
                ->lockForUpdate()
                ->findOrFail($id);

            /*
            |--------------------------------------------------------------------------
            | Validate Current State
            |--------------------------------------------------------------------------
            */

            if (
                $directMarket->status
                !== DirectMarket::STATUS_DRAFT
            ) {
                throw new RuntimeException(
                    "Direct Market [{$directMarket->dm_no}] is not in Draft status."
                );
            }

            /*
            |--------------------------------------------------------------------------
            | Validate Items
            |--------------------------------------------------------------------------
            */

            if (
                ! $directMarket->items()
                    ->exists()
            ) {
                throw new RuntimeException(
                    "Direct Market [{$directMarket->dm_no}] must have at least one item before submission."
                );
            }

            /*
            |--------------------------------------------------------------------------
            | Resolve DM Approval Master
            |--------------------------------------------------------------------------
            */

            $approvalMaster =
                $this->approvalTransactionService
                    ->getActiveMaster(
                        'DIRECT_MARKET'
                    );

            /*
            |--------------------------------------------------------------------------
            | Validate Approval Configuration
            |--------------------------------------------------------------------------
            |
            | Unlike Material Requisition, DM requires
            | the complete configured approval chain.
            |
            */

            $this->approvalTransactionService
                ->validateApproverAvailabilityForDocument(
                    $approvalMaster,
                    'DIRECT_MARKET'
                );

            /*
            |--------------------------------------------------------------------------
            | Submit DM
            |--------------------------------------------------------------------------
            */

            $userId = Auth::id();

            if ($userId === null) {
                throw new RuntimeException(
                    'Authenticated user is required to submit a Direct Market.'
                );
            }

            $directMarket->update([
                'status' =>
                    DirectMarket::STATUS_SUBMITTED,

                'submitted_by' =>
                    $userId,

                'updated_by' =>
                    $userId,
            ]);

            /*
            |--------------------------------------------------------------------------
            | Create Approval Transaction
            |--------------------------------------------------------------------------
            */

            $this->approvalTransactionService->create(
                'DIRECT_MARKET',
                $directMarket->getKey(),
                $directMarket->dm_no,
                Auth::id(),
            );

            /*
            |--------------------------------------------------------------------------
            | Return Fresh DM
            |--------------------------------------------------------------------------
            */

            return $directMarket->refresh();
        });
    }

    

    /*
    |--------------------------------------------------------------------------
    | Item
    |--------------------------------------------------------------------------
    */

    /**
     * Create Direct Market Item.
     */
    protected function createItem(
        DirectMarket $directMarket,
        array $data,
        ?int $userId,
    ): DirectMarketItem {

        return $directMarket->items()->create([

            'uuid' => (string) Str::uuid(),

            'item_id' =>
                $data['item_id'],

            'uom_id' =>
                $data['uom_id'],

            'qty' =>
                $data['qty'],

            'unit_price' =>
                $data['unit_price'] ?? null,            

            'required_date' =>
                $data['required_date']
                ?? $directMarket->required_date,

            'delivery_location' =>
                $data['delivery_location']
                ?? $directMarket->delivery_location,

            'remarks' =>
                $data['remarks'] ?? null,

            'status' =>
                DirectMarketItem::STATUS_DRAFT,

            'created_by' =>
                $userId,
        ]);
    }

    /*
    |--------------------------------------------------------------------------
    | Validation
    |--------------------------------------------------------------------------
    */

    /**
     * Validate Direct Market creation.
     *
     * @throws RuntimeException
     */
    protected function validateCreate(array $data): void
    {
        if (
            empty($data['company_id'])
        ) {
            throw new RuntimeException(
                'Company is required for Direct Market.'
            );
        }

        if (
            empty($data['requester_id'])
            && ! Auth::id()
        ) {
            throw new RuntimeException(
                'Requester is required for Direct Market.'
            );
        }

        if (
            ! isset($data['items'])
            || ! is_array($data['items'])
            || count($data['items']) === 0
        ) {
            throw new RuntimeException(
                'At least one Direct Market item is required.'
            );
        }

        foreach ($data['items'] as $index => $item) {

            if (
                empty($item['item_id'])
            ) {
                throw new RuntimeException(
                    "Direct Market item [{$index}] requires an item."
                );
            }

            if (
                empty($item['uom_id'])
            ) {
                throw new RuntimeException(
                    "Direct Market item [{$index}] requires a UOM."
                );
            }

            if (
                ! isset($item['qty'])
                || (float) $item['qty'] <= 0
            ) {
                throw new RuntimeException(
                    "Direct Market item [{$index}] requires quantity greater than zero."
                );
            }
        }
    }
}