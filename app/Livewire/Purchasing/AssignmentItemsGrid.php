<?php

declare(strict_types=1);

namespace App\Livewire\Purchasing;

use App\Models\AssignmentMaterialRequisition;
use App\Models\AssignmentMaterialRequisitionItem;
use App\Models\Supplier;
use Illuminate\Contracts\View\View;
use Illuminate\Database\Eloquent\Collection;
use Livewire\Component;
use App\Services\Purchasing\AssignmentMaterialRequisitionService;
use App\Models\TaxMaster;
use Filament\Notifications\Notification;

class AssignmentItemsGrid extends Component
{
    /**
     * Assignment Header.
     */
    public AssignmentMaterialRequisition $assignment;

    /**
     * Read Only Mode.
     */
    public bool $readonly = false;

    /**
     * Assignment Items.
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
     * Selected supplier while editing.
     */
    public array $selectedSupplier = [];

    /**
     * Selected Tax.
     */
    public array $selectedTax = [];    

    /**
     * Assigned Quantity while editing.
     */
    public array $assignedQty = [];

    /**
     * Unit Price while editing.
     */
    public array $unitPrices = [];

    /**
     * Discount Percent while editing.
     */
    public array $discountPercents = [];

    /**
     * Discount Amount while editing.
     */
    public array $discountAmounts = [];


    /**
     * Mount Component.
     */
    public function mount(
        AssignmentMaterialRequisition $assignment,
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
     * Load Assignment Items.
     */
    public function loadItems(): void
    {
        $this->items = $this->assignment
            ->items()
            ->with([
                    'purchaseRequisitionItem.item',
                    'purchaseRequisitionItem.uom',
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

            $this->selectedTax[$item->id] =
                $item->tax_id;

            $this->selectedSupplier[$item->id] = $item->supplier_id;    
        }
    }

    /**
     * Refresh Grid.
     */
    public function refreshGrid(): void
    {
        $this->assignment = $this->assignment->fresh();

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
     * Total Requested Qty.
     */
    public function getTotalRequestedQtyProperty(): float
    {
        return (float) $this->items->sum(
            fn ($item) => (float) (
                $item->purchaseRequisitionItem?->quantity ?? 0
            )
        );
    }

    /**
     * Total Assigned Qty.
     */
    public function getTotalAssignedQtyProperty(): float
    {
        return (float) $this->items->sum('assigned_qty');
    }

    /**
     * Total Amount.
     */
    public function getTotalAmountProperty(): float
    {
        return (float) $this->items->sum(
            fn ($item) => (float) $item->grand_total
        );
    }

    /**
     * Grid Editable?
     */
    public function getCanEditProperty(): bool
    {
        return ! $this->readonly
         && $this->assignment->canEdit();
    }

    /**
     * Find Assignment Item.
     */
    protected function findItem(
        int $itemId,
    ): ?AssignmentMaterialRequisitionItem {
        return AssignmentMaterialRequisitionItem::query()
            ->with([
                'purchaseRequisitionItem.item',
                'purchaseRequisitionItem.uom',
                'supplier',
            ])
            ->find($itemId);
    }

    /**
     * Start Editing.
     */
    public function editRow(int $itemId): void
    {
        if (! $this->canEdit) {
            return;
        }

        $item = $this->findItem($itemId);

        if (! $item) {
            return;
        }

        $this->editingRow = $item->id;

        $this->selectedSupplier[$item->id] = $item->supplier_id;

        $this->assignedQty[$item->id] =
            (float) $item->assigned_qty;

        $this->unitPrices[$item->id] =
            (float) $item->unit_price;

        $this->discountPercents[$item->id] =
            (float) $item->discount_percent;

        $this->discountAmounts[$item->id] =
            (float) $item->discount_amount;

        $this->selectedTax[$item->id] =
            $item->tax_id;

    }

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

        $this->selectedTax = [];
    }

    /**
     * Save Supplier Assignment.
     */
    public function updateRow(int $itemId): void
    {
        if (! $this->canEdit) {
            return;
        }


        $assignedQtyInput = $this->assignedQty[$itemId] ?? null;

        if (
            ! is_string($assignedQtyInput)
            && ! is_numeric($assignedQtyInput)
        ) {
            Notification::make()
                ->danger()
                ->title('Invalid Quantity')
                ->body('Please enter a number.')
                ->send();

            return;
        }

        $assignedQtyInput = (string) $assignedQtyInput;

        if (! preg_match('/^\d+(?:\.\d+)?$/', $assignedQtyInput)) {
            Notification::make()
                ->danger()
                ->title('Invalid Quantity')
                ->body('Please enter a number.')
                ->send();

            return;
        }

        $assignedQty = (float) $assignedQtyInput;

        $unitPrice =
            (float) ($this->unitPrices[$itemId] ?? 0);

        $discountPercent =
            (float) ($this->discountPercents[$itemId] ?? 0);

        $discountAmount =
            (float) ($this->discountAmounts[$itemId] ?? 0);

        $supplierId = isset($this->selectedSupplier[$itemId])
            && $this->selectedSupplier[$itemId] !== ''
            ? (int) $this->selectedSupplier[$itemId]
            : null;

        $taxId = isset($this->selectedTax[$itemId])
            && $this->selectedTax[$itemId] !== ''
            ? (int) $this->selectedTax[$itemId]
            : null;

        app(AssignmentMaterialRequisitionService::class)
            ->updateAssignmentItem(
                itemId: $itemId,
                supplierId: $supplierId,
                assignedQty: $assignedQty,
                unitPrice: $unitPrice,
                discountPercent: $discountPercent,
                discountAmount: $discountAmount,
                taxId: $taxId,
            );

        $this->cancelEdit();

        $this->refreshGrid();
    }

    /**
     * Render Component.
     */
    public function render(): View
    {
        return view(
            'components.purchasing.assignment-items-grid',
        );
    }
}