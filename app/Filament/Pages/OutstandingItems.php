<?php

declare(strict_types=1);

namespace App\Filament\Pages;

use App\Models\AssignmentDirectMarket;
use App\Models\AssignmentDirectMarketItem;
use App\Models\Branch;
use App\Models\Department;
use App\Models\GoodsReceiptItem;
use App\Models\PurchaseOrder;
use App\Models\PurchaseOrderItem;
use App\Models\Supplier;
use Filament\Pages\Page;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;
use Livewire\WithPagination;

class OutstandingItems extends Page
{
    use WithPagination;

    protected string $view = 'filament.pages.outstanding-items';

    protected static ?string $navigationLabel = 'Outstanding Items';

    protected static string|\UnitEnum|null $navigationGroup = 'Logistics';

    protected static \BackedEnum|string|null $navigationIcon = 'heroicon-o-clipboard-document-list';

    protected static ?int $navigationSort = 20;

    public string $sourceType = 'ALL';

    public ?int $branchFilter = null;

    public ?int $departmentFilter = null;

    public ?int $supplierFilter = null;

    public ?string $dateFrom = null;

    public ?string $dateTo = null;

    public string $search = '';

    public string $perPage = '10';

    protected string $paginationPageName = 'page';

    public function mount(): void
    {
        $this->dateFrom = null;
        $this->dateTo = null;
    }

    public function updatedSourceType(): void
    {
        $this->resetPage();
    }

    public function updatedBranchFilter(): void
    {
        $this->resetPage();
    }

    public function updatedDepartmentFilter(): void
    {
        $this->resetPage();
    }

    public function updatedSupplierFilter(): void
    {
        $this->resetPage();
    }

    public function updatedDateFrom(): void
    {
        $this->resetPage();
    }

    public function updatedDateTo(): void
    {
        $this->resetPage();
    }

    public function updatedSearch(): void
    {
        $this->resetPage();
    }

    public function updatedPerPage(): void
    {
        if (! in_array($this->perPage, ['10', '25', '50', '100', 'all'], true)) {
            $this->perPage = '10';
        }

        $this->resetPage();
    }

    public function resetFilters(): void
    {
        $this->sourceType = 'ALL';
        $this->branchFilter = null;
        $this->departmentFilter = null;
        $this->supplierFilter = null;
        $this->dateFrom = null;
        $this->dateTo = null;
        $this->search = '';
        $this->perPage = '10';

        $this->resetPage();
    }

    public function hasActiveFilters(): bool
    {
        return $this->sourceType !== 'ALL'
            || $this->branchFilter !== null
            || $this->departmentFilter !== null
            || $this->supplierFilter !== null
            || filled($this->dateFrom)
            || filled($this->dateTo)
            || trim($this->search) !== '';
    }

    public function getBranchOptionsProperty(): Collection
    {
        return Branch::query()
            ->orderBy('branch_name')
            ->pluck('branch_name', 'id');
    }

    public function getDepartmentOptionsProperty(): Collection
    {
        return Department::query()
            ->orderByRaw('LOWER(TRIM(department_name)) ASC')
            ->orderBy('id')
            ->pluck('department_name', 'id');
    }

    public function getSupplierOptionsProperty(): Collection
    {
        return Supplier::query()
            ->orderBy('supplier_name')
            ->pluck('supplier_name', 'id');
    }

    protected function getViewData(): array
    {
        $allRows = $this->getOutstandingRows();

        $totalLines = $allRows->count();

        $totalOutstanding = $allRows->sum('outstanding_qty');

        $totalDocuments = $allRows
            ->map(fn (array $row) => $row['source_type'] . '|' . $row['source_no'])
            ->unique()
            ->count();

        $perPage = $this->perPage === 'all'
            ? max($totalLines, 1)
            : (int) $this->perPage;

        $currentPage = $this->getPage();

        $rows = new LengthAwarePaginator(
            $allRows->forPage($currentPage, $perPage)->values(),
            $totalLines,
            $perPage,
            $currentPage,
            [
                'path' => request()->url(),
                'pageName' => $this->paginationPageName,
            ]
        );

        return [
            'rows' => $rows,
            'totalLines' => $totalLines,
            'totalOutstanding' => $totalOutstanding,
            'totalDocuments' => $totalDocuments,
        ];
    }

