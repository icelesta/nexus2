<?php

declare(strict_types=1);

namespace App\Livewire\Purchasing;

use App\Models\AdmLimitMaster;
use Filament\Notifications\Notification;

use App\Models\AssignmentDirectMarket;
use App\Models\AssignmentDirectMarketItem;
use App\Models\Supplier;
use App\Models\TaxMaster;
use App\Services\Purchasing\AssignmentDirectMarketService;
use Illuminate\Contracts\View\View;
use Illuminate\Database\Eloquent\Collection;
use Livewire\Component;

class AssignmentDirectMarketItemsGrid extends Component
{
    /**
     * Assignment Direct Market Header.
     */
    public AssignmentDirectMarket $assignment;

    /**
     * Read Only Mode.
     */
    public bool $readonly = false;

    /**
     * Assignment Direct Market Items.
     */
    public Collection $items;

    /**
     * Supplier List.
     */
    public Collection $suppliers;

    /**
     * Tax Master List.
     */
    public Collection $taxes;

    /**
     * Row currently being edited.
     */
    public ?int $editingRow = null;

    /**
     * Selected Supplier.
     */
    public array $selectedSupplier = [];

    /**
     * Selected Tax.
     */
    public array $selectedTax = [];

    /**
     * Assigned Quantity.
     */
    public array $assignedQty = [];

    /**
     * Unit Price.
     */
    public array $unitPrices = [];

    /**
     * Discount Percent.
     */
    public array $discountPercents = [];

    /**
     * Discount Amount.
     */
    public array $discountAmounts = [];    

    /**
     * Lead Time in Days.
     */
    public array $leadTimes = [];    

    /**
     * Mount Component.
     */
    public function mount(
        AssignmentDirectMarket $assignment,
        bool $readonly = false,
    ): void {

        $this->assignment = $assignment;
        $this->readonly = $readonly;

        $this->loadItems();

        $this->suppliers = Supplier::query()
            ->orderBy('supplier_name')
            ->get();

        $this->taxes = TaxMaster::query()
            ->where('is_active', true)
            ->orderBy('tax_name')
            ->get();
    }

    /**
     * Load Assignment Direct Market Items.
     */
    public function loadItems(): void
    {
        $this->items = $this->assignment
            ->items()
            ->with([
                'directMarketItem.item',
                'directMarketItem.uom',
                'item',
                'uom',
                'supplier',
                'tax',
            ])
            ->orderBy('id')
            ->get();

        foreach ($this->items as $item) {

            $this->assignedQty[$item->id] =
                (float) $item->assigned_qty;

            $this->unitPrices[$item->id] =
                (float) $item->unit_price;

            $this->discountPercents[$item->id] =
                (float) $item->discount_percent;

            $this->discountAmounts[$item->id] =
                (float) $item->discount_amount;                

            $this->leadTimes[$item->id] =
                (int) ($item->lead_time_days ?? 0);                

            $this->selectedTax[$item->id] =
                $item->tax_id;

            $this->selectedSupplier[$item->id] =
                $item->supplier_id;
        }
    }

    /**
     * Refresh Grid.
     */
    public function refreshGrid(): void
    {
        $this->assignment =
            $this->assignment->fresh();

        $this->loadItems();
    }

    /**
     * Total Item Count.
     */
    public function getTotalItemsProperty(): int
    {
        return $this->items->count();
    }

    /**
     * Total Requested Quantity.
     */
    public function getTotalRequestedQtyProperty(): float
    {
        return (float) $this->items->sum(
            fn ($item): float =>
                (float) $item->requested_qty
        );
    }

    /**
     * Total Assigned Quantity.
     */
    public function getTotalAssignedQtyProperty(): float
    {
        return (float) $this->items->sum(
            fn ($item): float =>
                (float) $item->assigned_qty
        );
    }

    /**
     * Total Amount.
     */
    public function getTotalAmountProperty(): float
    {
        return (float) $this->items->sum(
            fn ($item): float =>
                (float) $item->grand_total
        );
    }

    /**
     * Grid Editable?
     */
    public function getCanEditProperty(): bool
    {
        return ! $this->readonly
            && in_array(
                $this->assignment->status,
                [
                    AssignmentDirectMarket::STATUS_DRAFT,
                    AssignmentDirectMarket::STATUS_UPDATED,
                ],
                true
            );
    }

