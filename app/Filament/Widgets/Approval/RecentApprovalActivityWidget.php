<?php

declare(strict_types=1);

namespace App\Filament\Widgets\Approval;

use App\Services\Approval\ApprovalDashboardService;
use Filament\Widgets\Widget;
use Illuminate\Contracts\View\View;

class RecentApprovalActivityWidget extends Widget
{
    protected string $view =
        'filament.widgets.approval.recent-approval-activity-widget';

    protected int|string|array $columnSpan = 'full';

    public int|string $perPage = 5;

    public int $activityPage = 1;

    public function updatedPerPage(): void
    {
        $this->activityPage = 1;
    }

    public function render(): View
    {
        $activities = app(
            ApprovalDashboardService::class
        )->recentActivityPaginated(
            $this->perPage,
            'activityPage',
            $this->activityPage,
        );

        return view(
            $this->view,
            [
                'activities' => $activities,
            ],
        );
    }

    public function getStatusLabel(?string $status): string
    {
        return match ($status) {
            'APPROVED' => 'Approved',
            'REJECTED' => 'Rejected',
            'PENDING' => 'Pending',
            'CANCELLED' => 'Cancelled',
            default => ucfirst(strtolower($status ?? 'Unknown')),
        };
    }

    public function getStatusColor(?string $status): string
    {
        return match ($status) {
            'APPROVED' => 'success',
            'REJECTED' => 'danger',
            'PENDING' => 'warning',
            'CANCELLED' => 'gray',
            default => 'gray',
        };
    }

    public function getStatusIcon(?string $status): string
    {
        return match ($status) {
            'APPROVED' => 'heroicon-o-check-circle',
            'REJECTED' => 'heroicon-o-x-circle',
            'PENDING' => 'heroicon-o-clock',
            'CANCELLED' => 'heroicon-o-minus-circle',
            default => 'heroicon-o-document-text',
        };
    }

    public function getDocumentUrl(
        string $documentType,
        int $documentId
    ): ?string {
        return match ($documentType) {
            'MATERIAL_REQUISITION' =>
                url('/admin/purchase-requisitions/' . $documentId),
            default => null,
        };
    }
}
