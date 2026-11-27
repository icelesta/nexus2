<?php

declare(strict_types=1);

namespace App\Filament\Support\Concerns;

use App\Models\InventoryStockMovement;
use App\Models\Item;
use App\Models\User;
use App\Models\Warehouse;
use Illuminate\Database\Eloquent\Builder;

trait HasStockMovementFilters
{
    public ?string $movementTypeFilter = null;

    public ?int $movementWarehouseFilter = null;

    public ?int $movementItemFilter = null;

    public ?int $movementCreatorFilter = null;

    public ?string $movementDateFrom = null;

    public ?string $movementDateTo = null;

    public ?string $movementReferenceFilter = null;

    public array $appliedMovementFilters = [];

    public function mountStockMovementFilters(): void
    {
        $this->applyStockMovementFilters();
    }

    public function applyStockMovementFilters(): void
    {
        $this->appliedMovementFilters = [
            'movement_type' => $this->movementTypeFilter,
            'warehouse_id' => $this->movementWarehouseFilter,
            'item_id' => $this->movementItemFilter,
            'created_by' => $this->movementCreatorFilter,
            'date_from' => $this->movementDateFrom,
            'date_to' => $this->movementDateTo,
            'reference' => trim($this->movementReferenceFilter ?? ''),
        ];
    }

    public function resetStockMovementFilters(): void
    {
        $this->movementTypeFilter = null;
        $this->movementWarehouseFilter = null;
        $this->movementItemFilter = null;
        $this->movementCreatorFilter = null;
        $this->movementDateFrom = null;
        $this->movementDateTo = null;
        $this->movementReferenceFilter = null;

        $this->applyStockMovementFilters();
    }

    public function hasActiveStockMovementFilters(): bool
    {
        foreach ($this->appliedMovementFilters as $value) {
            if (filled($value)) {
                return true;
            }
        }

        return false;
    }

    public function getMovementTypeOptionsProperty(): array
    {
        return InventoryStockMovement::query()
            ->whereNotNull('movement_type')
            ->distinct()
            ->orderBy('movement_type')
            ->pluck('movement_type', 'movement_type')
            ->all();
    }

    public function getMovementWarehouseOptionsProperty(): array
    {
        return Warehouse::query()
            ->orderBy('warehouse_name')
            ->pluck('warehouse_name', 'id')
            ->all();
    }

    public function getMovementItemOptionsProperty(): array
    {
        return Item::query()
            ->orderBy('item_code')
            ->get(['id', 'item_code', 'item_name'])
            ->mapWithKeys(fn (Item $item): array => [
                $item->id => $item->item_code . ' - ' . $item->item_name,
            ])
            ->all();
    }

    public function getMovementCreatorOptionsProperty(): array
    {
        return User::query()
            ->orderBy('name')
            ->pluck('name', 'id')
            ->all();
    }

    public function applyStockMovementFiltersToQuery(
        Builder $query,
    ): Builder {
        $filters = $this->appliedMovementFilters;

        if (filled($filters['movement_type'] ?? null)) {
            $query->where(
                'inventory_stock_movements.movement_type',
                $filters['movement_type']
            );
        }

        if (filled($filters['warehouse_id'] ?? null)) {
            $query->where(
                'inventory_stock_movements.warehouse_id',
                $filters['warehouse_id']
            );
        }

        if (filled($filters['item_id'] ?? null)) {
            $query->where(
                'inventory_stock_movements.item_id',
                $filters['item_id']
            );
        }

        if (filled($filters['created_by'] ?? null)) {
            $query->where(
                'inventory_stock_movements.created_by',
                $filters['created_by']
            );
        }

        if (filled($filters['date_from'] ?? null)) {
            $query->whereDate(
                'inventory_stock_movements.created_at',
                '>=',
                $filters['date_from']
            );
        }

        if (filled($filters['date_to'] ?? null)) {
            $query->whereDate(
                'inventory_stock_movements.created_at',
                '<=',
                $filters['date_to']
            );
        }

        if (filled($filters['reference'] ?? null)) {
            $search = $filters['reference'];

            $query->whereHas(
                'goodsReceipt',
                fn (Builder $receiptQuery) => $receiptQuery
                    ->where(function (Builder $referenceQuery) use ($search): void {
                        $referenceQuery
                            ->where('grn_no', 'like', "%{$search}%")
                            ->orWhere('document_number', 'like', "%{$search}%");
                    })
            );
        }

        return $query;
    }

    public function canUseGlobalTransactionFilters(): bool
    {
        return auth()->check();
    }

}