    /**
     * Find Assignment Direct Market Item.
     */
    protected function findItem(
        int $itemId,
    ): ?AssignmentDirectMarketItem {

        return AssignmentDirectMarketItem::query()
            ->with([
                'directMarketItem.item',
                'directMarketItem.uom',
                'item',
                'uom',
                'supplier',
                'tax',
            ])
            ->where(
                'assignment_direct_market_id',
                $this->assignment->getKey()
            )
            ->find($itemId);
    }

    /**
     * Start Editing.
     */
    public function editRow(
        int $itemId,
    ): void {

        if (! $this->canEdit) {
            return;
        }

        $item = $this->findItem($itemId);

        if (! $item) {
            return;
        }

        $this->editingRow = $item->id;

        $this->selectedSupplier[$item->id] =
            $item->supplier_id;

        $this->assignedQty[$item->id] =
            (float) $item->assigned_qty;

        $this->unitPrices[$item->id] =
            (float) $item->unit_price;

        $this->discountPercents[$item->id] =
            (float) $item->discount_percent;

        $this->discountAmounts[$item->id] =
            (float) $item->discount_amount;            

        $this->leadTimes[$item->id] =
            (int) ($item->lead_time_days ?? 0);            

        $this->selectedTax[$item->id] =
            $item->tax_id;
    }

    /**
     * Cancel Editing.
     */
    public function cancelEdit(): void
    {
        if (! $this->canEdit) {
            return;
        }

        $this->editingRow = null;

        $this->selectedSupplier = [];

        $this->assignedQty = [];

        $this->unitPrices = [];

        $this->discountPercents = [];

        $this->discountAmounts = [];

        $this->leadTimes = [];

        $this->selectedTax = [];

        $this->loadItems();
    }

    /**
     * Save Assignment Direct Market Item.
     */
    protected function saveRow(
        int $itemId,
    ): void {

        if (! $this->canEdit) {
            return;
        }

        $assignedQty =
            (float) (
                $this->assignedQty[$itemId] ?? 0
            );

        $unitPrice =
            (float) (
                $this->unitPrices[$itemId] ?? 0
            );

        $discountPercent =
            (float) (
                $this->discountPercents[$itemId] ?? 0
            );

        $discountAmount =
            (float) (
                $this->discountAmounts[$itemId] ?? 0
            );

        $leadTimeDays =
            (int) (
                $this->leadTimes[$itemId] ?? 0
            );

        $supplierId =
            isset($this->selectedSupplier[$itemId])
            && $this->selectedSupplier[$itemId] !== ''
                ? (int) $this->selectedSupplier[$itemId]
                : null;

        $taxId =
            isset($this->selectedTax[$itemId])
            && $this->selectedTax[$itemId] !== ''
                ? (int) $this->selectedTax[$itemId]
                : null;

        app(
            AssignmentDirectMarketService::class
        )->updateItemPricing(
            itemId: $itemId,
            data: [

                'assigned_qty' =>
                    $assignedQty,

                'unit_price' =>
                    $unitPrice,

                'discount_percent' =>
                    $discountPercent,

                'discount_amount' =>
                    $discountAmount,

                'lead_time_days' =>
                    $leadTimeDays,

                'supplier_id' =>
                    $supplierId,

                'tax_id' =>
                    $taxId,

            ],
        );

        $this->refreshGrid();
    }

    /**
     * Update Assignment Direct Market Item.
     *
     * Saves the current item and keeps the row
     * in edit mode.
     */
    public function updateRow(
        int $itemId,
    ): void {

        $this->saveRow($itemId);

        if ($this->canEdit) {
            $this->editingRow = $itemId;
        }
    }

