<?php

declare(strict_types=1);

namespace App\Filament\Resources\GeneratePurchaseOrderResource\Schemas;

use App\Models\AssignmentMaterialRequisition;
use App\Models\ApprovalTransaction;
use App\Services\Purchasing\GeneratePurchaseOrderEligibilityService;

use Filament\Forms\Components\Placeholder;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Illuminate\Support\HtmlString;

class GeneratePurchaseOrderForm
{
    public static function configure(
        Schema $schema
    ): Schema {

        return $schema
            ->columns(1)

            ->components([

                /*
                |--------------------------------------------------------------------------
                | DOCUMENT INFORMATION
                |--------------------------------------------------------------------------
                */

                Section::make(
                    'Purchase Order Generation'
                )
                    ->description(
                        'Review the Assignment Material Requisition, approval workflow, supplier assignment, and eligibility before generating the Purchase Order.'
                    )
                    ->schema([

                        Grid::make(12)
                            ->schema([

                                Placeholder::make(
                                    'amr_number'
                                )
                                    ->label('AMR No.')
                                    ->content(
                                        fn (
                                            ?AssignmentMaterialRequisition $record
                                        ): string =>
                                            $record?->document_no
                                            ?? '-'
                                    )
                                    ->columnSpan(3),

                                Placeholder::make(
                                    'mr_number'
                                )
                                    ->label('MR No.')
                                    ->content(
                                        fn (
                                            ?AssignmentMaterialRequisition $record
                                        ): string =>
                                            $record
                                                ?->purchaseRequisition
                                                ?->pr_no
                                            ?? '-'
                                    )
                                    ->columnSpan(3),

                                Placeholder::make(
                                    'mr_status'
                                )
                                    ->label('MR Status')
                                    ->content(
                                        fn (
                                            ?AssignmentMaterialRequisition $record
                                        ): string =>
                                            $record
                                                ?->purchaseRequisition
                                                ?->status
                                            ?? '-'
                                    )
                                    ->columnSpan(3),

                                Placeholder::make(
                                    'amr_status'
                                )
                                    ->label('AMR Status')
                                    ->content(
                                        fn (
                                            ?AssignmentMaterialRequisition $record
                                        ): string =>
                                            $record?->status
                                            ?? '-'
                                    )
                                    ->columnSpan(3),

                            ]),

                    ])
                    ->collapsible(),

                /*
                |--------------------------------------------------------------------------
                | APPROVAL WORKFLOW
                |--------------------------------------------------------------------------
                */

                Section::make(
                    'Approval Workflow'
                )
                    ->description(
                        'Approval levels are read directly from the Approval Transaction snapshot. Level 2 and subsequent levels are activated by the AMR submission workflow.'
                    )
                    ->schema([

                        Placeholder::make(
                            'approval_timeline'
                        )
                            ->label('')
                            ->content(
                                fn (
                                    ?AssignmentMaterialRequisition $record
                                ): HtmlString =>

                                    self::renderApprovalTimeline(
                                        $record
                                    )
                            ),

                    ]),

                /*
                |--------------------------------------------------------------------------
                | SUPPLIER
                |--------------------------------------------------------------------------
                */

                Section::make(
                    'Supplier Assignment'
                )
                    ->schema([

                        Placeholder::make(
                            'supplier'
                        )
                            ->label('Supplier')
                            ->content(
                                fn (
                                    ?AssignmentMaterialRequisition $record
                                ): string =>
                                    self::resolveSupplierName(
                                        $record
                                    )
                            ),

                    ]),

                /*
                |--------------------------------------------------------------------------
                | ASSIGNMENT ITEMS
                |--------------------------------------------------------------------------
                */

                Section::make(
                    'Assignment Items'
                )
                    ->description(
                        'Read-only snapshot from the Assignment Material Requisition.'
                    )
                    ->schema([

                        Placeholder::make(
                            'assignment_items'
                        )
                            ->label('')
                            ->content(
                                fn (
                                    ?AssignmentMaterialRequisition $record
                                ): HtmlString =>
                                    self::renderItems(
                                        $record
                                    )
                            ),

                    ]),

                /*
                |--------------------------------------------------------------------------
                | ELIGIBILITY
                |--------------------------------------------------------------------------
                */

                Section::make(
                    'Generate PO Eligibility'
                )
                    ->description(
                        'Purchase Order generation is controlled by the server-side eligibility gate.'
                    )
                    ->schema([

                        Placeholder::make(
                            'eligibility'
                        )
                            ->label('')
                            ->content(
                                fn (
                                    ?AssignmentMaterialRequisition $record
                                ): HtmlString =>
                                    self::renderEligibility(
                                        $record
                                    )
                            ),

                    ]),

            ]);
    }

