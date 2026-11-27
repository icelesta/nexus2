<?php

declare(strict_types=1);

namespace App\Services\Purchasing;

use App\Models\AssignmentDirectMarket;
use App\Models\GoodsReceipt;
use App\Models\GoodsReceiptItem;
use App\Models\PurchaseOrder;
use App\Models\PurchaseOrderItem;
use App\Models\TransactionNumbering;
use App\Services\Numbering\NumberingService;
use Illuminate\Database\DatabaseManager;
use Illuminate\Support\Collection;
use RuntimeException;
use App\Services\Purchasing\GoodsReceiptAuditService;

class GoodsReceiptService
{
    public function __construct(
        protected GoodsReceipt $model,
        protected NumberingService $numberingService,
        protected DatabaseManager $db,
        protected GoodsReceiptAuditService $auditService,
    ) {
    }

    /**
     * Create Header-only Draft RR from an Approved Purchase Order.
     * No item rows and no allocation reservation are created here.
     */
    public function createHeaderFromPurchaseOrder(
        int $purchaseOrderId,
        ?int $userId = null,
        ?int $supplierId = null,
        ?string $remarks = null,
        ?string $documentNumber = null,
    ): GoodsReceipt {
        return $this->db->transaction(function () use (
            $purchaseOrderId,
            $userId,
            $supplierId,
            $remarks,
            $documentNumber
        ): GoodsReceipt {
            $purchaseOrder = PurchaseOrder::query()
                ->with('items')
                ->lockForUpdate()
                ->findOrFail($purchaseOrderId);

            $this->validatePurchaseOrder($purchaseOrder);

            if (
                $supplierId === null ||
                (int) $purchaseOrder->supplier_id !== $supplierId
            ) {
                throw new RuntimeException(
                    'Selected Supplier does not match the Purchase Order.'
                );
            }

            $outstandingItems = $purchaseOrder->items->filter(
                fn (PurchaseOrderItem $item): bool =>
                    $this->getPurchaseOrderItemOutstanding($item) > 0
            );

            if ($outstandingItems->isEmpty()) {
                throw new RuntimeException(
                    'The selected Purchase Order has no outstanding quantity.'
                );
            }

            $actorId = $userId ?? auth()->id();

            $grnNo = $this->numberingService->generate(
                TransactionNumbering::DOC_GOODS_RECEIPT,
                $purchaseOrder->company_id ?? null,
                $purchaseOrder->business_unit_id ?? null,
                $purchaseOrder->branch_id ?? null,
                $purchaseOrder->department_id ?? null,
            );

            $goodsReceipt = $this->model->newQuery()->create([
                'grn_no' => $grnNo,
                'document_number' => $documentNumber,
                'purchase_order_id' => $purchaseOrder->getKey(),
                'assignment_direct_market_id' => null,
                'supplier_id' => $purchaseOrder->supplier_id,
                'warehouse_id' => $purchaseOrder->warehouse_id,
                'receipt_date' => now()->toDateString(),
                'status' => GoodsReceipt::STATUS_DRAFT,
                'items_status' => 'DRAFT',
                'rr_status' => 'DRAFT',
                'remarks' => $remarks,
                'created_by' => $actorId,
                'updated_by' => $actorId,
            ]);

            $this->auditService->recordCreated(
                $goodsReceipt,
                $actorId,
            );

            return $goodsReceipt->fresh([
                'purchaseOrder',
                'supplier',
                'items',
            ]);
        });
    }

