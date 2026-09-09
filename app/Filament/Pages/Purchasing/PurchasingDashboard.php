<?php

declare(strict_types=1);

namespace App\Filament\Pages\Purchasing;

use BackedEnum;
use UnitEnum;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Carbon;

use Filament\Pages\Page;

use App\Models\Branch;
use App\Models\Department;
use App\Models\PurchaseRequisition;
use App\Models\AssignmentMaterialRequisition;
use App\Models\PurchaseOrder;
use App\Models\GoodsReceipt;
use App\Models\ApprovalTransaction;

class PurchasingDashboard extends Page
{
    /**
     * Navigation
     */
    protected static ?string $navigationLabel = 'Dashboard Purchasing';

    protected static string|UnitEnum|null $navigationGroup = 'Purchasing';

    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-chart-pie';

    protected static ?int $navigationSort = 1;

    protected static ?string $slug = 'purchasing/dashboard';

    /**
     * Remove Filament Default Heading
     */
    protected static ?string $title = '';

    protected string $view = 'filament.pages.purchasing.purchasing-dashboard';

    /*
    |--------------------------------------------------------------------------
    | Dashboard Filters
    |--------------------------------------------------------------------------
    */

    public ?int $branchFilter = null;

    public ?int $departmentFilter = null;

    public string $dateFrom;

    public string $dateTo;    

    public function mount(): void
    {
        $this->dateFrom = now()
            ->startOfMonth()
            ->toDateString();

        $this->dateTo = now()
            ->endOfMonth()
            ->toDateString();
    }

    /*
    |--------------------------------------------------------------------------
    | Dashboard Date Filter
    |--------------------------------------------------------------------------
    */

    protected function applyDashboardDateFilter($query, string $dateColumn)
    {
        return $query
            ->whereDate(
                $dateColumn,
                '>=',
                $this->dateFrom
            )
            ->whereDate(
                $dateColumn,
                '<=',
                $this->dateTo
            );
    }

    /*
    |--------------------------------------------------------------------------
    | Material Requisition Analytics
    |--------------------------------------------------------------------------
    */

    public function getMaterialRequisitionQueryProperty()
    {
        $query = PurchaseRequisition::query();

        if ($this->branchFilter !== null) {
            $query->where(
                'branch_id',
                $this->branchFilter
            );
        }

        if ($this->departmentFilter !== null) {
            $query->where(
                'department_id',
                $this->departmentFilter
            );
        }

        return $this->applyDashboardDateFilter(
            $query,
            'request_date'
        );
    }

    public function getMaterialRequisitionCountProperty(): int
    {
        return $this->materialRequisitionQuery->count();
    }

    /*
    |--------------------------------------------------------------------------
    | Assignment Material Requisition Analytics
    |--------------------------------------------------------------------------
    */

    public function getAssignmentMaterialRequisitionQueryProperty()
    {
        $query = AssignmentMaterialRequisition::query();

        if ($this->branchFilter !== null) {
            $query->where(
                'branch_id',
                $this->branchFilter
            );
        }

        if ($this->departmentFilter !== null) {
            $query->where(
                'department_id',
                $this->departmentFilter
            );
        }

        return $this->applyDashboardDateFilter(
            $query,
            'document_date'
        );
    }

    public function getAssignmentMaterialRequisitionCountProperty(): int
    {
        return $this->assignmentMaterialRequisitionQuery->count();
    }


    /*
    |--------------------------------------------------------------------------
    | Purchase Order Analytics
    |--------------------------------------------------------------------------
    */

    public function getPurchaseOrderQueryProperty()
    {
        $query = PurchaseOrder::query();

        if ($this->branchFilter !== null) {
            $query->where(
                'branch_id',
                $this->branchFilter
            );
        }

        if ($this->departmentFilter !== null) {
            $query->where(
                'department_id',
                $this->departmentFilter
            );
        }

        return $this->applyDashboardDateFilter(
            $query,
            'document_date'
        );
    }

    public function getPurchaseOrderCountProperty(): int
    {
        return $this->purchaseOrderQuery->count();
    }


    public function getPurchaseOrderSpendProperty(): float
    {
        return (float) $this->purchaseOrderQuery
            ->where('approval_status', 'Approved')
            ->sum('grand_total');
    }



    /*
    |--------------------------------------------------------------------------
    | Approved Purchase Order Analytics Base Query
    |--------------------------------------------------------------------------
    |
    | Single source of truth for purchasing-value analytics.
    | Only Approved Purchase Orders are included.
    |
    */

