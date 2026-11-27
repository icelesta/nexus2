<?php

declare(strict_types=1);

namespace App\Livewire\Purchasing;

use App\Services\Purchasing\GoodsReceiptDraftService;
use App\Models\AssignmentDirectMarket;
use App\Models\AssignmentDirectMarketItem;
use App\Models\DirectMarket;
use App\Models\GoodsReceipt;
use App\Models\GoodsReceiptItem;
use App\Models\PurchaseOrder;
use App\Models\PurchaseOrderItem;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Collection;
use Livewire\Component;
use Livewire\Attributes\On;
use App\Services\Purchasing\GoodsReceiptAuditService;
use Illuminate\Support\Facades\DB;

class GoodsReceiptItemsGrid extends Component
{
    public GoodsReceipt $goodsReceipt;

    public Collection $sourceItems;

    public Collection $receivingItems;

    public array $selectedItems = [];

    public array $receiveQty = [];

    public bool $viewOnly = false;

    public bool $itemsReadOnly = false;

    public function mount(
        GoodsReceipt $goodsReceipt,
        bool $viewOnly = false
    ): void {
        $this->goodsReceipt = $goodsReceipt;

        $this->viewOnly = $viewOnly;

        $this->itemsReadOnly = strtoupper(
            (string) $goodsReceipt->rr_status
        ) !== 'DRAFT';

        $this->loadItems();
    }

    protected function isDraftForModification(): bool
    {
        return GoodsReceipt::query()
            ->whereKey($this->goodsReceipt->getKey())
            ->where('rr_status', 'DRAFT')
            ->exists();
    }

    /*
    |--------------------------------------------------------------------------
    | LOAD ITEMS
    |--------------------------------------------------------------------------
    */

    public function loadItems(): void
    {
        $this->goodsReceipt->load([
            'purchaseOrder',
            'assignmentDirectMarket',
            'items.purchaseOrderItem',
            'items.assignmentDirectMarketItem',
            'items.item',
            'items.uom',
            'items.warehouse',
        ]);

        $this->receivingItems = $this->goodsReceipt
            ->items
            ->sortBy('id')
            ->values();

        $this->selectedItems = [];
        $this->receiveQty = [];

        foreach ($this->receivingItems as $item) {
            $sourceKey = $this->sourceKey($item);

            $this->selectedItems[$sourceKey] = true;
            $qty = (float) $item->draft_receive_qty;

            $this->receiveQty[$item->getKey()] = $qty > 0
                ? rtrim(rtrim(number_format($qty, 4, '.', ''), '0'), '.')
                : '0';
        }

        $this->loadSourceItems();
    }

    /*
    |--------------------------------------------------------------------------
    | SOURCE ITEMS
    |--------------------------------------------------------------------------
    */

    protected function loadSourceItems(): void
    {
        if ($this->goodsReceipt->purchase_order_id) {

            $this->sourceItems =
                PurchaseOrderItem::query()
                    ->where(
                        'purchase_order_id',
                        $this->goodsReceipt->purchase_order_id
                    )
                    ->orderBy('id')
                    ->get();

            return;
        }

        if (
            $this->goodsReceipt
                ->assignment_direct_market_id
        ) {

            $this->sourceItems =
                AssignmentDirectMarketItem::query()
                    ->where(
                        'assignment_direct_market_id',
                        $this->goodsReceipt
                            ->assignment_direct_market_id
                    )
                    ->where(
                        'supplier_id',
                        $this->goodsReceipt->supplier_id
                    )
                    ->orderBy('id')
                    ->get();

            return;
        }

        $this->sourceItems = collect();
    }

    /*
    |--------------------------------------------------------------------------
    | SELECT ALL
    |--------------------------------------------------------------------------
    */

    public function toggleSelectAll(): void
    {

        if ($this->viewOnly || ! $this->isDraftForModification()) {
            return;
        }

        if ($this->viewOnly || $this->sourceItems->isEmpty()) {
            return;
        }

        if ($this->allSourceItemsSelected()) {
            foreach ($this->sourceItems as $sourceItem) {
                $key = $this->sourceKeyFromSourceItem($sourceItem);

                // Item yang sudah tersimpan tidak boleh dilepas.
                if ($this->hasSavedSourceItem($sourceItem)) {
                    continue;
                }

                unset($this->selectedItems[$key]);

                $temporaryKey = 'new-' . $key;

                unset(
                    $this->receiveQty[$temporaryKey]
                );
            }

            return;
        }

        foreach ($this->sourceItems as $sourceItem) {
            if ($this->getRemainingQty($sourceItem) <= 0) {
                continue;
            }

            $key = $this->sourceKeyFromSourceItem($sourceItem);

            $this->selectedItems[$key] = true;

            $temporaryKey = 'new-' . $key;

            if (! $this->hasSavedSourceItem($sourceItem)) {
                $this->receiveQty[$temporaryKey] = '0';
            }
        }
    }

