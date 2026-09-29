<?php

declare(strict_types=1);

namespace App\Filament\Resources\PurchaseRequisitions\Pages;

use App\Filament\Resources\PurchaseRequisitions\PurchaseRequisitionResource;
use App\Filament\Support\Concerns\HasGlobalTransactionFilters;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;
use App\Models\PurchaseRequisition;

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
            PurchaseRequisition::STATUS_DRAFT
                => PurchaseRequisition::STATUS_DRAFT,

            'Waiting Approval'
                => 'Waiting Approval',

            'Approval 1/2'
                => 'Approval 1/2',

            'Approval 2/2'
                => 'Approval 2/2',

            PurchaseRequisition::STATUS_APPROVED
                => PurchaseRequisition::STATUS_APPROVED,

            PurchaseRequisition::STATUS_REJECTED
                => PurchaseRequisition::STATUS_REJECTED,

            PurchaseRequisition::STATUS_CANCELLED
                => PurchaseRequisition::STATUS_CANCELLED,

            PurchaseRequisition::STATUS_CLOSED
                => PurchaseRequisition::STATUS_CLOSED,
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