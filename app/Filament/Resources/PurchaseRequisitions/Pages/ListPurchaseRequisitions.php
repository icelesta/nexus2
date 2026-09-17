<?php

declare(strict_types=1);

namespace App\Filament\Resources\PurchaseRequisitions\Pages;

use App\Filament\Resources\PurchaseRequisitions\PurchaseRequisitionResource;
use App\Filament\Support\Concerns\HasGlobalTransactionFilters;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListPurchaseRequisitions extends ListRecords
{
    use HasGlobalTransactionFilters;

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

    public function getGlobalTransactionStatusOptions(): array
    {
        return [
            'Draft'             => 'Draft',
            'Waiting Approval'  => 'Waiting Approval',
            'Approved'          => 'Approved',
            'Rejected'          => 'Rejected',
            'Cancelled'         => 'Cancelled',
            'Closed'            => 'Closed',
        ];
    }

    public function mount(): void
    {
        parent::mount();

        $this->globalDepartmentFilter =
            $this->getDefaultGlobalDepartmentFilter();
    }

    protected function getDefaultGlobalDepartmentFilter(): ?int
    {
        $user = auth()->user();

        if ($user?->global_filter_all_departments) {
            return null;
        }

        return $user?->department_id;
    }

}