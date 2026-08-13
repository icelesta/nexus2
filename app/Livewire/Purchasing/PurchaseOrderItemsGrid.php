<?php

declare(strict_types=1);

namespace App\Livewire\Purchasing;

use App\Models\PurchaseOrder;
use Illuminate\Contracts\View\View;
use Illuminate\Database\Eloquent\Collection;
use Livewire\Component;

class PurchaseOrderItemsGrid extends Component
{
    /**
     * Purchase Order Header.
     */
    public PurchaseOrder $purchaseOrder;

    /**
     * Purchase Order Items.
     */
    public Collection $items;

    /**
     * Mount Component.
     */
    public function mount(
        PurchaseOrder $purchaseOrder,
    ): void {
        $this->purchaseOrder = $purchaseOrder;

        $this->loadItems();
    }

    /**
     * Load Purchase Order Items.
     */
    public function loadItems(): void
    {
        $this->items = $this->purchaseOrder
            ->items()
            ->with([
                'item',
                'supplier',
                'warehouse',
                'uom',
            ])
            ->orderBy('id')
            ->get();
    }

    /**
     * Refresh Grid.
     */
    public function refreshGrid(): void
    {
        $this->purchaseOrder = $this->purchaseOrder->fresh();

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
     * Total Ordered Quantity.
     */
    public function getTotalOrderedQtyProperty(): float
    {
        return (float) $this->items->sum(
            fn ($item) => (float) $item->ordered_qty
        );
    }

    /**
     * Total Received Quantity.
     */
    public function getTotalReceivedQtyProperty(): float
    {
        return (float) $this->items->sum(
            fn ($item) => (float) $item->received_qty
        );
    }

    /**
     * Total Remaining Quantity.
     */
    public function getTotalRemainingQtyProperty(): float
    {
        return (float) $this->items->sum(
            fn ($item) => (float) $item->remaining_qty
        );
    }

    /**
     * Total Gross Amount.
     */
    public function getTotalGrossAmountProperty(): float
    {
        return (float) $this->items->sum(
            fn ($item) => (float) $item->gross_amount
        );
    }

    /**
     * Total Discount Amount.
     */
    public function getTotalDiscountAmountProperty(): float
    {
        return (float) $this->items->sum(
            fn ($item) => (float) $item->discount_amount
        );
    }

    /**
     * Total Tax Amount.
     */
    public function getTotalTaxAmountProperty(): float
    {
        return (float) $this->items->sum(
            fn ($item) => (float) $item->tax_amount
        );
    }

    /**
     * Total Line Amount.
     */
    public function getTotalLineAmountProperty(): float
    {
        return (float) $this->items->sum(
            fn ($item) => (float) $item->line_total
        );
    }

    /**
     * Grand Total.
     */
    public function getGrandTotalProperty(): float
    {
        return (float) $this->items->sum(
            fn ($item) => (float) $item->grand_total
        );
    }

    /**
     * Check whether document is editable.
     *
     * Purchase Order grid is always readonly.
     */
    public function getCanEditProperty(): bool
    {
        return false;
    }

    /**
     * Render Component.
     */
    public function render(): View
    {
        return view(
            'components.purchasing.order-items-grid'
        );
    }
}