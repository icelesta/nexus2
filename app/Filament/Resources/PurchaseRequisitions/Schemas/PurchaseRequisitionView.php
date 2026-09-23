<?php

declare(strict_types=1);

namespace App\Filament\Resources\PurchaseRequisitions\Schemas;

use App\Models\ApprovalTransaction;
use Filament\Forms\Components\Placeholder;

use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\View;
use Filament\Schemas\Schema;

class PurchaseRequisitionView
{
    public static function configure(
        Schema $schema,
    ): Schema {

        return $schema
            ->components([

                /*
                |--------------------------------------------------------------------------
                | DOCUMENT HEADER
                |--------------------------------------------------------------------------
                */

                Section::make('Material Requisition')
                    ->schema([

                        View::make(
                            'filament.resources.purchase-requisitions.pages.purchase-requisition-header'
                        ),

                    ])
                    ->columnSpanFull()
                    ->collapsible(false),

                /*
                |--------------------------------------------------------------------------
                | GENERAL INFORMATION
                |--------------------------------------------------------------------------
                */

                Section::make('General Information')
                    ->description(
                        'Material Requisition document information.'
                    )
                    ->schema([

                        Grid::make(2)
                            ->schema([

                                /*
                                |--------------------------------------------------------------------------
                                | REQUEST INFORMATION
                                |--------------------------------------------------------------------------
                                */

                                Section::make('Request Information')
                                    ->schema([

                                        TextEntry::make('request_date')
                                            ->label('Request Date')
                                            ->date('d M Y'),

                                        TextEntry::make('required_date')
                                            ->label('Required Date')
                                            ->date('d M Y'),

                                        TextEntry::make('priority')
                                            ->label('Priority')
                                            ->formatStateUsing(
                                                fn ($state) =>
                                                    $state
                                                        ? ucfirst((string) $state)
                                                        : '-'
                                            ),

                                        TextEntry::make('requester.name')
                                            ->label('Requester')
                                            ->placeholder('-'),

                                        TextEntry::make('delivery_location')
                                            ->label('Delivery Location')
                                            ->placeholder('-'),

                                        TextEntry::make('reference_no')
                                            ->label('Reference No')
                                            ->placeholder('-'),

                                        TextEntry::make('remarks')
                                            ->label('Remarks')
                                            ->placeholder('-')
                                            ->columnSpanFull(),

                                    ])
                                    ->columns(2),

                                /*
                                |--------------------------------------------------------------------------
                                | ORGANIZATION INFORMATION
                                |--------------------------------------------------------------------------
                                */

                                Section::make('Organization Information')
                                    ->schema([

                                        TextEntry::make('company.company_name')
                                            ->label('Company')
                                            ->placeholder('-'),

                                        TextEntry::make(
                                            'businessUnit.business_unit_name'
                                        )
                                            ->label('Business Unit')
                                            ->placeholder('-'),

                                        TextEntry::make('branch.branch_name')
                                            ->label('Branch')
                                            ->placeholder('-'),

                                        TextEntry::make(
                                            'department.department_name'
                                        )
                                            ->label('Department')
                                            ->placeholder('-'),

                                        TextEntry::make('section.section_name')
                                            ->label('Section')
                                            ->placeholder('-'),

                                        TextEntry::make(
                                            'costCenter.cost_center_name'
                                        )
                                            ->label('Cost Center')
                                            ->placeholder('-'),

                                        TextEntry::make(
                                            'warehouse.warehouse_name'
                                        )
                                            ->label('Warehouse')
                                            ->placeholder('-'),

                                        TextEntry::make(
                                            'currency.currency_code'
                                        )
                                            ->label('Currency')
                                            ->placeholder('-'),

                                    ])
                                    ->columns(2),

                            ])
                            ->columnSpanFull(),

                    ])
                    ->columnSpanFull()
                    ->collapsible(false),

                /*
                |--------------------------------------------------------------------------
                | MATERIAL REQUISITION ITEMS
                |--------------------------------------------------------------------------
                |
                | GOLDEN READ-ONLY
                |
                | Commercial information is synchronized from AMR.
                |
                */

                Section::make('Material Requisition Items')
                    ->description(
                        'Requested items and current commercial assignment from Assignment Material Requisition.'
                    )
                    ->schema([

                        View::make(
                            'filament.resources.purchase-requisitions.pages.purchase-requisition-items-table-view'
                        ),

                    ])
                    ->columnSpanFull()
                    ->collapsible(false),


                /*
                |--------------------------------------------------------------------------
                | APPROVAL HISTORY
                |--------------------------------------------------------------------------
                |
                | Minimal / compact approval history.
                |
                | UI follows the Direct Market approval history pattern:
                | - One compact row per approval level
                | - Snapshot role
                | - Approval date
                | - Approver
                | - Remarks
                | - Status badge
                |
                | Business logic remains untouched.
                |
                */

                Section::make('Approval History')
                    ->description(
                        'Approval activity and decision history for this Material Requisition.'
                    )
                    ->icon('heroicon-o-document-duplicate')
                    ->schema([

                        Placeholder::make('approval_history')
                            ->hiddenLabel()
                            ->content(
                                function ($record): \Illuminate\Support\HtmlString {

                                    if (! $record) {
                                        return new \Illuminate\Support\HtmlString(
                                            '<div class="text-sm text-gray-500">'
                                            . 'No approval information available.'
                                            . '</div>'
                                        );
                                    }

                                    /*
                                    |--------------------------------------------------------------------------
                                    | APPROVAL TRANSACTION
                                    |--------------------------------------------------------------------------
                                    */

                                    $transaction = ApprovalTransaction::query()
                                        ->where(
                                            'document_type',
                                            'MATERIAL_REQUISITION'
                                        )
                                        ->where(
                                            'document_id',
                                            $record->getKey()
                                        )
                                        ->latest('id')
                                        ->with('steps')
                                        ->first();

                                    if (! $transaction) {
                                        return new \Illuminate\Support\HtmlString(
                                            '<div class="rounded-lg border border-gray-200 '
                                            . 'bg-gray-50 px-4 py-3 text-sm text-gray-500 '
                                            . 'dark:border-gray-700 dark:bg-gray-800">'
                                            . 'Approval has not been submitted yet.'
                                            . '</div>'
                                        );
                                    }

                                    /*
                                    |--------------------------------------------------------------------------
                                    | SNAPSHOT STEPS
                                    |--------------------------------------------------------------------------
                                    */

                                    $steps = $transaction->steps
                                        ->sortBy('approval_level')
                                        ->values();

                                    if ($steps->isEmpty()) {
                                        return new \Illuminate\Support\HtmlString(
                                            '<div class="rounded-lg border border-gray-200 '
                                            . 'bg-gray-50 px-4 py-3 text-sm text-gray-500 '
                                            . 'dark:border-gray-700 dark:bg-gray-800">'
                                            . 'No approval steps available.'
                                            . '</div>'
                                        );
                                    }

                                    /*
                                    |--------------------------------------------------------------------------
                                    | BUILD COMPACT HISTORY
                                    |--------------------------------------------------------------------------
                                    */

                                    $html = '
                                        <div class="overflow-hidden rounded-lg border border-gray-200 dark:border-gray-700">
                                    ';

                                    foreach ($steps as $step) {

                                        $level = (int) $step->approval_level;

                                        $roleName = filled($step->role_name)
                                            ? e($step->role_name)
                                            : 'Approver';

                                        $status = strtoupper(
                                            (string) ($step->status ?? 'PENDING')
                                        );

                                        /*
                                        |--------------------------------------------------------------------------
                                        | APPROVER SNAPSHOT
                                        |--------------------------------------------------------------------------
                                        */

                                        $approverName = '-';

                                        if ($step->approved_by) {

                                            $approverName = e(
                                                \App\Models\User::query()
                                                    ->where(
                                                        'id',
                                                        $step->approved_by
                                                    )
                                                    ->value('name')
                                                ?? '-'
                                            );

                                        } elseif ($status === 'PENDING') {

                                            $approverName = 'Waiting for approval';
                                        }

                                        /*
                                        |--------------------------------------------------------------------------
                                        | DATE SNAPSHOT
                                        |--------------------------------------------------------------------------
                                        */

                                        $dateDisplay = '-';

                                        if ($step->acted_at) {

                                            $dateDisplay = e(
                                                $step->acted_at
                                                    ->timezone(
                                                        config(
                                                            'app.timezone',
                                                            'Asia/Jakarta'
                                                        )
                                                    )
                                                    ->format('d M Y H:i')
                                            );
                                        }

                                        /*
                                        |--------------------------------------------------------------------------
                                        | REMARKS SNAPSHOT
                                        |--------------------------------------------------------------------------
                                        */

                                        $remarks = filled($step->remarks)
                                            ? e($step->remarks)
                                            : '-';

                                        /*
                                        |--------------------------------------------------------------------------
                                        | STATUS
                                        |--------------------------------------------------------------------------
                                        */

                                        $statusLabel = match ($status) {

                                            'APPROVED' => 'APPROVED',

                                            'REJECTED' => 'REJECTED',

                                            default => 'PENDING',
                                        };

                                        $statusClass = match ($status) {

                                            'APPROVED' =>
                                                'bg-success-50 text-success-700 ring-success-600/20',

                                            'REJECTED' =>
                                                'bg-danger-50 text-danger-700 ring-danger-600/20',

                                            default =>
                                                'bg-warning-50 text-warning-700 ring-warning-600/20',
                                        };

                                        /*
                                        |--------------------------------------------------------------------------
                                        | STATUS ICON
                                        |--------------------------------------------------------------------------
                                        |
                                        | Inline SVG is used intentionally so the icon
                                        | remains visible and independent from dynamic
                                        | component rendering.
                                        |
                                        */

                                        $statusIcon = match ($status) {

                                            'APPROVED' => '
                                                <svg
                                                    xmlns="http://www.w3.org/2000/svg"
                                                    viewBox="0 0 20 20"
                                                    fill="currentColor"
                                                    class="h-3.5 w-3.5"
                                                >
                                                    <path
                                                        fill-rule="evenodd"
                                                        d="M16.704 5.29a.75.75 0 0 1 .006 1.06l-7.25 7.25a.75.75 0 0 1-1.06 0l-3.25-3.25a.75.75 0 1 1 1.06-1.06l2.72 2.72 6.72-6.72a.75.75 0 0 1 1.054 0Z"
                                                        clip-rule="evenodd"
                                                    />
                                                </svg>
                                            ',

                                            'REJECTED' => '
                                                <svg
                                                    xmlns="http://www.w3.org/2000/svg"
                                                    viewBox="0 0 20 20"
                                                    fill="currentColor"
                                                    class="h-3.5 w-3.5"
                                                >
                                                    <path
                                                        d="M6.28 5.22a.75.75 0 0 0-1.06 1.06L8.94 10l-3.72 3.72a.75.75 0 1 0 1.06 1.06L10 11.06l3.72 3.72a.75.75 0 0 0 1.06-1.06L11.06 10l3.72-3.72a.75.75 0 0 0-1.06-1.06L10 8.94 6.28 5.22Z"
                                                    />
                                                </svg>
                                            ',

                                            default => '
                                                <svg
                                                    xmlns="http://www.w3.org/2000/svg"
                                                    viewBox="0 0 20 20"
                                                    fill="currentColor"
                                                    class="h-3.5 w-3.5"
                                                >
                                                    <path
                                                        fill-rule="evenodd"
                                                        d="M10 5.25a.75.75 0 0 1 .75.75v3.69l2.47 1.426a.75.75 0 1 1-.75 1.299l-2.844-1.642A.75.75 0 0 1 9.25 10V6a.75.75 0 0 1 .75-.75ZM10 18a8 8 0 1 0 0-16 8 8 0 0 0 0 16Z"
                                                        clip-rule="evenodd"
                                                    />
                                                </svg>
                                            ',
                                        };

                                        /*
                                        |--------------------------------------------------------------------------
                                        | COMPACT ROW
                                        |--------------------------------------------------------------------------
                                        */

                                        $html .= <<<HTML

                                            <div class="border-b border-gray-200 px-3 py-2.5 last:border-b-0 dark:border-gray-700">

                                                <div class="flex items-center gap-3">

                                                    <!-- STATUS ICON -->
                                                    <div class="flex h-7 w-7 shrink-0 items-center justify-center rounded-full
                                                        {$statusClass}">
                                                        {$statusIcon}
                                                    </div>

                                                    <!-- LEVEL / ROLE -->
                                                    <div class="w-36 shrink-0">

                                                        <div class="text-xs font-semibold text-gray-900 dark:text-white">
                                                            Approval Level {$level}
                                                        </div>

                                                        <div class="mt-0.5 text-xs text-gray-500 dark:text-gray-400">
                                                            {$roleName}
                                                        </div>

                                                    </div>

                                                    <!-- DATE -->
                                                    <div class="w-40 shrink-0 border-l border-gray-200 pl-3 dark:border-gray-700">

                                                        <div class="text-[11px] text-gray-400">
                                                            Date
                                                        </div>

                                                        <div class="mt-0.5 text-xs text-gray-700 dark:text-gray-200">
                                                            {$dateDisplay}
                                                        </div>

                                                    </div>

                                                    <!-- APPROVER -->
                                                    <div class="w-40 shrink-0 border-l border-gray-200 pl-3 dark:border-gray-700">

                                                        <div class="text-[11px] text-gray-400">
                                                            By
                                                        </div>

                                                        <div class="mt-0.5 truncate text-xs text-gray-700 dark:text-gray-200">
                                                            {$approverName}
                                                        </div>

                                                    </div>

                                                    <!-- REMARKS -->
                                                    <div class="min-w-0 flex-1 border-l border-gray-200 pl-3 dark:border-gray-700">

                                                        <div class="text-[11px] text-gray-400">
                                                            Remarks
                                                        </div>

                                                        <div class="mt-0.5 truncate text-xs text-gray-700 dark:text-gray-200">
                                                            {$remarks}
                                                        </div>

                                                    </div>

                                                    <!-- STATUS -->
                                                    <div class="shrink-0">

                                                        <span class="inline-flex items-center rounded-full px-2.5 py-1 text-[10px] font-semibold ring-1 {$statusClass}">
                                                            {$statusLabel}
                                                        </span>

                                                    </div>

                                                </div>

                                            </div>

                                        HTML;
                                    }

                                    $html .= '</div>';

                                    return new \Illuminate\Support\HtmlString($html);
                                }
                            )
                            ->columnSpanFull(),

                    ])
                    ->columnSpanFull()
                    ->collapsible(false),


            ]);
    }
}