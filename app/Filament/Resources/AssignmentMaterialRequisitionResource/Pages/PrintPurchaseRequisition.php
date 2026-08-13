<?php

declare(strict_types=1);

namespace App\Filament\Resources\PurchaseRequisitions\Pages;

use App\Filament\Resources\PurchaseRequisitions\PurchaseRequisitionResource;
use App\Models\PurchaseRequisition;
use Filament\Resources\Pages\Page;

class PrintPurchaseRequisition extends Page
{
    protected static string $resource = PurchaseRequisitionResource::class;

    protected string $view =
        'filament.resources.purchase-requisitions.pages.print-purchase-requisition';

    public PurchaseRequisition $record;

    public function mount(PurchaseRequisition $record): void
    {
        $this->record = $record;
    }

    public function getTitle(): string
    {
        return 'Material Requisition Preview';
    }

    public function getHeading(): string
    {
        return 'Material Requisition Preview';
    }

    public function getSubheading(): ?string
    {
        return 'Preview document before printing or exporting to PDF.';
    }
}