    /**
     * Create Header-only Draft RR from an Approved ADM.
     * Supplier must have at least one outstanding ADM item.
     */
    public function createHeaderFromAssignmentDirectMarket(
        int $assignmentId,
        ?int $userId = null,
        ?int $supplierId = null,
        ?string $remarks = null,
        ?string $documentNumber = null,
    ): GoodsReceipt {
        return $this->db->transaction(function () use (
            $assignmentId,
            $userId,
            $supplierId,
            $remarks,
            $documentNumber
        ): GoodsReceipt {
            $assignment = AssignmentDirectMarket::query()
                ->with('items')
                ->lockForUpdate()
                ->findOrFail($assignmentId);

            $this->validateAssignmentDirectMarket($assignment);

            if ($assignment->items->isEmpty()) {
                throw new RuntimeException(
                    'The selected Assignment Direct Market has no items.'
                );
            }

            if ($supplierId === null) {
                throw new RuntimeException('Supplier is required.');
            }

            $outstandingItems = $assignment->items->filter(
                fn ($item): bool =>
                    (int) $item->supplier_id === $supplierId
                    && $this->getAssignmentDirectMarketItemOutstanding($item) > 0
            );

            if ($outstandingItems->isEmpty()) {
                throw new RuntimeException(
                    'The selected Assignment Direct Market has no outstanding items for this Supplier.'
                );
            }

            $actorId = $userId ?? auth()->id();

            $grnNo = $this->numberingService->generate(
                TransactionNumbering::DOC_GOODS_RECEIPT,
                $assignment->company_id ?? null,
                $assignment->business_unit_id ?? null,
                $assignment->branch_id ?? null,
                $assignment->department_id ?? null,
            );

            $goodsReceipt = $this->model->newQuery()->create([
                'grn_no' => $grnNo,
                'document_number' => $documentNumber,
                'purchase_order_id' => null,
                'assignment_direct_market_id' => $assignment->getKey(),
                'supplier_id' => $supplierId,
                'warehouse_id' => $assignment->warehouse_id,
                'receipt_date' => now()->toDateString(),
                'status' => GoodsReceipt::STATUS_DRAFT,
                'items_status' => 'DRAFT',
                'rr_status' => 'DRAFT',
                'remarks' => $remarks,
                'created_by' => $actorId,
                'updated_by' => $actorId,
            ]);

            $this->auditService->recordCreated(
                $goodsReceipt,
                $actorId,
            );

            return $goodsReceipt->fresh([
                'assignmentDirectMarket',
                'supplier',
                'items',
            ]);
        });
    }


    /**
     * Create one Draft RR from an Approved Purchase Order.
     *
     * RR-04A: creation/snapshot from outstanding PO items.
     * Multiple RR per PO are allowed.
     *
     * No approval, source completion, or inventory posting.
     */
    public function createFromPurchaseOrder(
        int $purchaseOrderId,
        ?int $userId = null,
        ?int $supplierId = null,
        ?string $remarks = null,
        ?string $documentNumber = null,
    ): GoodsReceipt {
        return $this->db->transaction(
            function () use (
                $purchaseOrderId,
                $userId,
                $supplierId,
                $remarks,
                $documentNumber
            ): GoodsReceipt {
                $purchaseOrder = PurchaseOrder::query()
                    ->with(['items'])
                    ->lockForUpdate()
                    ->findOrFail($purchaseOrderId);

                    $this->validatePurchaseOrder($purchaseOrder);

                    if (
                        $supplierId !== null
                        && (int) $purchaseOrder->supplier_id !== $supplierId
                    ) {
                        throw new RuntimeException(
                            "Selected Supplier does not match Purchase Order [{$purchaseOrder->document_no}]."
                        );
                    }


                    if ($purchaseOrder->items->isEmpty()) {
                        throw new RuntimeException(
                            "Purchase Order [{$purchaseOrder->document_no}] has no items."
                        );
                    }

                    $outstandingItems = $purchaseOrder->items
                        ->filter(
                            fn (PurchaseOrderItem $item): bool =>
                                $this->getPurchaseOrderItemOutstanding($item) > 0
                        )
                        ->values();

                    if ($outstandingItems->isEmpty()) {
                        throw new RuntimeException(
                            "Purchase Order [{$purchaseOrder->document_no}] has no outstanding quantity for Receiving Record."
                        );
                    }

                $actorId = $userId ?? auth()->id();

                $grnNo = $this->numberingService->generate(
                    TransactionNumbering::DOC_GOODS_RECEIPT,
                    $purchaseOrder->company_id ?? null,
                    $purchaseOrder->business_unit_id ?? null,
                    $purchaseOrder->branch_id ?? null,
                    $purchaseOrder->department_id ?? null,
                );

                $goodsReceipt = $this->model->newQuery()->create([
                    'grn_no' => $grnNo,
                    'document_number' => $documentNumber,
                    'purchase_order_id' => $purchaseOrder->getKey(),
                    'assignment_direct_market_id' => null,
                    'supplier_id' => $purchaseOrder->supplier_id ?? null,
                    'warehouse_id' => $purchaseOrder->warehouse_id,
                    'receipt_date' => now()->toDateString(),
                    'status' => GoodsReceipt::STATUS_DRAFT,
                    'remarks' => $remarks,
                    'created_by' => $actorId,
                    'updated_by' => $actorId,
                ]);

                foreach ($outstandingItems as $purchaseOrderItem) {
                    $allocatedQty = $this->getPurchaseOrderItemOutstanding(
                        $purchaseOrderItem
                    );

                    GoodsReceiptItem::query()->create([
                        'goods_receipt_id' => $goodsReceipt->getKey(),
                        'purchase_order_item_id' => $purchaseOrderItem->getKey(),
                        'assignment_direct_market_item_id' => null,
                        'item_id' => $purchaseOrderItem->item_id,
                        'received_qty' => 0,
                        'allocated_qty' => $allocatedQty,
                        'uom_id' => $purchaseOrderItem->uom_id,
                        'warehouse_id' => $purchaseOrderItem->warehouse_id ?? null,
                        'accepted_qty' => 0,
                        'rejected_qty' => 0,
                        'remarks' => $purchaseOrderItem->remarks ?? null,
                        'created_by' => $actorId,
                        'updated_by' => $actorId,
                    ]);
                }

                return $goodsReceipt->fresh([
                    'purchaseOrder',
                    'supplier',
                    'items',
                ]);
            }
        );
    }

