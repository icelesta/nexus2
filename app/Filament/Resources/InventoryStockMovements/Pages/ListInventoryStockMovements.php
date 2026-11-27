<?php

declare(strict_types=1);

namespace App\Filament\Resources\InventoryStockMovements\Pages;

use App\Filament\Resources\InventoryStockMovements\InventoryStockMovementResource;
use App\Filament\Support\Concerns\HasStockMovementFilters;
use Filament\Resources\Pages\ListRecords;

class ListInventoryStockMovements extends ListRecords
{
    use HasStockMovementFilters;

    protected static string $resource = InventoryStockMovementResource::class;

    public function mount(): void
    {
        parent::mount();

        $this->mountStockMovementFilters();
    }
}