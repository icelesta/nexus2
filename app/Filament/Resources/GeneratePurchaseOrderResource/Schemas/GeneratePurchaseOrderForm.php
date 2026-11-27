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

                        Grid::make(5)
                            ->schema([

                                Placeholder::make('amr_number')
                                    ->label('AMR No.')
                                    ->content(
                                        fn (
                                            ?AssignmentMaterialRequisition $record
                                        ): HtmlString =>
                                            self::renderInfoBox(
                                                $record?->document_no
                                            )
                                    )
                                    ->columnSpan(1),

                                Placeholder::make('mr_number')
                                    ->label('MR No.')
                                    ->content(
                                        fn (
                                            ?AssignmentMaterialRequisition $record
                                        ): HtmlString =>
                                            self::renderInfoBox(
                                                $record?->purchaseRequisition?->pr_no
                                            )
                                    )
                                    ->columnSpan(1),

                                Placeholder::make('mr_status')
                                    ->label('MR Status')
                                    ->content(
                                        fn (
                                            ?AssignmentMaterialRequisition $record
                                        ): HtmlString =>
                                            self::renderInfoBox(
                                                $record?->purchaseRequisition?->status
                                            )
                                    )
                                    ->columnSpan(1),

                                Placeholder::make('amr_status')
                                    ->label('AMR Status')
                                    ->content(
                                        fn (
                                            ?AssignmentMaterialRequisition $record
                                        ): HtmlString =>
                                            self::renderInfoBox(
                                                $record?->status
                                            )
                                    )
                                    ->columnSpan(1),

                                Placeholder::make('amr_remarks')
                                    ->label('Remark')
                                    ->content(
                                        fn (
                                            ?AssignmentMaterialRequisition $record
                                        ): HtmlString =>
                                            self::renderInfoBox(
                                                $record?->remarks
                                            )
                                    )
                                    ->columnSpan(1),

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
                | SUPPORTING DOCUMENTS
                |--------------------------------------------------------------------------
                */

                Section::make('Supporting Documents')
                    ->description(
                        'Supplier quotations and other supporting documents for this Assignment Material Requisition.'
                    )
                    ->schema([

                        Placeholder::make('supporting_documents')
                            ->label('Documents')
                            ->content(
                                fn (
                                    ?AssignmentMaterialRequisition $record
                                ): HtmlString => self::renderSupportingDocuments(
                                    $record
                                )
                            ),

                    ])
                    ->collapsible(),

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
                    ? \App\Support\Timezone\UserTimezone::format(
                        $step->acted_at,
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
    | Assignment Items
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

        /*
        |--------------------------------------------------------------------------
        | Load Assignment Items
        |--------------------------------------------------------------------------
        | Follow the same relationships used by AssignmentItemsGrid.
        */

        $items = $record->items()
            ->with([
                'purchaseRequisitionItem.item',
                'purchaseRequisitionItem.uom',
                'purchaseRequisitionItem.allocationJournal',
                'supplier',
                'tax',
            ])
            ->orderBy('id')
            ->get();

        if ($items->isEmpty()) {
            return new HtmlString(
                '<div class="rounded-lg border border-dashed
                    border-gray-300 px-4 py-6 text-center
                    text-sm text-gray-500 dark:border-gray-700">
                    No assignment items found.
                </div>'
            );
        }

        /*
        |--------------------------------------------------------------------------
        | Summary Calculations
        |--------------------------------------------------------------------------
        | Use existing Assignment values.
        | No financial recalculation.
        */

        $totalLines = $items->count();

        $totalRequestedQty = (float) $items->sum(
            fn ($item) => (float) (
                $item->purchaseRequisitionItem?->quantity ?? 0
            )
        );

        $totalAssignedQty = (float) $items->sum('assigned_qty');

        $totalAmount = (float) $items->sum(
            fn ($item) => (float) $item->grand_total
        );

        /*
        |--------------------------------------------------------------------------
        | Table Rows
        |--------------------------------------------------------------------------
        */

        $rows = '';

        foreach ($items as $index => $item) {

            $purchaseItem = $item->purchaseRequisitionItem;

            $itemCode = $purchaseItem?->item?->item_code ?? '-';

            $itemName = $purchaseItem?->item?->item_name ?? '-';

            $description = $purchaseItem?->remarks ?? '-';

            $uom = $purchaseItem?->uom?->uom_name ?? '-';

            $journal = $purchaseItem?->allocationJournal?->display_name ?? '-';

            $requestedQty = number_format(
                (float) ($purchaseItem?->quantity ?? 0),
                2
            );

            $assignedQty = number_format(
                (float) $item->assigned_qty,
                2
            );

            $supplierName = $item->supplier?->supplier_name ?? '-';

            $unitPrice = number_format(
                (float) $item->unit_price,
                2
            );

            $discountPercent = number_format(
                (float) $item->discount_percent,
                2
            );

            $discountAmount = number_format(
                (float) $item->discount_amount,
                2
            );

            $taxName = $item->tax_name
                ?? $item->tax?->tax_name
                ?? '-';

            $taxPercent = number_format(
                (float) $item->tax_percent,
                2
            );

            $taxAmount = number_format(
                (float) $item->tax_amount,
                2
            );

            $grandTotal = number_format(
                (float) $item->grand_total,
                2
            );

            $rows .= '
                <tr class="transition-colors hover:bg-gray-50
                    dark:hover:bg-gray-800/50">

                    <td class="border border-gray-300 px-2 py-2
                        text-center text-xs dark:border-gray-700">
                        '.($index + 1).'
                    </td>

                    <td class="border border-gray-300 px-2 py-2
                        text-center text-xs dark:border-gray-700">
                        '.e($itemCode).'
                    </td>

                    <td class="border border-gray-300 px-2 py-2
                        text-xs whitespace-nowrap dark:border-gray-700">
                        '.e($itemName).'
                    </td>

                    <td class="border border-gray-300 px-2 py-2
                        text-xs dark:border-gray-700">
                        '.e($description).'
                    </td>

                    <td class="border border-gray-300 px-2 py-2
                        text-center text-xs dark:border-gray-700">
                        '.e($uom).'
                    </td>

                    <td class="border border-gray-300 px-2 py-2
                        text-xs whitespace-nowrap dark:border-gray-700">
                        '.e($journal).'
                    </td>

                    <td class="border border-gray-300 px-2 py-2
                        text-right text-xs dark:border-gray-700">
                        '.$requestedQty.'
                    </td>

                    <td class="border border-gray-300 px-2 py-2
                        text-right text-xs dark:border-gray-700">
                        '.$assignedQty.'
                    </td>

                    <td class="border border-gray-300 px-2 py-2
                        text-xs whitespace-nowrap dark:border-gray-700">
                        '.e($supplierName).'
                    </td>

                    <td class="border border-gray-300 px-2 py-2
                        text-right text-xs whitespace-nowrap dark:border-gray-700">
                        Rp. '.$unitPrice.'
                    </td>

                    <td class="border border-gray-300 px-2 py-2
                        text-right text-xs dark:border-gray-700">
                        '.$discountPercent.' %
                    </td>

                    <td class="border border-gray-300 px-2 py-2
                        text-right text-xs whitespace-nowrap dark:border-gray-700">
                        Rp '.$discountAmount.'
                    </td>

                    <td class="border border-gray-300 px-2 py-2
                        text-xs whitespace-nowrap dark:border-gray-700">
                        '.e($taxName).'
                    </td>

                    <td class="border border-gray-300 px-2 py-2
                        text-right text-xs dark:border-gray-700">
                        '.$taxPercent.' %
                    </td>

                    <td class="border border-gray-300 px-2 py-2
                        text-right text-xs whitespace-nowrap dark:border-gray-700">
                        Rp '.$taxAmount.'
                    </td>

                    <td class="border border-gray-300 px-2 py-2
                        text-right text-xs font-bold text-blue-700
                        whitespace-nowrap dark:border-gray-700">
                        Rp '.$grandTotal.'
                    </td>

                </tr>
            ';
        }

        /*
        |--------------------------------------------------------------------------
        | Render Table
        |--------------------------------------------------------------------------
        */

        return new HtmlString(

            '
            <div class="overflow-x-auto rounded-lg
                border border-gray-200 dark:border-gray-700">

                <table class="w-full min-w-[1650px]
                    border-collapse">

                    <thead>
                        <tr class="bg-gray-100 dark:bg-gray-800">

                            <th class="border border-gray-300 px-2 py-2
                                text-center text-xs font-semibold
                                uppercase dark:border-gray-700">
                                #
                            </th>

                            <th class="border border-gray-300 px-2 py-2
                                text-center text-xs font-semibold
                                uppercase dark:border-gray-700">
                                Item Code
                            </th>

                            <th class="border border-gray-300 px-2 py-2
                                text-left text-xs font-semibold
                                uppercase dark:border-gray-700">
                                Item Name
                            </th>

                            <th class="border border-gray-300 px-2 py-2
                                text-left text-xs font-semibold
                                uppercase dark:border-gray-700">
                                Description
                            </th>

                            <th class="border border-gray-300 px-2 py-2
                                text-center text-xs font-semibold
                                uppercase dark:border-gray-700">
                                UOM
                            </th>

                            <th class="border border-gray-300 px-2 py-2
                                text-center text-xs font-semibold
                                uppercase dark:border-gray-700">
                                Journal (COA)
                            </th>

                            <th class="border border-gray-300 px-2 py-2
                                text-center text-xs font-semibold
                                uppercase dark:border-gray-700">
                                Requested
                            </th>

                            <th class="border border-gray-300 px-2 py-2
                                text-center text-xs font-semibold
                                uppercase dark:border-gray-700">
                                Assigned
                            </th>

                            <th class="border border-gray-300 px-2 py-2
                                text-left text-xs font-semibold
                                uppercase dark:border-gray-700">
                                Supplier
                            </th>

                            <th class="border border-gray-300 px-2 py-2
                                text-right text-xs font-semibold
                                uppercase dark:border-gray-700">
                                Unit Price
                            </th>

                            <th class="border border-gray-300 px-2 py-2
                                text-right text-xs font-semibold
                                uppercase dark:border-gray-700">
                                Disc %
                            </th>

                            <th class="border border-gray-300 px-2 py-2
                                text-right text-xs font-semibold
                                uppercase dark:border-gray-700">
                                Disc Amt
                            </th>

                            <th class="border border-gray-300 px-2 py-2
                                text-left text-xs font-semibold
                                uppercase dark:border-gray-700">
                                Tax
                            </th>

                            <th class="border border-gray-300 px-2 py-2
                                text-right text-xs font-semibold
                                uppercase dark:border-gray-700">
                                Tax %
                            </th>

                            <th class="border border-gray-300 px-2 py-2
                                text-right text-xs font-semibold
                                uppercase dark:border-gray-700">
                                Tax Amt
                            </th>

                            <th class="border border-gray-300 px-2 py-2
                                text-right text-xs font-semibold
                                uppercase dark:border-gray-700">
                                Grand Total
                            </th>

                        </tr>
                    </thead>

                    <tbody class="bg-white dark:bg-gray-900">
                        '.$rows.'
                    </tbody>

                </table>

                <div class="flex min-w-[1650px] items-center
                    justify-between gap-8 border-t border-gray-200
                    bg-gray-50 px-5 py-3 text-xs
                    dark:border-gray-700 dark:bg-gray-800">

                    <div class="whitespace-nowrap">
                        <span class="text-gray-500">
                            Total Lines :
                        </span>
                        <strong class="text-blue-700">
                            '.$totalLines.'
                        </strong>
                    </div>

                    <div class="flex items-center gap-8 whitespace-nowrap">

                        <div>
                            <span class="text-gray-500">
                                Requested Qty :
                            </span>
                            <strong class="text-blue-700">
                                '.number_format($totalRequestedQty, 2).'
                            </strong>
                        </div>

                        <div>
                            <span class="text-gray-500">
                                Assigned Qty :
                            </span>
                            <strong class="text-blue-700">
                                '.number_format($totalAssignedQty, 2).'
                            </strong>
                        </div>

                        <div>
                            <span class="text-gray-500">
                                Total Amount :
                            </span>
                            <strong class="text-blue-700">
                                Rp '.number_format($totalAmount, 2).'
                            </strong>
                        </div>

                    </div>

                </div>

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


    /*
    |--------------------------------------------------------------------------
    | Supporting Documents
    |--------------------------------------------------------------------------
    */

    protected static function renderSupportingDocuments(
        ?AssignmentMaterialRequisition $record
    ): HtmlString {

        if (! $record) {
            return new HtmlString(
                '<div class="text-sm text-gray-500">
                    No assignment found.
                </div>'
            );
        }

        $documents = $record
            ->documents()
            ->with('uploader')
            ->orderBy('id')
            ->get();

        if ($documents->isEmpty()) {
            return new HtmlString(
                '<div class="rounded-lg border border-dashed
                    border-gray-300 px-4 py-6 text-center
                    text-sm text-gray-500 dark:border-gray-700">
                    No supporting documents uploaded.
                </div>'
            );
        }

        $rows = '';

        foreach ($documents as $document) {

            $fileName = $document->file_name
                ?: basename($document->file_path);

            $documentType = $document->document_type ?: '-';

            $uploadedBy = $document->uploader?->name ?? '-';

            $uploadedAt = $document->created_at
                ? \App\Support\Timezone\UserTimezone::format(
                    $document->created_at,
                    'd M Y H:i:s'
                )
                : '-';

            $storage = \Illuminate\Support\Facades\Storage::disk(
                'public'
            );

            $exists = $storage->exists($document->file_path);

            $url = $exists
                ? $storage->url($document->file_path)
                : null;

            $viewAction = $url
                ? '<a href="'.e($url).'"
                        target="_blank"
                        rel="noopener noreferrer"
                        class="inline-flex items-center gap-1
                            whitespace-nowrap text-primary-600
                            hover:underline">
                        <span>◎</span> Open
                   </a>'
                : '<span class="whitespace-nowrap text-gray-400">
                        Unavailable
                   </span>';

            $downloadAction = $url
                ? '<a href="'.e($url).'"
                        download="'.e($fileName).'"
                        class="inline-flex items-center gap-1
                            whitespace-nowrap text-primary-600
                            hover:underline">
                        <span>↓</span> Download
                   </a>'
                : '<span class="whitespace-nowrap text-gray-400">
                        Unavailable
                   </span>';

            $rows .= '
                <tr class="border-b border-gray-100
                    last:border-0 dark:border-gray-800">

                    <td class="px-3 py-4 text-sm font-semibold
                        text-gray-900 dark:text-white">
                        '.e($fileName).'
                    </td>

                    <td class="whitespace-nowrap px-3 py-4">
                        <span class="inline-flex rounded-md
                            bg-warning-50 px-2 py-1 text-xs
                            font-medium text-warning-700
                            dark:bg-warning-400/10
                            dark:text-warning-400">
                            '.e($documentType).'
                        </span>
                    </td>

                    <td class="whitespace-nowrap px-3 py-4
                        text-sm text-gray-700 dark:text-gray-300">
                        '.e($uploadedBy).'
                    </td>

                    <td class="whitespace-nowrap px-3 py-4
                        text-sm text-gray-700 dark:text-gray-300">
                        '.e($uploadedAt).'
                    </td>

                    <td class="whitespace-nowrap px-3 py-4
                        text-sm">
                        '.$viewAction.'
                    </td>

                    <td class="whitespace-nowrap px-3 py-4
                        text-sm">
                        '.$downloadAction.'
                    </td>

                </tr>
            ';
        }

        return new HtmlString(
            '
            <div class="overflow-x-auto rounded-lg
                border border-gray-200 dark:border-gray-700">

                <table class="w-full min-w-[1050px]
                    table-auto">

                    <thead class="bg-gray-50 dark:bg-gray-800">
                        <tr>

                            <th class="px-3 py-3 text-left
                                text-xs font-semibold uppercase
                                tracking-wide text-gray-500">
                                Document
                            </th>

                            <th class="px-3 py-3 text-left
                                text-xs font-semibold uppercase
                                tracking-wide text-gray-500">
                                Type
                            </th>

                            <th class="px-3 py-3 text-left
                                text-xs font-semibold uppercase
                                tracking-wide text-gray-500">
                                Uploaded By
                            </th>

                            <th class="px-3 py-3 text-left
                                text-xs font-semibold uppercase
                                tracking-wide text-gray-500">
                                Uploaded At
                            </th>

                            <th class="px-3 py-3 text-left
                                text-xs font-semibold uppercase
                                tracking-wide text-gray-500">
                                View
                            </th>

                            <th class="px-3 py-3 text-left
                                text-xs font-semibold uppercase
                                tracking-wide text-gray-500">
                                Download
                            </th>

                        </tr>
                    </thead>

                    <tbody class="divide-y divide-gray-100
                        dark:divide-gray-800">
                        '.$rows.'
                    </tbody>

                </table>

            </div>
            '
        );
    }



    /*
    |--------------------------------------------------------------------------
    | Information Box
    |--------------------------------------------------------------------------
    */

    protected static function renderInfoBox(
        ?string $value
    ): HtmlString {

        return new HtmlString(
            '<div class="rounded-lg border border-gray-200
                bg-gray-50 px-3 py-2 text-sm text-gray-700
                dark:border-gray-700 dark:bg-gray-800
                dark:text-gray-200">'
                . e($value ?: '-')
            . '</div>'
        );
    }


}