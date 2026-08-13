<?php

declare(strict_types=1);

namespace App\Filament\Widgets\Approval;

use App\Services\Approval\ApprovalDashboardService;
use Filament\Widgets\Widget;
use Illuminate\Support\Collection;

class RecentApprovalActivityWidget extends Widget
{
    /*
    |--------------------------------------------------------------------------
    | View
    |--------------------------------------------------------------------------
    */

    protected string $view =
        'filament.widgets.approval.recent-approval-activity-widget';

    /*
    |--------------------------------------------------------------------------
    | Layout
    |--------------------------------------------------------------------------
    */

    protected int|string|array $columnSpan = 'full';

    /*
    |--------------------------------------------------------------------------
    | State
    |--------------------------------------------------------------------------
    */

    public Collection $activities;

    /*
    |--------------------------------------------------------------------------
    | Mount
    |--------------------------------------------------------------------------
    */

    public function mount(): void
    {
        $this->activities = app(
            ApprovalDashboardService::class
        )->recentActivity(10);
    }

    /*
    |--------------------------------------------------------------------------
    | Status Helpers
    |--------------------------------------------------------------------------
    */

    public function getStatusLabel(
        ?string $status
    ): string {
        return match ($status) {
            'APPROVED' => 'Approved',
            'REJECTED' => 'Rejected',
            'PENDING' => 'Pending',
            'CANCELLED' => 'Cancelled',
            default => ucfirst(
                strtolower(
                    $status ?? 'Unknown'
                )
            ),
        };
    }

    public function getStatusColor(
        ?string $status
    ): string {
        return match ($status) {
            'APPROVED' => 'success',
            'REJECTED' => 'danger',
            'PENDING' => 'warning',
            'CANCELLED' => 'gray',
            default => 'gray',
        };
    }

    public function getStatusIcon(
        ?string $status
    ): string {
        return match ($status) {
            'APPROVED' =>
                'heroicon-o-check-circle',

            'REJECTED' =>
                'heroicon-o-x-circle',

            'PENDING' =>
                'heroicon-o-clock',

            'CANCELLED' =>
                'heroicon-o-minus-circle',

            default =>
                'heroicon-o-document-text',
        };
    }

    /*
    |--------------------------------------------------------------------------
    | Document URL
    |--------------------------------------------------------------------------
    */

    public function getDocumentUrl(
        string $documentType,
        int $documentId
    ): ?string {
        return match ($documentType) {

            'MATERIAL_REQUISITION' =>
                url(
                    '/admin/purchase-requisitions/' .
                    $documentId
                ),

            default => null,
        };
    }
}