    /**
     * Create Draft RR(s) from an Approved Assignment Direct Market.
     *
     * One supplier group produces one RR.
     * Multiple RR per ADM are allowed.
     *
     * Only items with outstanding quantity are included.
     */
    public function createFromAssignmentDirectMarket(
        int $assignmentId,
        ?int $userId = null,
        ?int $supplierId = null,
        ?string $remarks = null,
        ?string $documentNumber = null,
    ): Collection {
        return $this->db->transaction(
            function () use (
                $assignmentId,
                $userId,
                $supplierId,
                $remarks,
                $documentNumber
            ): Collection {
                $assignment = AssignmentDirectMarket::query()
                    ->with(['items'])
                    ->lockForUpdate()
                    ->findOrFail($assignmentId);

                $this->validateAssignmentDirectMarket($assignment);

                if ($assignment->items->isEmpty()) {
                    throw new RuntimeException(
                        "Assignment Direct Market [{$assignment->document_no}] has no items."
                    );
                }

                $outstandingItems = $assignment->items
                    ->filter(
                        fn ($item): bool =>
                            $this->getAssignmentDirectMarketItemOutstanding($item) > 0
                    )
                    ->when(
                        $supplierId !== null,
                        fn (Collection $items): Collection =>
                            $items
                                ->filter(
                                    fn ($item): bool =>
                                        (int) $item->supplier_id === $supplierId
                                )
                                ->values()
                    )
                    ->values();

                if ($outstandingItems->isEmpty()) {
                    if ($supplierId !== null) {
                        throw new RuntimeException(
                            "Direct Market [{$assignment->document_no}] has no outstanding items for the selected Supplier."
                        );
                    }

                    throw new RuntimeException(
                        "Assignment Direct Market [{$assignment->document_no}] has no outstanding quantity for Receiving Record."
                    );
                }

                $itemGroups = $outstandingItems->groupBy('supplier_id');

                if ($itemGroups->has(null)) {
                    throw new RuntimeException(
                        "Assignment Direct Market [{$assignment->document_no}] contains outstanding items without a supplier."
                    );
                }

                $actorId = $userId ?? auth()->id();
                $receipts = collect();

                foreach ($itemGroups as $supplierId => $items) {
                    $grnNo = $this->numberingService->generate(
                        TransactionNumbering::DOC_GOODS_RECEIPT,
                        $assignment->company_id ?? null,
                        $assignment->business_unit_id ?? null,
                        $assignment->branch_id ?? null,
                        $assignment->department_id ?? null,
                    );

                    $goodsReceipt = $this->model->newQuery()->create([
                        'grn_no' => $grnNo,
                        'document_number' => $documentNumber,
                        'purchase_order_id' => null,
                        'assignment_direct_market_id' => $assignment->getKey(),
                        'supplier_id' => (int) $supplierId,
                        'warehouse_id' => $assignment->warehouse_id,
                        'receipt_date' => now()->toDateString(),
                        'status' => GoodsReceipt::STATUS_DRAFT,
                        'remarks' => $remarks,
                        'created_by' => $actorId,
                        'updated_by' => $actorId,
                    ]);

                    foreach ($items as $assignmentItem) {
                        $allocatedQty = $this->getAssignmentDirectMarketItemOutstanding(
                            $assignmentItem
                        );

                        GoodsReceiptItem::query()->create([
                            'goods_receipt_id' => $goodsReceipt->getKey(),
                            'purchase_order_item_id' => null,
                            'assignment_direct_market_item_id' => $assignmentItem->getKey(),
                            'item_id' => $assignmentItem->item_id,
                            'received_qty' => 0,
                            'allocated_qty' => $allocatedQty,
                            'uom_id' => $assignmentItem->uom_id,
                            'warehouse_id' => $assignment->warehouse_id ?? null,
                            'accepted_qty' => 0,
                            'rejected_qty' => 0,
                            'remarks' => $assignmentItem->remarks ?? null,
                            'created_by' => $actorId,
                            'updated_by' => $actorId,
                        ]);
                    }

                    $receipts->push(
                        $goodsReceipt->fresh([
                            'assignmentDirectMarket',
                            'supplier',
                            'items',
                        ])
                    );
                }

                return $receipts;
            }
        );
    }