    public function selectItem(int $sourceItemId): void
    {

        if ($this->viewOnly || ! $this->isDraftForModification()) {
            return;
        }

        if ($this->viewOnly) {
            return;
        }

        $sourceItem = $this->findSourceItem($sourceItemId);

        if (! $sourceItem || $this->getRemainingQty($sourceItem) <= 0) {
            return;
        }

        $key = $this->sourceKeyFromSourceItem($sourceItem);

        // Item yang sudah tersimpan selalu tetap terpilih.
        if ($this->hasSavedSourceItem($sourceItem)) {
            $this->selectedItems[$key] = true;
            return;
        }

        if (isset($this->selectedItems[$key])) {
            unset($this->selectedItems[$key]);

            unset(
                $this->receiveQty['new-' . $key]
            );
        } else {
            $this->selectedItems[$key] = true;

            $temporaryKey = 'new-' . $key;

            $this->receiveQty[$temporaryKey] = '0';
        }
    }

    protected function hasSavedSourceItem(
        PurchaseOrderItem|AssignmentDirectMarketItem $sourceItem
    ): bool {
        return $this->receivingItems->contains(
            fn (GoodsReceiptItem $item): bool =>
                (int) (
                    $item->purchase_order_item_id
                    ?? $item->assignment_direct_market_item_id
                ) === (int) $sourceItem->getKey()
        );
    }

    public function getDisplayReceivingItems(): Collection
    {
        $items = $this->receivingItems->keyBy(
            fn (GoodsReceiptItem $item) => $this->sourceKey($item)
        );

        foreach ($this->selectedItems as $sourceKey => $selected) {
            if (! $selected || $items->has($sourceKey)) {
                continue;
            }

            $sourceItem = $this->sourceItems->first(
                fn ($item) =>
                    $this->sourceKeyFromSourceItem($item) === $sourceKey
            );

            if (! $sourceItem) {
                continue;
            }

            $temporaryItem = new GoodsReceiptItem();

            // ID sementara hanya untuk identitas baris UI, bukan record DB.
            $temporaryItem->setAttribute('id', 'new-' . $sourceKey);
            $temporaryKey = 'new-' . $sourceKey;

            $temporaryItem->setAttribute(
                'draft_receive_qty',
                $this->receiveQty[$temporaryKey] ?? 0
            );
            $temporaryItem->setAttribute('received_qty', 0);
            $temporaryItem->setAttribute('warehouse_id',
                $sourceItem instanceof PurchaseOrderItem
                    ? $sourceItem->warehouse_id
                    : $this->goodsReceipt->assignmentDirectMarket?->warehouse_id
            );

            if ($sourceItem instanceof PurchaseOrderItem) {
                $temporaryItem->setAttribute(
                    'purchase_order_item_id',
                    $sourceItem->getKey()
                );

                $temporaryItem->setRelation('purchaseOrderItem', $sourceItem);
            } else {
                $temporaryItem->setAttribute(
                    'assignment_direct_market_item_id',
                    $sourceItem->getKey()
                );

                $temporaryItem->setRelation(
                    'assignmentDirectMarketItem',
                    $sourceItem
                );
            }

            $items->put($sourceKey, $temporaryItem);
        }

        return $items->values();
    }


    /*
    |--------------------------------------------------------------------------
    | REMOVE ITEM
    |--------------------------------------------------------------------------
    */

    public function removeItem(int $goodsReceiptItemId): void
    {
        $this->dispatch(
            'goods-receipt-item-error',
            message: 'Saved items cannot be removed or replaced from a Draft Receiving Record.'
        );
    }

    /**
     * ============================================================
     * QC AUTHORIZATION
     * ============================================================
     *
     * QC access follows the Receiving Record permissions
     * configured through the Role module.
     *
     * Create OR Update permission grants QC access.
     */
    public function canManageQc(): bool
    {
        $user = auth()->user();

        return $user !== null
            && (
                $user->can('Create:GoodsReceipt')
                || $user->can('Update:GoodsReceipt')
            );
    }

