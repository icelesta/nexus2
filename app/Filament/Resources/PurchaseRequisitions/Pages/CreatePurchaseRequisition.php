<?php

declare(strict_types=1);

namespace App\Filament\Resources\PurchaseRequisitions\Pages;

use App\Filament\Resources\PurchaseRequisitions\PurchaseRequisitionResource;
use Filament\Resources\Pages\CreateRecord;
use App\Services\Purchasing\PurchaseRequisitionService;

class CreatePurchaseRequisition extends CreateRecord
{
    protected static string $resource = PurchaseRequisitionResource::class;



    public function getTitle(): string
    {
        return 'Create Material Requisition';
    }

    public function getHeading(): string
    {
        return 'Create Material Requisition';
    }

    public function getSubheading(): ?string
    {
        return 'Create a new Material Requisition document.';
    }

    public function getBreadcrumb(): string
    {
        return 'Create';
    }

    protected function getCreatedNotificationTitle(): ?string
    {
        return 'Material Requisition created successfully.';
    }

    protected function getRedirectUrl(): string
    {
        return static::getResource()::getUrl('index');
    }

    
}