    /*
    |--------------------------------------------------------------------------
    | Approval Transaction
    |--------------------------------------------------------------------------
    */

    protected static function getApprovalTransaction(
        ?AssignmentMaterialRequisition $record
    ): ?ApprovalTransaction {

        if (! $record) {
            return null;
        }

        return ApprovalTransaction::query()
            ->with([
                'approvalMaster.steps.role',
                'steps',
            ])
            ->where(
                'document_type',
                'MATERIAL_REQUISITION'
            )
            ->where(
                'document_id',
                $record->purchase_requisition_id
            )
            ->latest('id')
            ->first();
    }

    /*
    |--------------------------------------------------------------------------
    | Approval Timeline
    |--------------------------------------------------------------------------
    */

    protected static function renderApprovalTimeline(
        ?AssignmentMaterialRequisition $record
    ): HtmlString {

        $transaction =
            self::getApprovalTransaction(
                $record
            );

        if (! $transaction) {

            return new HtmlString(
                '<div class="text-sm text-gray-500">
                    No approval transaction found.
                </div>'
            );
        }

        $masterSteps = $transaction
            ->approvalMaster
            ?->steps
            ?->sortBy('approval_level')
            ?? collect();

        $transactionSteps = $transaction
            ->steps
            ->keyBy('approval_level');

        $rows = '';

        foreach ($masterSteps as $masterStep) {

            $level = (int) $masterStep->approval_level;

            $step = $transactionSteps->get($level);

            if ($step) {

                $status = strtoupper(
                    (string) $step->status
                );

                $roleName =
                    $step->role_name
                    ?? $masterStep->role?->name
                    ?? 'Unknown';

                $statusClass =
                    self::approvalStatusClass(
                        $status
                    );

                $actedAt =
                    $step->acted_at
                    ? $step->acted_at->format(
                        'd M Y H:i'
                    )
                    : '-';

                $approvedBy =
                    $step->approved_by
                    ? optional(
                        \App\Models\User::find(
                            $step->approved_by
                        )
                    )->name
                    : '-';

                $rows .= '
                    <div class="flex items-start gap-4 py-3">
                        <div class="mt-1 h-3 w-3 rounded-full '.$statusClass['dot'].'"></div>

                        <div class="flex-1">
                            <div class="flex items-center justify-between gap-4">
                                <div>
                                    <div class="text-sm font-semibold text-gray-900 dark:text-white">
                                        Level '.$level.' — '.e($roleName).'
                                    </div>

                                    <div class="mt-1 text-xs text-gray-500">
                                        '.e($status).'
                                    </div>
                                </div>

                                <div class="rounded-full px-3 py-1 text-xs font-semibold '.$statusClass['badge'].'">
                                    '.e($status).'
                                </div>
                            </div>

                            <div class="mt-2 grid grid-cols-1 gap-1 text-xs text-gray-500 md:grid-cols-3">
                                <div>
                                    <span class="font-medium">Approved By:</span>
                                    '.e($approvedBy).'
                                </div>

                                <div>
                                    <span class="font-medium">Acted At:</span>
                                    '.e($actedAt).'
                                </div>

                                <div>
                                    <span class="font-medium">Remarks:</span>
                                    '.e($step->remarks ?? '-').'
                                </div>
                            </div>
                        </div>
                    </div>
                ';

            } else {

                /*
                |--------------------------------------------------------------------------
                | NOT ACTIVATED
                |--------------------------------------------------------------------------
                */

                $roleName =
                    $masterStep->role?->name
                    ?? 'Unknown';

                $rows .= '
                    <div class="flex items-start gap-4 py-3">
                        <div class="mt-1 h-3 w-3 rounded-full bg-gray-300"></div>

                        <div class="flex-1">
                            <div class="flex items-center justify-between gap-4">
                                <div>
                                    <div class="text-sm font-semibold text-gray-700 dark:text-gray-300">
                                        Level '.$level.' — '.e($roleName).'
                                    </div>

                                    <div class="mt-1 text-xs text-gray-500">
                                        NOT ACTIVATED
                                    </div>
                                </div>

                                <div class="rounded-full bg-gray-100 px-3 py-1 text-xs font-semibold text-gray-500 dark:bg-gray-800">
                                    NOT ACTIVATED
                                </div>
                            </div>

                            <div class="mt-2 text-xs text-gray-500">
                                This approval level will be activated by the AMR submission workflow.
                            </div>
                        </div>
                    </div>
                ';
            }
        }

        $transactionStatus =
            strtoupper(
                (string) $transaction->status
            );

        return new HtmlString(
            '
            <div class="rounded-lg border border-gray-200 dark:border-gray-700">

                <div class="border-b border-gray-200 bg-gray-50 px-4 py-3 dark:border-gray-700 dark:bg-gray-800">
                    <div class="flex items-center justify-between gap-4">

                        <div>
                            <div class="text-xs font-medium uppercase tracking-wide text-gray-500">
                                Approval Transaction
                            </div>

                            <div class="mt-1 text-sm font-semibold text-gray-900 dark:text-white">
                                '.e($transaction->document_no ?? '-').'
                            </div>
                        </div>

                        <div class="rounded-full bg-gray-100 px-3 py-1 text-xs font-semibold text-gray-700 dark:bg-gray-700 dark:text-gray-200">
                            '.e($transactionStatus).'
                        </div>

                    </div>
                </div>

                <div class="divide-y divide-gray-100 px-4 dark:divide-gray-800">
                    '.$rows.'
                </div>

            </div>
            '
        );
    }