    /**
     * ============================================================
     * TOGGLE RECEIVING ITEM QC
     * ============================================================
     */
    public function toggleQc(int $goodsReceiptItemId): void
    {

        abort_unless(
            $this->isDraftForModification(),
            403,
            'Receiving Items can only be modified while the RR is in DRAFT status.'
        );

        $user = auth()->user();

        // Always validate authorization on the server.
        abort_unless(
            $user !== null
                && (
                    $user->can('Create:GoodsReceipt')
                    || $user->can('Update:GoodsReceipt')
                ),
            403,
            'You are not authorized to update Receiving Item QC.'
        );

        DB::transaction(function () use ($goodsReceiptItemId, $user): void {

            // Lock the RR header first.
            $goodsReceipt = GoodsReceipt::query()
                ->lockForUpdate()
                ->findOrFail($this->goodsReceipt->getKey());

            // Only allow QC changes on an item belonging to this RR.
            $item = GoodsReceiptItem::query()
                ->where('goods_receipt_id', $goodsReceipt->getKey())
                ->lockForUpdate()
                ->findOrFail($goodsReceiptItemId);

            $oldValue = (bool) $item->qc_passed;
            $newValue = ! $oldValue;

            $item->forceFill([
                'qc_passed' => $newValue,
                'qc_checked_by' => $user->getAuthIdentifier(),
                'qc_checked_at' => now(),
            ])->save();

            // Record every actual QC status change.
            app(GoodsReceiptAuditService::class)->recordQcUpdate(
                goodsReceipt: $goodsReceipt,
                goodsReceiptItem: $item,
                oldValue: $oldValue,
                newValue: $newValue,
                userId: (int) $user->getAuthIdentifier(),
            );
        });

        foreach ($this->receivingItems as $receivingItem) {
            if ((int) $receivingItem->getKey() === $goodsReceiptItemId) {
                $receivingItem->refresh();
                break;
            }
        }

        $this->dispatch('goods-receipt-qc-updated');
    }

    /*
    |--------------------------------------------------------------------------
    | SAVE RECEIVE QUANTITY
    |--------------------------------------------------------------------------
    */

    public function saveReceivingItems(): void
    {

        if ($this->viewOnly || ! $this->isDraftForModification()) {
            return;
        }

        if ($this->viewOnly) {
            return;
        }

        $items = [];

        foreach ($this->selectedItems as $sourceKey => $selected) {
            if (! $selected) {
                continue;
            }

            $sourceItem = $this->sourceItems->first(
                fn ($item) =>
                    $this->sourceKeyFromSourceItem($item) === $sourceKey
            );

            if (! $sourceItem) {
                continue;
            }

            $existing = $this->receivingItems->first(
                fn (GoodsReceiptItem $item) =>
                    $this->sourceKey($item) === $sourceKey
            );

            $rowKey = $existing
                ? $existing->getKey()
                : 'new-' . $sourceKey;

            $receive = (float) (
                $this->receiveQty[$rowKey]
                ?? $existing?->draft_receive_qty
                ?? 0
            );

            $items[] = [
                'source_item_id' => (int) $sourceItem->getKey(),
                'draft_receive_qty' => $receive,
            ];
        }

        try {
            app(GoodsReceiptDraftService::class)->saveDraft(
                (int) $this->goodsReceipt->getKey(),
                $items,
                auth()->id()
            );

            $this->goodsReceipt->refresh();
            $this->loadItems();

            $this->dispatch('goods-receipt-items-saved');

        } catch (\Illuminate\Validation\ValidationException $e) {
            $message = collect($e->errors())->flatten()->first();

            $this->dispatch(
                'goods-receipt-item-error',
                message: $message ?? 'Unable to save Draft Receiving Items.'
            );
        } catch (\Throwable $e) {
            report($e);

            $this->dispatch(
                'goods-receipt-item-error',
                message: 'Unable to save Draft Receiving Items. Please check the data and try again.'
            );
        }
    }

    /**
     * ============================================================
     * UNIT PRICE
     * ============================================================
     *
     * Unit Price is inherited from the source document.
     *
     * PO:
     * PurchaseOrderItem->unit_price
     *
     * ADM:
     * AssignmentDirectMarketItem->unit_price
     *
     * User cannot edit this value in Receiving Record.
     */
    public function getUnitPrice($sourceItem): float
    {
        return round(
            (float) (
                $sourceItem->unit_price
                ?? 0
            ),
            4
        );
    }