    /**
     * Calculate outstanding quantity for one PO item.
     *
     * Outstanding =
     * ordered_qty - all received_qty from existing RR items.
     */
    protected function getPurchaseOrderItemOutstanding(
        PurchaseOrderItem $item,
    ): float {
        $receivedQty = (float) GoodsReceiptItem::query()
            ->where('purchase_order_item_id', $item->getKey())
            ->sum('received_qty');

        return max(
            0,
            (float) $item->ordered_qty - $receivedQty
        );
    }

    /**
     * Calculate outstanding quantity for one ADM item.
     *
     * Outstanding =
     * assigned_qty - all received_qty from existing RR items.
     */
    protected function getAssignmentDirectMarketItemOutstanding(
        $item,
    ): float {
        $receivedQty = (float) GoodsReceiptItem::query()
            ->where(
                'assignment_direct_market_item_id',
                $item->getKey()
            )
            ->sum('received_qty');

        return max(
            0,
            (float) $item->assigned_qty - $receivedQty
        );
    }

    protected function validatePurchaseOrder(
        PurchaseOrder $purchaseOrder,
    ): void {
        if ($purchaseOrder->approval_status !== PurchaseOrder::APPROVAL_APPROVED) {
            throw new RuntimeException(
                "Purchase Order [{$purchaseOrder->document_no}] must be approved before creating Goods Receipt. Current approval status: [{$purchaseOrder->approval_status}]."
            );
        }
    }

    protected function validateAssignmentDirectMarket(
        AssignmentDirectMarket $assignment,
    ): void {
        if ($assignment->status !== AssignmentDirectMarket::STATUS_APPROVED) {
            throw new RuntimeException(
                "Assignment Direct Market [{$assignment->document_no}] must be Approved before creating Goods Receipt. Current status: [{$assignment->status}]."
            );
        }
    }
}