    /*
    |--------------------------------------------------------------------------
    | Supplier
    |--------------------------------------------------------------------------
    */

    protected static function resolveSupplierName(
        ?AssignmentMaterialRequisition $record
    ): string {

        if (! $record) {
            return '-';
        }

    $suppliers = $record
        ->items
        ->loadMissing('supplier')
        ->pluck('supplier.supplier_name')
        ->filter()
        ->unique()
        ->values();

        if ($suppliers->count() === 1) {
            return (string) $suppliers->first();
        }

        if ($suppliers->count() > 1) {
            return $suppliers->implode(', ');
        }

        return '-';
    }

    /*
    |--------------------------------------------------------------------------
    | Items
    |--------------------------------------------------------------------------
    */

    protected static function renderItems(
        ?AssignmentMaterialRequisition $record
    ): HtmlString {

        if (! $record) {
            return new HtmlString(
                '<div class="text-sm text-gray-500">
                    No assignment found.
                </div>'
            );
        }

        $items = $record->items;

        if ($items->isEmpty()) {

            return new HtmlString(
                '<div class="rounded-lg border border-dashed border-gray-300 px-4 py-6 text-center text-sm text-gray-500">
                    No assignment items found.
                </div>'
            );
        }

        $rows = '';

        foreach ($items as $item) {

            /*
            |--------------------------------------------------------------------------
            | Description Source
            |--------------------------------------------------------------------------
            |
            | Primary:
            |   AMR snapshot item_description
            |
            | Fallback:
            |   MR Item -> Item Master -> description
            |
            */

            $description =
                $item->item_description
                ?? $item->purchaseRequisitionItem?->remarks
                ?? '-';

            $qty =
                number_format(
                    (float) $item->assigned_qty,
                    2
                );

            $price =
                number_format(
                    (float) $item->unit_price,
                    2
                );

            $amount =
                (float) $item->assigned_qty
                * (float) $item->unit_price;

            $amount =
                number_format(
                    $amount,
                    2
                );

            $supplierName =
                $item->supplier?->supplier_name
                ?? '-';

            $rows .= '
                <tr class="border-b border-gray-100 last:border-0 dark:border-gray-800">

                    <td class="px-3 py-3 text-sm font-medium text-gray-900 dark:text-white">
                        '.e($item->item_code ?? '-').'
                    </td>

                    <td class="px-3 py-3 text-sm text-gray-700 dark:text-gray-300">
                        '.e($item->item_name ?? '-').'
                    </td>

                    <td class="px-3 py-3 text-sm text-gray-600 dark:text-gray-400">
                        '.e($description).'
                    </td>

                    <td class="px-3 py-3 text-right text-sm text-gray-700 dark:text-gray-300">
                        '.e($qty).'
                    </td>

                    <td class="px-3 py-3 text-sm text-gray-600 dark:text-gray-400">
                        '.e($item->uom_code ?? '-').'
                    </td>

                    <td class="px-3 py-3 text-sm text-gray-700 dark:text-gray-300">
                        '.e($supplierName).'
                    </td>

                    <td class="px-3 py-3 text-right text-sm text-gray-700 dark:text-gray-300">
                        Rp '.e($price).'
                    </td>

                    <td class="px-3 py-3 text-right text-sm font-semibold text-gray-900 dark:text-white">
                        Rp '.e($amount).'
                    </td>

                </tr>
            ';
        }

        return new HtmlString(
            '
            <div class="overflow-x-auto rounded-lg border border-gray-200 dark:border-gray-700">

                <table class="w-full min-w-[1000px]">

                    <thead class="bg-gray-50 dark:bg-gray-800">

                        <tr>

                            <th class="px-3 py-3 text-left text-xs font-semibold uppercase tracking-wide text-gray-500">
                                Item
                            </th>

                            <th class="px-3 py-3 text-left text-xs font-semibold uppercase tracking-wide text-gray-500">
                                Name
                            </th>

                            <th class="px-3 py-3 text-left text-xs font-semibold uppercase tracking-wide text-gray-500">
                                Description
                            </th>

                            <th class="px-3 py-3 text-right text-xs font-semibold uppercase tracking-wide text-gray-500">
                                Assigned
                            </th>

                            <th class="px-3 py-3 text-left text-xs font-semibold uppercase tracking-wide text-gray-500">
                                UOM
                            </th>

                            <th class="px-3 py-3 text-left text-xs font-semibold uppercase tracking-wide text-gray-500">
                                Supplier
                            </th>

                            <th class="px-3 py-3 text-right text-xs font-semibold uppercase tracking-wide text-gray-500">
                                Unit Price
                            </th>

                            <th class="px-3 py-3 text-right text-xs font-semibold uppercase tracking-wide text-gray-500">
                                Amount
                            </th>

                        </tr>

                    </thead>

                    <tbody>
                        '.$rows.'
                    </tbody>

                </table>

            </div>
            '
        );
    }

