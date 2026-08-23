<?php

declare(strict_types=1);

namespace App\Services\Purchasing;

use App\Models\AssignmentMaterialRequisition;
use App\Models\PurchaseOrder;
use App\Models\PurchaseRequisition;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;

class PurchasingDashboardService
{
    /*
    |--------------------------------------------------------------------------
    | KPI
    |--------------------------------------------------------------------------
    */

    /**
     * Get all executive KPIs.
     */
    public function getKpis(
        ?int $branchId,
        ?int $departmentId,
        Carbon $dateFrom,
        Carbon $dateTo,
    ): array {

        $previousPeriod = $this->getPreviousPeriod(
            $dateFrom,
            $dateTo,
        );

        /*
        |--------------------------------------------------------------------------
        | Purchase Value
        |--------------------------------------------------------------------------
        */

        $purchaseValue =
            $this->purchaseOrderQuery(
                $branchId,
                $departmentId,
                $dateFrom,
                $dateTo,
            )->sum('grand_total');

        $previousPurchaseValue =
            $this->purchaseOrderQuery(
                $branchId,
                $departmentId,
                $previousPeriod['from'],
                $previousPeriod['to'],
            )->sum('grand_total');

        /*
        |--------------------------------------------------------------------------
        | Open Material Requisition
        |--------------------------------------------------------------------------
        */

        $openMr =
            $this->materialRequisitionQuery(
                $branchId,
                $departmentId,
                $dateFrom,
                $dateTo,
            )
            ->whereNotIn('status', [
                PurchaseRequisition::STATUS_DRAFT,
                PurchaseRequisition::STATUS_REJECTED,
                PurchaseRequisition::STATUS_CANCELLED,
                PurchaseRequisition::STATUS_CLOSED,
            ])
            ->count();

        /*
        |--------------------------------------------------------------------------
        | Pending Assignment
        |--------------------------------------------------------------------------
        |
        | Current AMR workflow:
        |
        | Draft
        | Updated
        | Waiting Approval
        |
        | Pending Assignment only means AMR that
        | still requires Purchasing PIC processing.
        |
        */

        $pendingAssignment =
            $this->assignmentQuery(
                $branchId,
                $departmentId,
                $dateFrom,
                $dateTo,
            )
            ->whereIn('status', [
                AssignmentMaterialRequisition::STATUS_DRAFT,
                AssignmentMaterialRequisition::STATUS_UPDATED,
            ])
            ->count();

        /*
        |--------------------------------------------------------------------------
        | PO Pending Approval
        |--------------------------------------------------------------------------
        */

        $poPendingApproval =
            $this->purchaseOrderQuery(
                $branchId,
                $departmentId,
                $dateFrom,
                $dateTo,
            )
            ->whereIn('approval_status', [
                PurchaseOrder::APPROVAL_PENDING,
                PurchaseOrder::APPROVAL_WAITING,
            ])
            ->count();

        /*
        |--------------------------------------------------------------------------
        | PO Approved
        |--------------------------------------------------------------------------
        */

        $poApproved =
            $this->purchaseOrderQuery(
                $branchId,
                $departmentId,
                $dateFrom,
                $dateTo,
            )
            ->where(
                'approval_status',
                PurchaseOrder::APPROVAL_APPROVED,
            )
            ->count();

        /*
        |--------------------------------------------------------------------------
        | Total Spend
        |--------------------------------------------------------------------------
        |
        | For V1:
        |
        | Total Spend = approved PO value.
        |
        */

        $totalSpend =
            $this->purchaseOrderQuery(
                $branchId,
                $departmentId,
                $dateFrom,
                $dateTo,
            )
            ->where(
                'approval_status',
                PurchaseOrder::APPROVAL_APPROVED,
            )
            ->sum('grand_total');

        /*
        |--------------------------------------------------------------------------
        | Return
        |--------------------------------------------------------------------------
        */

        return [

            'purchase_value' => [
                'value' => round(
                    (float) $purchaseValue,
                    2,
                ),

                'previous' => round(
                    (float) $previousPurchaseValue,
                    2,
                ),

                'change_percent' =>
                    $this->percentageChange(
                        (float) $previousPurchaseValue,
                        (float) $purchaseValue,
                    ),
            ],

            'open_mr' => [
                'value' => $openMr,
            ],

            'pending_assignment' => [
                'value' => $pendingAssignment,
            ],

            'po_pending_approval' => [
                'value' => $poPendingApproval,
            ],

            'po_approved' => [
                'value' => $poApproved,
            ],

            'total_spend' => [
                'value' => round(
                    (float) $totalSpend,
                    2,
                ),
            ],

        ];
    }

