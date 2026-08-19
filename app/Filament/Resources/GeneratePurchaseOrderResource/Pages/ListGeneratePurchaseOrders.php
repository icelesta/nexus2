<?php

declare(strict_types=1);

namespace App\Filament\Resources\GeneratePurchaseOrderResource\Pages;

use App\Filament\Resources\GeneratePurchaseOrderResource\GeneratePurchaseOrderResource;

use Filament\Resources\Pages\ListRecords;

class ListGeneratePurchaseOrders extends ListRecords
{
    protected static string $resource =
        GeneratePurchaseOrderResource::class;

    protected function getHeaderActions(): array
    {
        return [];
    }

    public function getTitle(): string
    {
        return 'Generate Purchase Order';
    }

    public function getHeading(): string
    {
        return 'Generate Purchase Order';
    }

    public function getSubheading(): ?string
    {
        return 'Review approval status and generate Purchase Orders for approved Assignment Material Requisitions.';
    }

    public function getBreadcrumb(): string
    {
        return 'Generate PO';
    }
}