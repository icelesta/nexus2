<?php

declare(strict_types=1);

namespace App\Filament\Widgets\Approval;

use App\Services\Approval\ApprovalDashboardService;
use Filament\Widgets\Widget;
use Illuminate\Database\Eloquent\Collection;

class MyPendingApprovalsWidget extends Widget
{
    /*
    |--------------------------------------------------------------------------
    | View
    |--------------------------------------------------------------------------
    |
    | Filament 5 Widget::$view is NON-STATIC.
    |
    */

    protected string $view =
        'filament.widgets.approval.my-pending-approvals-widget';

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

    public Collection $approvals;

    /*
    |--------------------------------------------------------------------------
    | Mount
    |--------------------------------------------------------------------------
    */

    public function mount(): void
    {
        $user = auth()->user();

        if (! $user) {
            $this->approvals = new Collection();

            return;
        }

        $this->approvals = app(
            ApprovalDashboardService::class
        )->pendingApprovals(
            $user,
            10
        );
    }

    /*
    |--------------------------------------------------------------------------
    | Helpers
    |--------------------------------------------------------------------------
    */

    public function getDocumentUrl(
        int $documentId
    ): string {
        return url(
            '/admin/purchase-requisitions/' . $documentId
        );
    }

    public function getCurrentRole(
        $transaction
    ): string {
        return $transaction
            ->currentStep()
            ?->role_name
            ?? 'Approval';
    }

    public function getCurrentLevel(
        $transaction
    ): string {
        return 'Level ' .
            (int) $transaction->current_level;
    }
}