    /*
    |--------------------------------------------------------------------------
    | Purchase Order Query
    |--------------------------------------------------------------------------
    */

    protected function purchaseOrderQuery(
        ?int $branchId,
        ?int $departmentId,
        Carbon $from,
        Carbon $to,
    ): Builder {

        $query = PurchaseOrder::query()
            ->whereBetween(
                'document_date',
                [
                    $from->toDateString(),
                    $to->toDateString(),
                ],
            );

        /*
        |--------------------------------------------------------------------------
        | Branch
        |--------------------------------------------------------------------------
        */

        if ($branchId !== null) {

            $query->where(
                'branch_id',
                $branchId,
            );
        }

        /*
        |--------------------------------------------------------------------------
        | Department
        |--------------------------------------------------------------------------
        */

        if ($departmentId !== null) {

            $query->where(
                'department_id',
                $departmentId,
            );
        }

        return $query;
    }

    /*
    |--------------------------------------------------------------------------
    | Material Requisition Query
    |--------------------------------------------------------------------------
    */

    protected function materialRequisitionQuery(
        ?int $branchId,
        ?int $departmentId,
        Carbon $from,
        Carbon $to,
    ): Builder {

        $query = PurchaseRequisition::query()
            ->whereBetween(
                'request_date',
                [
                    $from->toDateString(),
                    $to->toDateString(),
                ],
            );

        if ($branchId !== null) {

            $query->where(
                'branch_id',
                $branchId,
            );
        }

        if ($departmentId !== null) {

            $query->where(
                'department_id',
                $departmentId,
            );
        }

        return $query;
    }

    /*
    |--------------------------------------------------------------------------
    | Assignment Query
    |--------------------------------------------------------------------------
    */

    protected function assignmentQuery(
        ?int $branchId,
        ?int $departmentId,
        Carbon $from,
        Carbon $to,
    ): Builder {

        $query = AssignmentMaterialRequisition::query()
            ->whereHas(
                'purchaseRequisition',
                function (Builder $query) use (
                    $branchId,
                    $departmentId,
                    $from,
                    $to,
                ): void {

                    $query->whereBetween(
                        'request_date',
                        [
                            $from->toDateString(),
                            $to->toDateString(),
                        ],
                    );

                    if ($branchId !== null) {

                        $query->where(
                            'branch_id',
                            $branchId,
                        );
                    }

                    if ($departmentId !== null) {

                        $query->where(
                            'department_id',
                            $departmentId,
                        );
                    }
                }
            );

        return $query;
    }

    /*
    |--------------------------------------------------------------------------
    | Previous Period
    |--------------------------------------------------------------------------
    */

    protected function getPreviousPeriod(
        Carbon $from,
        Carbon $to,
    ): array {

        $days =
            $from->diffInDays(
                $to
            ) + 1;

        $previousTo =
            $from
                ->copy()
                ->subDay();

        $previousFrom =
            $previousTo
                ->copy()
                ->subDays(
                    $days - 1
                );

        return [

            'from' => $previousFrom,

            'to' => $previousTo,

        ];
    }

    /*
    |--------------------------------------------------------------------------
    | Percentage Change
    |--------------------------------------------------------------------------
    */

    protected function percentageChange(
        float $previous,
        float $current,
    ): float {

        if ($previous == 0.0) {

            return $current == 0.0
                ? 0.0
                : 100.0;
        }

        return round(
            (
                ($current - $previous)
                / $previous
            ) * 100,
            2,
        );
    }
}