    /*
    |--------------------------------------------------------------------------
    | Eligibility
    |--------------------------------------------------------------------------
    */

    protected static function renderEligibility(
        ?AssignmentMaterialRequisition $record
    ): HtmlString {

        if (! $record) {
            return new HtmlString(
                '<div class="text-sm text-gray-500">
                    Eligibility cannot be evaluated.
                </div>'
            );
        }

        $result = app(
            GeneratePurchaseOrderEligibilityService::class
        )->evaluate(
            (int) $record->getKey()
        );

        $checks = [];

        /*
        |--------------------------------------------------------------------------
        | Approval
        |--------------------------------------------------------------------------
        */

        $checks[] = [
            'label' =>
                'MR Approval',

            'passed' =>
                (bool) (
                    $result['approval']['required_steps_passed']
                    ?? false
                ),

            'detail' =>
                self::approvalEligibilityDetail(
                    $result
                ),
        ];

        /*
        |--------------------------------------------------------------------------
        | Items
        |--------------------------------------------------------------------------
        */

        $checks[] = [
            'label' =>
                'Assignment Items',

            'passed' =>
                (bool) (
                    $result['items']['passed']
                    ?? false
                ),

            'detail' =>
                implode(
                    ', ',
                    $result['items']['errors']
                    ?? []
                )
                ?: 'Items valid',
        ];

        /*
        |--------------------------------------------------------------------------
        | Supplier
        |--------------------------------------------------------------------------
        */

        $checks[] = [
            'label' =>
                'Supplier',

            'passed' =>
                (bool) (
                    $result['supplier']['passed']
                    ?? false
                ),

            'detail' =>
                implode(
                    ', ',
                    $result['supplier']['errors']
                    ?? []
                )
                ?: 'Supplier valid',
        ];

        /*
        |--------------------------------------------------------------------------
        | Existing PO
        |--------------------------------------------------------------------------
        */

        $checks[] = [
            'label' =>
                'Existing Purchase Order',

            'passed' =>
                (bool) (
                    $result['purchase_order']['passed']
                    ?? false
                ),

            'detail' =>
                ($result['purchase_order']['exists'] ?? false)
                    ? 'Purchase Order already exists'
                    : 'No Purchase Order exists',
        ];

        $rows = '';

        foreach ($checks as $check) {

            $passed = $check['passed'];

            $icon =
                $passed
                    ? '✓'
                    : '🔒';

            $badge =
                $passed
                    ? 'bg-green-50 text-green-700'
                    : 'bg-amber-50 text-amber-700';

            $rows .= '
                <div class="flex items-center justify-between gap-4 border-b border-gray-100 py-3 last:border-0 dark:border-gray-800">

                    <div class="flex items-center gap-3">

                        <div class="text-base">
                            '.$icon.'
                        </div>

                        <div>
                            <div class="text-sm font-medium text-gray-900 dark:text-white">
                                '.e($check['label']).'
                            </div>

                            <div class="mt-1 text-xs text-gray-500">
                                '.e($check['detail']).'
                            </div>
                        </div>

                    </div>

                    <div class="rounded-full px-3 py-1 text-xs font-semibold '.$badge.'">
                        '.(
                            $passed
                                ? 'PASSED'
                                : 'BLOCKED'
                        ).'
                    </div>

                </div>
            ';
        }