    /**
     * ============================================================
     * AMOUNT
     * ============================================================
     *
     * Amount is based on the actual Already Received Qty
     * of this Goods Receipt item.
     *
     * Amount =
     * Already Received × Unit Price
     */
    public function getAmount($sourceItem): float
    {
        $unitPrice = $this->getUnitPrice($sourceItem);

        $sourceKey = $this->sourceKeyFromSourceItem($sourceItem);

        $displayItem = $this->getDisplayReceivingItems()
            ->first(
                fn (GoodsReceiptItem $item): bool =>
                    $this->sourceKey($item) === $sourceKey
            );

        if (! $displayItem) {
            return 0;
        }

        $rrStatus = strtoupper(
            (string) $this->goodsReceipt->rr_status
        );

        if (in_array($rrStatus, ['RECEIVED', 'CLOSE'], true)) {
            $receiveQty = (float) $displayItem->received_qty;
        } else {
            $receiveQty = (float) (
                $this->receiveQty[$displayItem->getKey()] ?? 0
            );
        }

        return round($receiveQty * $unitPrice, 2);
    }

    public function getDisplayReceiveQty(
        GoodsReceiptItem $item
    ): float {
        $rrStatus = strtoupper(
            (string) $this->goodsReceipt->rr_status
        );

        if (in_array($rrStatus, ['RECEIVED', 'CLOSE'], true)) {
            return (float) $item->received_qty;
        }

        return (float) (
            $this->receiveQty[$item->getKey()]
            ?? $item->draft_receive_qty
            ?? 0
        );
    }

    /*
    |--------------------------------------------------------------------------
    | VALIDATE RECEIVE QTY ON INPUT
    |--------------------------------------------------------------------------
    */

    public function updatedReceiveQty($value, $key): void
    {

        if (
            $this->viewOnly
            || ! $this->isDraftForModification()
            || ! is_numeric($value)
        ) {
            return;
        }

        if ($this->viewOnly || ! is_numeric($value)) {
            return;
        }

        $rowKey = (string) $key;

        $displayItem = $this->getDisplayReceivingItems()
            ->first(
                fn (GoodsReceiptItem $item): bool =>
                    (string) $item->getKey() === $rowKey
            );

        if (! $displayItem) {
            return;
        }

        $sourceItem = $displayItem->purchaseOrderItem
            ?? $displayItem->assignmentDirectMarketItem;

        if (! $sourceItem) {
            return;
        }

        $maximumQty = $this->getRemainingQty($sourceItem);
        $enteredQty = (float) $value;

        if ($enteredQty > $maximumQty + 0.00005) {
            // Restore input to the maximum permitted quantity.
            $this->receiveQty[$rowKey] = $maximumQty > 0
                ? $maximumQty
                : '';

            $this->dispatch(
                'goods-receipt-item-error',
                message: sprintf(
                    'Quantity Exceeded! Maximum Receive Qty for item %s is %s %s.',
                    $sourceItem->item_code ?? '',
                    rtrim(rtrim(number_format($maximumQty, 4, '.', ''), '0'), '.'),
                    $this->getSourceUom($sourceItem)
                )
            );
        }
    }

    /**
     * ============================================================
     * ALLOCATION JOURNAL
     * ============================================================
     *
     * Allocation Journal is inherited from the originating job.
     *
     * PO flow:
     *
     * PO Item
     *   ↓
     * AMR Item
     *   ↓
     * MR Item
     *   ↓
     * Allocation Journal / COA
     *
     * ADM flow:
     *
     * ADM
     *   ↓
     * Direct Market
     *   ↓
     * Allocation Journal / COA
     */
    public function getAllocationJournal($sourceItem): string
    {
        /*
         * ========================================================
         * PURCHASE ORDER
         * ========================================================
         */
        if (
            $sourceItem instanceof
            \App\Models\PurchaseOrderItem
        ) {
            $sourceItem->loadMissing([
                'assignmentMaterialRequisitionItem.purchaseRequisitionItem.allocationJournal',
            ]);

            return
                $sourceItem
                    ->assignmentMaterialRequisitionItem
                    ?->purchaseRequisitionItem
                    ?->allocationJournal
                    ?->display_name
                ?? '-';
        }


        /*
         * ========================================================
         * ASSIGNMENT DIRECT MARKET
         * ========================================================
         *
         * AssignmentDirectMarketItem does NOT have a directMarket
         * relationship.
         *
         * The current Goods Receipt already knows its parent
         * Assignment Direct Market, therefore use:
         *
         * GoodsReceipt
         *      ↓
         * AssignmentDirectMarket
         *      ↓
         * DirectMarket
         *      ↓
         * AllocationJournal
         */
        if (
            $sourceItem instanceof
            \App\Models\AssignmentDirectMarketItem
        ) {
            $assignment = $this->goodsReceipt
                ->assignmentDirectMarket;

            if (! $assignment) {
                return '-';
            }

            $directMarket = $assignment
                ->directMarket;

            return
                $directMarket
                    ?->allocationJournal
                    ?->display_name
                ?? '-';
        }


        return '-';
    }