    protected function getOutstandingRows(): Collection
    {
        $rows = collect();

        $submittedStatuses = ['RECEIVED', 'CLOSE'];

        /*
         * Draft allocations temporarily reserve the source item.
         * They do not count as received quantities.
         */
        $poDraftItemIds = GoodsReceiptItem::query()
            ->whereNotNull('purchase_order_item_id')
            ->where('allocated_qty', '>', 0)
            ->whereHas('goodsReceipt', fn (Builder $query) =>
                $query->where('rr_status', 'DRAFT')
            )
            ->pluck('purchase_order_item_id')
            ->unique();

        $dmDraftItemIds = GoodsReceiptItem::query()
            ->whereNotNull('assignment_direct_market_item_id')
            ->where('allocated_qty', '>', 0)
            ->whereHas('goodsReceipt', fn (Builder $query) =>
                $query->where('rr_status', 'DRAFT')
            )
            ->pluck('assignment_direct_market_item_id')
            ->unique();

        /*
         * PURCHASE ORDER ITEMS
         */
        if (in_array($this->sourceType, ['ALL', 'PO'], true)) {
            $poQuery = PurchaseOrderItem::query()
                ->with(['purchaseOrder.supplier'])
                ->whereHas('purchaseOrder', function (Builder $query) {
                    $query->where(
                        'approval_status',
                        PurchaseOrder::APPROVAL_APPROVED
                    );

                    if ($this->branchFilter !== null) {
                        $query->where('branch_id', $this->branchFilter);
                    }

                    if ($this->departmentFilter !== null) {
                        $query->where('department_id', $this->departmentFilter);
                    }

                    if ($this->supplierFilter !== null) {
                        $query->where('supplier_id', $this->supplierFilter);
                    }

                    if (filled($this->dateFrom)) {
                        $query->whereDate('document_date', '>=', $this->dateFrom);
                    }

                    if (filled($this->dateTo)) {
                        $query->whereDate('document_date', '<=', $this->dateTo);
                    }
                })
                ->whereNotIn('id', $poDraftItemIds)
                ->orderBy('id');

            foreach ($poQuery->get() as $item) {
                $received = (float) GoodsReceiptItem::query()
                    ->where('purchase_order_item_id', $item->getKey())
                    ->whereHas('goodsReceipt', fn (Builder $query) =>
                        $query->whereIn('rr_status', $submittedStatuses)
                    )
                    ->sum('received_qty');

                $sourceQty = (float) $item->ordered_qty;

                $outstanding = max(0, round($sourceQty - $received, 4));

                if ($outstanding <= 0) {
                    continue;
                }

                $supplier = $item->purchaseOrder?->supplier;

                $rows->push([
                    'source_type' => 'PO',
                    'supplier_id' => $item->purchaseOrder?->supplier_id,
                    'source_id' => $item->purchaseOrder?->getKey(),
                    'source_no' => $item->purchaseOrder?->document_no ?? '-',
                    'source_date' => $item->purchaseOrder?->document_date,
                    'supplier' => $this->formatSupplier($supplier),
                    'item_code' => $item->item_code ?? '-',
                    'item_name' => $item->item_name ?? '-',
                    'uom' => $item->uom_code ?? $item->uom_name ?? '-',
                    'source_qty' => $sourceQty,
                    'received_qty' => $received,
                    'outstanding_qty' => $outstanding,
                ]);
            }
        }

        /*
         * DIRECT MARKET ITEMS
         */
        if (in_array($this->sourceType, ['ALL', 'DM'], true)) {
            $dmQuery = AssignmentDirectMarketItem::query()
                ->with(['assignmentDirectMarket.directMarket', 'supplier'])
                ->whereHas('assignmentDirectMarket', function (Builder $query) {
                    $query->where(
                        'status',
                        AssignmentDirectMarket::STATUS_APPROVED
                    );

                    if ($this->branchFilter !== null) {
                        $query->where('branch_id', $this->branchFilter);
                    }

                    if ($this->departmentFilter !== null) {
                        $query->where('department_id', $this->departmentFilter);
                    }

                    if (filled($this->dateFrom)) {
                        $query->whereDate('document_date', '>=', $this->dateFrom);
                    }

                    if (filled($this->dateTo)) {
                        $query->whereDate('document_date', '<=', $this->dateTo);
                    }
                });

            if ($this->supplierFilter !== null) {
                $dmQuery->where('supplier_id', $this->supplierFilter);
            }

            $dmItems = $dmQuery
                ->whereNotIn('id', $dmDraftItemIds)
                ->orderBy('id')
                ->get();

            foreach ($dmItems as $item) {
                $received = (float) GoodsReceiptItem::query()
                    ->where('assignment_direct_market_item_id', $item->getKey())
                    ->whereHas('goodsReceipt', fn (Builder $query) =>
                        $query->whereIn('rr_status', $submittedStatuses)
                    )
                    ->sum('received_qty');

                $sourceQty = (float) $item->assigned_qty;

                $outstanding = max(0, round($sourceQty - $received, 4));

                if ($outstanding <= 0) {
                    continue;
                }

                $rows->push([
                    'source_type' => 'DM',
                    'source_id' => $item->assignmentDirectMarket?->getKey(),
                    'supplier_id' => $item->supplier_id,
                    'source_no' => $item->assignmentDirectMarket?->directMarket?->dm_no ?? '-',
                    'source_date' => $item->assignmentDirectMarket?->document_date,
                    'supplier' => $this->formatSupplier($item->supplier),
                    'item_code' => $item->item_code ?? '-',
                    'item_name' => $item->item_name ?? '-',
                    'uom' => $item->uom_code ?? $item->uom_name ?? '-',
                    'source_qty' => $sourceQty,
                    'received_qty' => $received,
                    'outstanding_qty' => $outstanding,
                ]);
            }
        }

        /*
         * SEARCH
         */
        if (trim($this->search) !== '') {
            $needle = mb_strtolower(trim($this->search));

            $rows = $rows->filter(function (array $row) use ($needle) {
                return str_contains(mb_strtolower($row['source_no']), $needle)
                    || str_contains(mb_strtolower($row['item_code']), $needle)
                    || str_contains(mb_strtolower($row['item_name']), $needle)
                    || str_contains(mb_strtolower($row['supplier']), $needle);
            });
        }

        return $rows
            ->sortBy([
                ['source_type', 'asc'],
                ['source_no', 'asc'],
                ['item_code', 'asc'],
            ])
            ->values();
    }

    private function formatSupplier(?Supplier $supplier): string
    {
        if (! $supplier) {
            return '-';
        }

        return collect([
            $supplier->supplier_code,
            $supplier->supplier_name ?? $supplier->name,
        ])
            ->filter(fn ($value) => filled($value))
            ->implode(' - ') ?: '-';
    }
}