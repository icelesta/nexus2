<?php

declare(strict_types=1);

namespace App\Filament\Resources\PurchaseRequisitions\Pages;

use App\Filament\Resources\PurchaseRequisitions\PurchaseRequisitionResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListPurchaseRequisitions extends ListRecords
{
    protected static string $resource = PurchaseRequisitionResource::class;

    protected function getHeaderActions(): array
    {
        return [

            CreateAction::make()
                ->label('New Material Requisition')
                ->icon('heroicon-o-plus'),

        ];
    }

    public function getTitle(): string
    {
        return 'Material Requisition';
    }

    public function getHeading(): string
    {
        return 'Material Requisition';
    }

    public function getSubheading(): ?string
    {
        return 'Manage Material Requisitions, requested items, approval workflow, and document status.';
    }

    public function getBreadcrumb(): string
    {
        return 'Material Requisition';
    }
}