    /**
     * Validate ADM Limit before Save & Close.
     *
     * The current edited row is projected using the
     * current Livewire values before anything is saved.
     */
    protected function validateAdmLimitBeforeClose(
        int $itemId,
    ): bool {

        $limitMaster = AdmLimitMaster::query()
            ->active()
            ->first();

        if ($limitMaster === null) {

            Notification::make()
                ->danger()
                ->title('ADM Limit Master Not Configured')
                ->body(
                    'No active ADM Limit Master configuration is available. '
                    . 'Save & Close cannot be completed.'
                )
                ->persistent()
                ->send();

            return false;
        }

        $item = $this->findItem($itemId);

        if (! $item) {
            return false;
        }

        /*
        |--------------------------------------------------------------------------
        | CURRENT EDITED VALUES
        |--------------------------------------------------------------------------
        */

        $assignedQty = (float) (
            $this->assignedQty[$itemId]
            ?? $item->assigned_qty
            ?? 0
        );

        $unitPrice = (float) (
            $this->unitPrices[$itemId]
            ?? $item->unit_price
            ?? 0
        );

        $discountPercent = (float) (
            $this->discountPercents[$itemId]
            ?? $item->discount_percent
            ?? 0
        );

        $discountAmount = (float) (
            $this->discountAmounts[$itemId]
            ?? $item->discount_amount
            ?? 0
        );

        /*
        |--------------------------------------------------------------------------
        | SAME PRICING BASIS AS ADM SERVICE
        |--------------------------------------------------------------------------
        */

        $grossAmount = round(
            $assignedQty * $unitPrice,
            2
        );

        if ($discountPercent > 0) {

            $discountAmount = round(
                $grossAmount * ($discountPercent / 100),
                2
            );

        } elseif (
            $discountAmount > 0
            && $grossAmount > 0
        ) {

            $discountPercent = round(
                ($discountAmount / $grossAmount) * 100,
                2
            );
        }

        $netAmount = round(
            max(
                0,
                $grossAmount - $discountAmount
            ),
            2
        );

        /*
        |--------------------------------------------------------------------------
        | Tax
        |--------------------------------------------------------------------------
        |
        | Keep existing ADM tax behavior unchanged.
        |
        */

        $taxPercent = (float) (
            $item->tax_percent ?? 0
        );

        $taxAmount = round(
            $netAmount * ($taxPercent / 100),
            2
        );

        $projectedGrandTotal = round(
            $netAmount + $taxAmount,
            2
        );

        /*
        |--------------------------------------------------------------------------
        | PROJECTED ADM TOTAL
        |--------------------------------------------------------------------------
        */

        $currentTotal = round(
            (float) $this->assignment
                ->items()
                ->sum('grand_total'),
            2
        );

        $oldItemTotal = round(
            (float) $item->grand_total,
            2
        );

        $projectedTotal = round(
            $currentTotal
            - $oldItemTotal
            + $projectedGrandTotal,
            2
        );

        $limitAmount = round(
            (float) $limitMaster->limit_amount,
            2
        );

        /*
        |--------------------------------------------------------------------------
        | LIMIT RULE
        |--------------------------------------------------------------------------
        |
        | < limit  = ALLOW
        | >= limit = BLOCK
        |
        */

        if ($projectedTotal >= $limitAmount) {

            Notification::make()
                ->danger()
                ->title('ADM Limit Exceeded')
                ->body(
                    'Total Amount ADM Rp '
                    . number_format(
                        $projectedTotal,
                        2,
                        ',',
                        '.'
                    )
                    . ' telah mencapai atau melebihi '
                    . 'limit approval DM sebesar Rp '
                    . number_format(
                        $limitAmount,
                        2,
                        ',',
                        '.'
                    )
                    . '. Save & Close tidak dapat dilanjutkan.'
                )
                ->persistent()
                ->send();

            return false;
        }

        return true;
    }


    /**
     * Save Assignment Direct Market Item
     * and close the current row editing mode.
     */
    public function saveAndClose(
        int $itemId,
    ): void {

        /*
        |--------------------------------------------------------------------------
        | ADM LIMIT CHECK
        |--------------------------------------------------------------------------
        */

        if (! $this->validateAdmLimitBeforeClose($itemId)) {
            return;
        }

        /*
        |--------------------------------------------------------------------------
        | EXISTING SAVE & CLOSE FLOW
        |--------------------------------------------------------------------------
        */

        $this->saveRow($itemId);

        $this->editingRow = null;

        $this->selectedSupplier = [];

        $this->assignedQty = [];

        $this->unitPrices = [];

        $this->discountPercents = [];

        $this->discountAmounts = [];

        $this->leadTimes = [];

        $this->selectedTax = [];

        $this->loadItems();
    }
    /**
     * Render Component.
     */
    public function render(): View
    {
        return view(
            'components.purchasing.assignment-direct-market-items-grid',
        );
    }

    protected function getAdmLimitAmount(): ?float
    {
        $limitAmount = AdmLimitMaster::query()
            ->active()
            ->value('limit_amount');

        return $limitAmount !== null
            ? (float) $limitAmount
            : null;
    }
    
}