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
        $transaction
    ): string {
        return match ($transaction->document_type) {

            'ASSIGNMENT_DIRECT_MARKET' =>
                url(
                    '/admin/assignment-direct-markets/'
                    . $transaction->document_id
                ),

            'DIRECT_MARKET' =>
                url(
                    '/admin/direct-markets/'
                    . $transaction->document_id
                ),

            'PURCHASE_ORDER' =>
                url(
                    '/admin/purchase-orders/'
                    . $transaction->document_id
                ),

            'MATERIAL_REQUISITION' =>
                url(
                    '/admin/purchase-requisitions/'
                    . $transaction->document_id
                ),

            default =>
                '#',
        };
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