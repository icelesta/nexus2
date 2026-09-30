<?php

declare(strict_types=1);

namespace App\Filament\Widgets\Approval;

use App\Services\Approval\ApprovalDashboardService;
use Filament\Widgets\Widget;
use Illuminate\Contracts\View\View;
use Illuminate\Database\Eloquent\Collection;

class MyPendingApprovalsWidget extends Widget
{
    protected string $view =
        'filament.widgets.approval.my-pending-approvals-widget';

    protected int|string|array $columnSpan = 'full';

    public int|string $perPage = 5;

    public int $pendingPage = 1;

    public function updatedPerPage(): void
    {
        $this->pendingPage = 1;
    }

    public function render(): View
    {
        $user = auth()->user();

        if (! $user) {
            $approvals = new Collection();
            $pendingCount = 0;
        } else {
            $service = app(ApprovalDashboardService::class);

            $approvals = $service->pendingApprovalsPaginated(
                $user,
                $this->perPage,
                'pendingPage',
                $this->pendingPage,
            );

            $pendingCount = $service->pendingCount($user);
        }

        return view(
            $this->view,
            [
                'approvals' => $approvals,
                'pendingCount' => $pendingCount,
            ],
        );
    }

    public function getDocumentUrl($transaction): string
    {
        return match ($transaction->document_type) {
            'ASSIGNMENT_DIRECT_MARKET' =>
                url('/admin/assignment-direct-markets/' . $transaction->document_id),
            'DIRECT_MARKET' =>
                url('/admin/direct-markets/' . $transaction->document_id),
            'PURCHASE_ORDER' =>
                url('/admin/purchase-orders/' . $transaction->document_id),
            'MATERIAL_REQUISITION' =>
                url('/admin/purchase-requisitions/' . $transaction->document_id),
            default => '#',
        };
    }

    public function getCurrentRole($transaction): string
    {
        return $transaction->currentStep()?->role_name ?? 'Approval';
    }

    public function getCurrentLevel($transaction): string
    {
        return 'Level ' . (int) $transaction->current_level;
    }
}