    /*
    |--------------------------------------------------------------------------
    | SOURCE HELPERS
    |--------------------------------------------------------------------------
    */

    protected function findSourceItem(
        int $id,
    ): PurchaseOrderItem|AssignmentDirectMarketItem|null {

        return $this->sourceItems
            ->firstWhere('id', $id);
    }

    protected function sourceForeignKey(): string
    {
        return $this->goodsReceipt->purchase_order_id
            ? 'purchase_order_item_id'
            : 'assignment_direct_market_item_id';
    }

    protected function sourceKey(
        GoodsReceiptItem $item
    ): string {

        return $item->purchase_order_item_id
            ? 'PO-' . $item->purchase_order_item_id
            : 'ADM-' .
                $item->assignment_direct_market_item_id;
    }

    protected function sourceKeyFromSourceItem(
        PurchaseOrderItem|AssignmentDirectMarketItem $item
    ): string {

        return $this->goodsReceipt->purchase_order_id
            ? 'PO-' . $item->getKey()
            : 'ADM-' . $item->getKey();
    }

    /*
    |--------------------------------------------------------------------------
    | SELECT ALL STATE
    |--------------------------------------------------------------------------
    */

    public function allSourceItemsSelected(): bool
    {
        $availableItems =
            $this->sourceItems
                ->filter(
                    fn ($item): bool =>
                        $this->getRemainingQty($item) > 0
                );

        if ($availableItems->isEmpty()) {
            return false;
        }

        foreach ($availableItems as $item) {

            $key =
                $this->sourceKeyFromSourceItem(
                    $item
                );

            if (
                ! isset(
                    $this->selectedItems[$key]
                )
            ) {
                return false;
            }
        }

        return true;
    }

    /*
    |--------------------------------------------------------------------------
    | SOURCE CODE
    |--------------------------------------------------------------------------
    */

    protected function getSourceCode(
        mixed $item
    ): string {

        return (string)
            ($item->item_code ?? '-');
    }

    /*
    |--------------------------------------------------------------------------
    | SOURCE NAME
    |--------------------------------------------------------------------------
    */

    protected function getSourceName(
        mixed $item
    ): string {

        return (string)
            ($item->item_name ?? '-');
    }

    /*
    |--------------------------------------------------------------------------
    | SOURCE UOM
    |--------------------------------------------------------------------------
    */

    protected function getSourceUom(
        mixed $item
    ): string {

        return (string) (
            $item->uom_code
            ?? $item->uom_name
            ?? '-'
        );
    }

    /*
    |--------------------------------------------------------------------------
    | SOURCE QTY
    |--------------------------------------------------------------------------
    */

    protected function getSourceQty(
        mixed $item
    ): float {

        return $item instanceof PurchaseOrderItem
            ? (float) $item->ordered_qty
            : (float) $item->assigned_qty;
    }

    /*
    |--------------------------------------------------------------------------
    | ALREADY RECEIVED
    |--------------------------------------------------------------------------
    */

    protected function getAlreadyReceived(mixed $item): float
    {
        if ($item instanceof GoodsReceiptItem) {
            return (float) $item->received_qty;
        }

        $foreignKey = $item instanceof PurchaseOrderItem
            ? 'purchase_order_item_id'
            : 'assignment_direct_market_item_id';

        return (float) GoodsReceiptItem::query()
            ->where($foreignKey, $item->getKey())
            ->whereNull('deleted_at')
            ->whereHas('goodsReceipt', function ($query) {
                $query->whereIn('rr_status', ['RECEIVED', 'CLOSE']);
            })
            ->sum('received_qty');
    }

    public function getItemStatus($sourceItem): string
    {
        $sourceQty = (float) (
            $sourceItem->ordered_qty
            ?? $sourceItem->assigned_qty
            ?? $sourceItem->requested_qty
            ?? 0
        );

        $alreadyReceived = (float) $this->getAlreadyReceived($sourceItem);

        if ($alreadyReceived <= 0) {
            return 'Open';
        }

        if ($alreadyReceived >= $sourceQty) {
            return 'Completed';
        }

        return 'Partially Received';
    }

