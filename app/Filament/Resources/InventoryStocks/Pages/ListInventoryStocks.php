<?php

declare(strict_types=1);

namespace App\Filament\Resources\InventoryStocks\Pages;

use App\Filament\Resources\InventoryStocks\InventoryStockResource;
use App\Filament\Resources\InventoryStocks\Widgets\InventoryStockStats;
use Filament\Resources\Pages\ListRecords;

class ListInventoryStocks extends ListRecords
{
    protected static string $resource = InventoryStockResource::class;

    /**
     * Normalize table state passed to header widgets.
     */
    public function getWidgetData(): array
    {
        return [
            'activeTab' => $this->activeTab,
            'paginators' => $this->paginators,
            'tableColumnSearches' => $this->tableColumnSearches ?? [],
            'tableFilters' => $this->tableFilters,
            'tableGrouping' => $this->tableGrouping,
            'tableRecordsPerPage' => $this->tableRecordsPerPage,
            'tableSearch' => $this->tableSearch ?? '',
            'tableSort' => $this->tableSort,
        ];
    }

    protected function getHeaderWidgets(): array
    {
        return [
            InventoryStockStats::class,
        ];
    }

    public function getHeaderWidgetsColumns(): int | array
    {
        return 4;
    }

    public function getTitle(): string
    {
        return 'Inventory Stock';
    }

    public function getHeading(): string
    {
        return 'Inventory Stock';
    }

    public function getSubheading(): ?string
    {
        return 'Monitor stock balances by item and warehouse.';
    }

    public function getBreadcrumb(): string
    {
        return 'Inventory Stock';
    }

    protected function getHeaderActions(): array
    {
        return [];
    }
}