<?php

declare(strict_types=1);

namespace App\Filament\Resources\PurchaseRequisitions\Pages;

use App\Filament\Resources\PurchaseRequisitions\PurchaseRequisitionResource;
use Filament\Resources\Pages\ViewRecord;
use Filament\Actions\Action;



class ViewPurchaseRequisition extends ViewRecord
{
    protected static string $resource = PurchaseRequisitionResource::class;

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
        return $this->record->pr_no;
    }

    public function getBreadcrumb(): string
    {
        return 'View';
    }

    protected function getHeaderActions(): array
    {
        return [
            Action::make('status')
                ->label(strtoupper($this->record->status))
                ->color(match ($this->record->status) {
                    'Draft' => 'warning',
                    'Submitted' => 'info',
                    'Approved' => 'success',
                    'Rejected' => 'danger',
                    'Cancelled' => 'gray',
                    'Closed' => 'gray',
                    default => 'gray',
                })
                ->disabled(),
        ];
    }
    
}