    protected function getFilteredApprovedPurchaseOrderQuery()
    {
        return $this->purchaseOrderQuery
            ->where(
                'approval_status',
                'Approved'
            );
    }

    /**
     * Purchase Value Trend
     *
     * Annual purchasing trend.
     * IMPORTANT:
     * - Does NOT follow dashboard From / To filter.
     * - Always shows Jan-Dec of current year.
     * - Uses purchase_orders.grand_total.
     */
    public function getPurchaseValueTrendProperty(): array
    {
        $year = now()->year;

        $query = PurchaseOrder::query()
            ->whereYear('purchase_orders.document_date', $year)
            ->where(
                'purchase_orders.approval_status',
                'Approved'
            );

        /*
        |--------------------------------------------------------------------------
        | Branch Filter
        |--------------------------------------------------------------------------
        */

        if ($this->branchFilter !== null) {

            $query->where(
                'purchase_orders.branch_id',
                $this->branchFilter
            );
        }

        /*
        |--------------------------------------------------------------------------
        | Department Filter
        |--------------------------------------------------------------------------
        */

        if ($this->departmentFilter !== null) {

            $query->where(
                'purchase_orders.department_id',
                $this->departmentFilter
            );
        }

        $rows = $query
            ->selectRaw(
                "DATE_FORMAT(
                    purchase_orders.document_date,
                    '%Y-%m'
                ) as period"
            )
            ->selectRaw(
                "SUM(
                    purchase_orders.grand_total
                ) as purchase_value"
            )
            ->groupBy('period')
            ->orderBy('period')
            ->get();

        /*
        |--------------------------------------------------------------------------
        | Always return Jan-Dec
        |--------------------------------------------------------------------------
        */

        $months = collect(range(1, 12));

        return $months->map(function ($month) use ($rows, $year) {

            $period = sprintf(
                '%04d-%02d',
                $year,
                $month
            );

            $row = $rows->firstWhere(
                'period',
                $period
            );

            return [
                'period' => $period,

                'label' => Carbon::create(
                    $year,
                    $month,
                    1
                )->format('M'),

                'purchase_value' => (float) (
                    $row->purchase_value ?? 0
                ),
            ];

        })->values()->all();
    }


    /**
     * Top 5 Categories by Purchase Value
     *
     * Uses dashboard date range.
     * Uses purchase_order_items.grand_total.
     */
    public function getTopPurchaseCategoriesProperty(): array
    {
        $query = PurchaseOrder::query()
            ->join(
                'purchase_order_items',
                'purchase_order_items.purchase_order_id',
                '=',
                'purchase_orders.id'
            )
            ->join(
                'items',
                'items.id',
                '=',
                'purchase_order_items.item_id'
            )
            ->leftJoin(
                'master_categories',
                'master_categories.id',
                '=',
                'items.category_id'
            )
            ->where(
                'purchase_orders.approval_status',
                'Approved'
            );

        /*
        |--------------------------------------------------------------------------
        | Branch Filter
        |--------------------------------------------------------------------------
        */

        if ($this->branchFilter !== null) {

            $query->where(
                'purchase_orders.branch_id',
                $this->branchFilter
            );
        }

        /*
        |--------------------------------------------------------------------------
        | Department Filter
        |--------------------------------------------------------------------------
        */

        if ($this->departmentFilter !== null) {

            $query->where(
                'purchase_orders.department_id',
                $this->departmentFilter
            );
        }

        /*
        |--------------------------------------------------------------------------
        | Dashboard Date Range
        |--------------------------------------------------------------------------
        */

        $query = $this->applyDashboardDateFilter(
            $query,
            'purchase_orders.document_date'
        );

        $rows = $query
            ->selectRaw(
                "COALESCE(
                    master_categories.category_name,
                    'Uncategorized'
                ) as category_name"
            )
            ->selectRaw(
                "SUM(
                    purchase_order_items.grand_total
                ) as purchase_value"
            )
            ->groupBy(
                'master_categories.id',
                'master_categories.category_name'
            )
            ->orderByDesc('purchase_value')
            ->limit(5)
            ->get();

        $total = (float) $rows->sum(
            'purchase_value'
        );

        return $rows->map(function ($row) use ($total) {

            $value = (float) $row->purchase_value;

            return [
                'category' => $row->category_name,

                'purchase_value' => $value,

                'percentage' => $total > 0
                    ? round(
                        ($value / $total) * 100,
                        1
                    )
                    : 0,
            ];

        })->values()->all();
    }


    /*
    |--------------------------------------------------------------------------
    | Top 5 Suppliers Analytics
    |--------------------------------------------------------------------------
    |
    | Ranked by approved purchase value.
    | Uses dashboard date range.
    |
    | Metrics:
    | - Supplier
    | - PO Count
    | - Purchase Value
    | - On-Time Delivery
    |
    */

    public function getTopPurchaseSuppliersProperty(): array
    {
        /*
        |--------------------------------------------------------------------------
        | Base Supplier Purchase Query
        |--------------------------------------------------------------------------
        */

        $query = PurchaseOrder::query()
            ->join(
                'suppliers',
                'suppliers.id',
                '=',
                'purchase_orders.supplier_id'
            )
            ->where(
                'purchase_orders.approval_status',
                'Approved'
            );

        /*
        |--------------------------------------------------------------------------
        | Branch Filter
        |--------------------------------------------------------------------------
        */

        if ($this->branchFilter !== null) {

            $query->where(
                'purchase_orders.branch_id',
                $this->branchFilter
            );
        }

        /*
        |--------------------------------------------------------------------------
        | Department Filter
        |--------------------------------------------------------------------------
        */

        if ($this->departmentFilter !== null) {

            $query->where(
                'purchase_orders.department_id',
                $this->departmentFilter
            );
        }

        /*
        |--------------------------------------------------------------------------
        | Dashboard Date Filter
        |--------------------------------------------------------------------------
        */

        $query = $this->applyDashboardDateFilter(
            $query,
            'purchase_orders.document_date'
        );

        /*
        |--------------------------------------------------------------------------
        | Top 5 Suppliers
        |--------------------------------------------------------------------------
        */

        $rows = $query
            ->select([
                'purchase_orders.supplier_id',
                'suppliers.supplier_name',
            ])
            ->selectRaw(
                'COUNT(DISTINCT purchase_orders.id) as po_count'
            )
            ->selectRaw(
                'SUM(purchase_orders.grand_total) as purchase_value'
            )
            ->groupBy(
                'purchase_orders.supplier_id',
                'suppliers.supplier_name'
            )
            ->orderByDesc('purchase_value')
            ->limit(5)
            ->get();

        /*
        |--------------------------------------------------------------------------
        | No Supplier Data
        |--------------------------------------------------------------------------
        */

        if ($rows->isEmpty()) {
            return [];
        }

        /*
        |--------------------------------------------------------------------------
        | Supplier IDs
        |--------------------------------------------------------------------------
        */

        $supplierIds = $rows
            ->pluck('supplier_id')
            ->map(fn ($id) => (int) $id)
            ->values()
            ->all();

        /*
        |--------------------------------------------------------------------------
        | Actual Receipt Date per PO Item
        |--------------------------------------------------------------------------
        |
        | If an item has multiple GR records,
        | the latest receipt date represents completion.
        |
        */

        $receiptSubQuery = DB::table(
            'goods_receipt_items as gri'
        )
            ->join(
                'goods_receipts as gr',
                'gr.id',
                '=',
                'gri.goods_receipt_id'
            )
            ->whereNull('gri.deleted_at')
            ->whereNull('gr.deleted_at')
            ->select(
                'gri.purchase_order_item_id'
            )
            ->selectRaw(
                'MAX(gr.receipt_date) as actual_receipt_date'
            )
            ->groupBy(
                'gri.purchase_order_item_id'
            );

        /*
        |--------------------------------------------------------------------------
        | On-Time Delivery per Supplier
        |--------------------------------------------------------------------------
        */

        $otdRows = DB::table(
            'purchase_order_items as poi'
        )
            ->join(
                'purchase_orders as po',
                'po.id',
                '=',
                'poi.purchase_order_id'
            )
            ->leftJoinSub(
                $receiptSubQuery,
                'receipt_dates',
                function ($join) {
                    $join->on(
                        'receipt_dates.purchase_order_item_id',
                        '=',
                        'poi.id'
                    );
                }
            )
            ->where(
                'po.approval_status',
                'Approved'
            )
            ->whereIn(
                'po.supplier_id',
                $supplierIds
            )
            ->whereNotNull(
                'poi.delivery_date'
            )
            ->whereNotNull(
                'receipt_dates.actual_receipt_date'
            )
            ->select(
                'po.supplier_id'
            )
            ->selectRaw(
                'COUNT(*) as delivered_items'
            )
            ->selectRaw(
                'SUM(
                    CASE
                        WHEN receipt_dates.actual_receipt_date
                             <= poi.delivery_date
                        THEN 1
                        ELSE 0
                    END
                ) as on_time_items'
            )
            ->groupBy(
                'po.supplier_id'
            )
            ->get()
            ->keyBy('supplier_id');

        /*
        |--------------------------------------------------------------------------
        | Final Dataset
        |--------------------------------------------------------------------------
        */

        return $rows->map(function ($row) use ($otdRows) {

            $supplierId = (int) $row->supplier_id;

            $otd = $otdRows->get(
                $supplierId
            );

            $deliveredItems = $otd
                ? (int) $otd->delivered_items
                : 0;

            $onTimeItems = $otd
                ? (int) $otd->on_time_items
                : 0;

            $onTimePercentage = $deliveredItems > 0
                ? round(
                    ($onTimeItems / $deliveredItems) * 100
                )
                : null;

            return [
                'supplier_id' => $supplierId,

                'supplier_name' =>
                    (string) $row->supplier_name,

                'po_count' =>
                    (int) $row->po_count,

                'purchase_value' =>
                    (float) $row->purchase_value,

                'on_time_delivery' =>
                    $onTimePercentage,

                'delivered_items' =>
                    $deliveredItems,

                'on_time_items' =>
                    $onTimeItems,
            ];

        })->values()->all();
    }

    /*
    |--------------------------------------------------------------------------
    | Spend by Department Analytics
    |--------------------------------------------------------------------------
    |
    | Approved purchase value grouped by department.
    | Uses dashboard date range.
    |
    */

    public function getSpendByDepartmentProperty(): array
    {
        $query = PurchaseOrder::query()
            ->join(
                'departments',
                'departments.id',
                '=',
                'purchase_orders.department_id'
            )
            ->where(
                'purchase_orders.approval_status',
                'Approved'
            );

        /*
        |--------------------------------------------------------------------------
        | Branch Filter
        |--------------------------------------------------------------------------
        */

        if ($this->branchFilter !== null) {

            $query->where(
                'purchase_orders.branch_id',
                $this->branchFilter
            );
        }

        /*
        |--------------------------------------------------------------------------
        | Department Filter
        |--------------------------------------------------------------------------
        */

        if ($this->departmentFilter !== null) {

            $query->where(
                'purchase_orders.department_id',
                $this->departmentFilter
            );
        }

        /*
        |--------------------------------------------------------------------------
        | Dashboard Date Filter
        |--------------------------------------------------------------------------
        */

        $query = $this->applyDashboardDateFilter(
            $query,
            'purchase_orders.document_date'
        );

        /*
        |--------------------------------------------------------------------------
        | Aggregate
        |--------------------------------------------------------------------------
        */

        $rows = $query
            ->select([
                'purchase_orders.department_id',
                'departments.department_name',
            ])
            ->selectRaw(
                'SUM(purchase_orders.grand_total) as purchase_value'
            )
            ->groupBy(
                'purchase_orders.department_id',
                'departments.department_name'
            )
            ->orderByDesc('purchase_value')
            ->limit(5)
            ->get();

        /*
        |--------------------------------------------------------------------------
        | Total
        |--------------------------------------------------------------------------
        */

        $total = (float) $rows->sum(
            'purchase_value'
        );

        /*
        |--------------------------------------------------------------------------
        | Final Dataset
        |--------------------------------------------------------------------------
        */

        return $rows->map(function ($row) use ($total) {

            $value = (float) $row->purchase_value;

            return [
                'department_id' =>
                    (int) $row->department_id,

                'department_name' =>
                    (string) $row->department_name,

                'purchase_value' =>
                    $value,

                'percentage' =>
                    $total > 0
                        ? round(
                            ($value / $total) * 100,
                            1
                        )
                        : 0,
            ];

        })->values()->all();
    }


    /*
    |--------------------------------------------------------------------------
    | Goods Receipt Analytics
    |--------------------------------------------------------------------------
    */

    public function getGoodsReceiptQueryProperty()
    {
        $query = GoodsReceipt::query()
            ->whereHas(
                'purchaseOrder',
                function ($poQuery) {

                    if ($this->branchFilter !== null) {
                        $poQuery->where(
                            'branch_id',
                            $this->branchFilter
                        );
                    }

                    if ($this->departmentFilter !== null) {
                        $poQuery->where(
                            'department_id',
                            $this->departmentFilter
                        );
                    }
                }
            );

        return $this->applyDashboardDateFilter(
            $query,
            'receipt_date'
        );
    }

    public function getGoodsReceiptCountProperty(): int
    {
        return $this->goodsReceiptQuery->count();
    }

    public function getPendingMaterialRequisitionApprovalCountProperty(): int
    {
        return $this->materialRequisitionQuery
            ->where(
                'status',
                PurchaseRequisition::STATUS_WAITING_APPROVAL
            )
            ->count();
    }

    public function getOpenPurchaseOrderCountProperty(): int
    {
        $query = PurchaseOrder::query();

        if ($this->branchFilter !== null) {
            $query->where(
                'branch_id',
                $this->branchFilter
            );
        }

        if ($this->departmentFilter !== null) {
            $query->where(
                'department_id',
                $this->departmentFilter
            );
        }

        $query = $this->applyDashboardDateFilter(
            $query,
            'document_date'
        );

        return $query
            ->where(
                'approval_status',
                'Approved'
            )
            ->whereNotIn(
                'status',
                [
                    PurchaseOrder::STATUS_COMPLETED,
                    PurchaseOrder::STATUS_CANCELLED,
                ]
            )
            ->count();
    }

    /*
    |--------------------------------------------------------------------------
    | Approval Analytics
    |--------------------------------------------------------------------------
    */

    public function getPendingApprovalCountProperty(): int
    {
        $query = ApprovalTransaction::query()
            ->where('status', 'PENDING')
            ->whereIn('document_type', [
                'MATERIAL_REQUISITION',
                'PURCHASE_ORDER',
            ]);

        /*
        |--------------------------------------------------------------------------
        | Date Range
        |--------------------------------------------------------------------------
        */

        $query->whereDate(
            'submitted_at',
            '>=',
            $this->dateFrom
        );

        $query->whereDate(
            'submitted_at',
            '<=',
            $this->dateTo
        );

        /*
        |--------------------------------------------------------------------------
        | Branch / Department
        |--------------------------------------------------------------------------
        */

        if (
            $this->branchFilter !== null
            || $this->departmentFilter !== null
        ) {
            $query->where(function ($approvalQuery) {

                /*
                |--------------------------------------------------------------------------
                | Material Requisition
                |--------------------------------------------------------------------------
                */

                $approvalQuery->where(function ($query) {

                    $query->where(
                        'document_type',
                        'MATERIAL_REQUISITION'
                    );

                    if ($this->branchFilter !== null) {
                        $query->whereIn(
                            'document_id',
                            PurchaseRequisition::query()
                                ->select('id')
                                ->where(
                                    'branch_id',
                                    $this->branchFilter
                                )
                        );
                    }

                    if ($this->departmentFilter !== null) {
                        $query->whereIn(
                            'document_id',
                            PurchaseRequisition::query()
                                ->select('id')
                                ->where(
                                    'department_id',
                                    $this->departmentFilter
                                )
                        );
                    }
                });

                /*
                |--------------------------------------------------------------------------
                | Purchase Order
                |--------------------------------------------------------------------------
                */

                $approvalQuery->orWhere(function ($query) {

                    $query->where(
                        'document_type',
                        'PURCHASE_ORDER'
                    );

                    if ($this->branchFilter !== null) {
                        $query->whereIn(
                            'document_id',
                            PurchaseOrder::query()
                                ->select('id')
                                ->where(
                                    'branch_id',
                                    $this->branchFilter
                                )
                        );
                    }

                    if ($this->departmentFilter !== null) {
                        $query->whereIn(
                            'document_id',
                            PurchaseOrder::query()
                                ->select('id')
                                ->where(
                                    'department_id',
                                    $this->departmentFilter
                                )
                        );
                    }
                });
            });
        }

        return $query->count();
    }


    public function getBranchOptionsProperty()
    {
        return Branch::query()
            ->orderBy('branch_name')
            ->pluck('branch_name', 'id');
    }

    public function getDepartmentOptionsProperty()
    {
        return Department::query()
            ->orderBy('department_name')
            ->pluck('department_name', 'id');
    }


    public function getHeading(): string
    {
        return '';
    }

    public function getSubheading(): ?string
    {
        return null;
    }
}