<?php

declare(strict_types=1);

namespace App\Services\Purchasing;

use App\Models\AssignmentDirectMarket;
use App\Models\GoodsReceipt;
use App\Models\GoodsReceiptItem;
use App\Models\PurchaseOrder;
use App\Models\TransactionNumbering;
use App\Services\Numbering\NumberingService;
use Illuminate\Database\DatabaseManager;
use Illuminate\Support\Collection;
use RuntimeException;

class GoodsReceiptService
{
    public function __construct(
        protected GoodsReceipt $model,
        protected NumberingService $numberingService,
        protected DatabaseManager $db,
    ) {
    }

    /**
     * Create one Draft RR from an Approved Purchase Order.
     *
     * RR-01A only: creation/snapshot.
     * No approval, receiving, source completion, or inventory posting.
     */
    public function createFromPurchaseOrder(
        int $purchaseOrderId,
        ?int $userId = null,
    ): GoodsReceipt {
        return $this->db->transaction(
            function () use ($purchaseOrderId, $userId): GoodsReceipt {
                $purchaseOrder = PurchaseOrder::query()
                    ->with(['items'])
                    ->lockForUpdate()
                    ->findOrFail($purchaseOrderId);

                $this->validatePurchaseOrder($purchaseOrder);

                if (
                    GoodsReceipt::query()
                        ->where('purchase_order_id', $purchaseOrder->getKey())
                        ->exists()
                ) {
                    throw new RuntimeException(
                        "Goods Receipt for Purchase Order [{$purchaseOrder->document_no}] already exists."
                    );
                }

                if ($purchaseOrder->items->isEmpty()) {
                    throw new RuntimeException(
                        "Purchase Order [{$purchaseOrder->document_no}] has no items."
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
                    'purchase_order_id' => $purchaseOrder->getKey(),
                    'assignment_direct_market_id' => null,
                    'supplier_id' => $purchaseOrder->supplier_id ?? null,
                    'receipt_date' => now()->toDateString(),
                    'status' => GoodsReceipt::STATUS_DRAFT,
                    'remarks' => null,
                    'created_by' => $actorId,
                    'updated_by' => $actorId,
                ]);

                foreach ($purchaseOrder->items as $purchaseOrderItem) {
                    GoodsReceiptItem::query()->create([
                        'goods_receipt_id' => $goodsReceipt->getKey(),
                        'purchase_order_item_id' => $purchaseOrderItem->getKey(),
                        'assignment_direct_market_item_id' => null,
                        'item_id' => $purchaseOrderItem->item_id,
                        'received_qty' => 0,
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
     */
    public function createFromAssignmentDirectMarket(
        int $assignmentId,
        ?int $userId = null,
    ): Collection {
        return $this->db->transaction(
            function () use ($assignmentId, $userId): Collection {
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

                if (
                    GoodsReceipt::query()
                        ->where(
                            'assignment_direct_market_id',
                            $assignment->getKey()
                        )
                        ->exists()
                ) {
                    throw new RuntimeException(
                        "Goods Receipt for Assignment Direct Market [{$assignment->document_no}] already exists."
                    );
                }

                $itemGroups = $assignment->items->groupBy('supplier_id');

                if ($itemGroups->has(null)) {
                    throw new RuntimeException(
                        "Assignment Direct Market [{$assignment->document_no}] contains items without a supplier."
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
                        'purchase_order_id' => null,
                        'assignment_direct_market_id' => $assignment->getKey(),
                        'supplier_id' => (int) $supplierId,
                        'receipt_date' => now()->toDateString(),
                        'status' => GoodsReceipt::STATUS_DRAFT,
                        'remarks' => null,
                        'created_by' => $actorId,
                        'updated_by' => $actorId,
                    ]);

                    foreach ($items as $assignmentItem) {
                        GoodsReceiptItem::query()->create([
                            'goods_receipt_id' => $goodsReceipt->getKey(),
                            'purchase_order_item_id' => null,
                            'assignment_direct_market_item_id' => $assignmentItem->getKey(),
                            'item_id' => $assignmentItem->item_id,
                            'received_qty' => 0,
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