        $eligible =
            (bool) (
                $result['eligible']
                ?? false
            );

        return new HtmlString(
            '
            <div class="rounded-lg border border-gray-200 dark:border-gray-700">

                <div class="divide-y divide-gray-100 px-4 dark:divide-gray-800">
                    '.$rows.'
                </div>

                <div class="border-t border-gray-200 bg-gray-50 px-4 py-4 dark:border-gray-700 dark:bg-gray-800">

                    <div class="flex items-center justify-between">

                        <div>
                            <div class="text-xs font-medium uppercase tracking-wide text-gray-500">
                                Overall Eligibility
                            </div>

                            <div class="mt-1 text-base font-semibold '.(
                                $eligible
                                    ? 'text-green-600'
                                    : 'text-amber-600'
                            ).'">
                                '.(
                                    $eligible
                                        ? 'READY TO GENERATE PO'
                                        : 'NOT ELIGIBLE'
                                ).'
                            </div>
                        </div>

                        <div class="text-2xl">
                            '.(
                                $eligible
                                    ? '✓'
                                    : '🔒'
                            ).'
                        </div>

                    </div>

                </div>

            </div>
            '
        );
    }

    /*
    |--------------------------------------------------------------------------
    | Approval Eligibility Detail
    |--------------------------------------------------------------------------
    */

    protected static function approvalEligibilityDetail(
        array $result
    ): string {

        $steps =
            $result['approval']['required_steps']
            ?? [];

        if (! $steps) {
            return 'No approval step information available.';
        }

        return collect($steps)
            ->map(
                fn (array $step): string =>
                    'L'
                    .($step['approval_level'] ?? '?')
                    .' '
                    .($step['role_name'] ?? 'Unknown')
                    .' = '
                    .($step['status'] ?? 'UNKNOWN')
            )
            ->implode(' | ');
    }

    /*
    |--------------------------------------------------------------------------
    | Status Classes
    |--------------------------------------------------------------------------
    */

    protected static function approvalStatusClass(
        string $status
    ): array {

        return match ($status) {

            'APPROVED' => [
                'dot' =>
                    'bg-green-500',

                'badge' =>
                    'bg-green-50 text-green-700',
            ],

            'PENDING' => [
                'dot' =>
                    'bg-amber-500',

                'badge' =>
                    'bg-amber-50 text-amber-700',
            ],

            'REJECTED' => [
                'dot' =>
                    'bg-red-500',

                'badge' =>
                    'bg-red-50 text-red-700',
            ],

            default => [
                'dot' =>
                    'bg-gray-400',

                'badge' =>
                    'bg-gray-100 text-gray-600',
            ],

        };
    }
}