    /*
    |--------------------------------------------------------------------------
    | REMAINING QTY
    |--------------------------------------------------------------------------
    */

    protected function getRemainingQty(mixed $item): float
    {
        return max(
            0,
            round(
                $this->getSourceQty($item)
                    - $this->getAlreadyReceived($item),
                4
            )
        );
    }

    /*
    |--------------------------------------------------------------------------
    | SOURCE NUMBER
    |--------------------------------------------------------------------------
    */

    protected function getSourceNumber(): string
    {
        if (
            $this->goodsReceipt->purchase_order_id
        ) {

            return (string) (
                $this->goodsReceipt
                    ->purchaseOrder
                    ?->document_no
                ?? '-'
            );
        }

        if (
            $this->goodsReceipt
                ->assignment_direct_market_id
        ) {

            $assignment =
                $this->goodsReceipt
                    ->assignmentDirectMarket;

            if (
                $assignment?->direct_market_id
            ) {

                return (string) (
                    DirectMarket::query()
                        ->where(
                            'id',
                            $assignment->direct_market_id
                        )
                        ->value('dm_no')
                    ??
                    $assignment->document_no
                    ??
                    '-'
                );
            }

            return (string) (
                $assignment?->document_no
                ?? '-'
            );
        }

        return '-';
    }

    /*
    |--------------------------------------------------------------------------
    | SOURCE DATE
    |--------------------------------------------------------------------------
    */

    protected function getSourceDate(): ?string
    {
        if (
            $this->goodsReceipt->purchase_order_id
        ) {

            return $this->goodsReceipt
                ->purchaseOrder
                ?->document_date
                ?->format('d-M-Y');
        }

        if (
            $this->goodsReceipt
                ->assignment_direct_market_id
        ) {

            $assignment =
                $this->goodsReceipt
                    ->assignmentDirectMarket;

            /*
            |--------------------------------------------------------------------------
            | Direct Market uses request_date as source date.
            |--------------------------------------------------------------------------
            */

            if (
                $assignment?->direct_market_id
            ) {

                return DirectMarket::query()
                    ->where(
                        'id',
                        $assignment->direct_market_id
                    )
                    ->value('request_date')
                    ?->format('d-M-Y');
            }

            return $assignment?->document_date
                ?->format('d-M-Y');
        }

        return null;
    }

    /*
    |--------------------------------------------------------------------------
    | SOURCE STATUS
    |--------------------------------------------------------------------------
    */

    protected function getSourceStatus(): string
    {
        $sourceItems =
            $this->sourceItems;

        if ($sourceItems->isEmpty()) {
            return 'Open';
        }

        $sourceQty =
            $sourceItems->sum(
                fn ($item): float =>
                    $this->getSourceQty($item)
            );

        $alreadyReceived =
            $sourceItems->sum(
                fn ($item): float =>
                    $this->getAlreadyReceived($item)
            );

        if (
            $alreadyReceived <= 0
        ) {
            return 'Open';
        }

        if (
            $alreadyReceived >= $sourceQty
        ) {
            return 'Complete';
        }

        return 'Partially Received';
    }

    /*
    |--------------------------------------------------------------------------
    | STATUS COLOR CLASS
    |--------------------------------------------------------------------------
    */

    protected function getSourceStatusClass(?string $status = null): string
    {
        $status ??= $this->getSourceStatus();

        return match ($status) {
            'Complete', 'Completed' =>
                'inline-flex rounded-md border border-sky-200 bg-sky-50 px-2 py-1 text-xs font-semibold text-sky-700',

            'Partially Received' =>
                'inline-flex rounded-md border border-amber-200 bg-amber-50 px-2 py-1 text-xs font-semibold text-amber-700',

            default =>
                'inline-flex rounded-md border border-emerald-200 bg-emerald-50 px-2 py-1 text-xs font-semibold text-emerald-700',
        };
    }

    /*
    |--------------------------------------------------------------------------
    | RENDER
    |--------------------------------------------------------------------------
    */

    public function render(): View
    {
        return view(
            'components.purchasing.goods-receipt-items-grid'
        );
    }

    #[On('goods-receipt-save-items')]
    public function handleHeaderSaveItems(): void
    {
        $this->saveReceivingItems();
    }

}