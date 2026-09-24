<?php

declare(strict_types=1);

namespace App\Filament\Resources\PurchaseRequisitions\Schemas;

use App\Models\ApprovalMaster;
use App\Models\ApprovalTransaction;
use Filament\Forms\Components\Placeholder;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use App\Support\Timezone\UserTimezone;

class PurchaseRequisitionApproval
{
    public static function configure(
        Schema $schema,
    ): Schema {

        return $schema
            ->components([

                /*
                |--------------------------------------------------------------------------
                | WORKFLOW INFORMATION
                |--------------------------------------------------------------------------
                */

                Section::make('Workflow Information')
                    ->description(
                        'Current workflow status of this Material Requisition.'
                    )
                    ->icon('heroicon-o-arrow-path')
                    ->collapsible()
                    ->columns(12)
                    ->schema([

                        Placeholder::make('document_status')
                            ->label('Document Status')
                            ->content(
                                fn ($record): string =>
                                    $record?->status ?? 'Draft'
                            )
                            ->columnSpan(3),

                        Placeholder::make('workflow_status')
                            ->label('Workflow Status')
                            ->content(
                                function ($record): string {

                                    if (! $record) {
                                        return 'Waiting for Submission';
                                    }

                                    $transaction =
                                        ApprovalTransaction::query()
                                            ->where(
                                                'document_type',
                                                'MATERIAL_REQUISITION'
                                            )
                                            ->where(
                                                'document_id',
                                                $record->getKey()
                                            )
                                            ->latest('id')
                                            ->first();

                                    if (! $transaction) {

                                        return match (
                                            $record->status
                                        ) {

                                            'Draft' =>
                                                'Waiting for Submission',

                                            default =>
                                                $record->status,
                                        };
                                    }

                                    return match (
                                        $transaction->status
                                    ) {

                                        'PENDING' =>
                                            'Approval In Progress',

                                        'APPROVED' =>
                                            'Approval In Progress',

                                        'REJECTED' =>
                                            'Approval Rejected',

                                        'CANCELLED' =>
                                            'Approval Cancelled',

                                        default =>
                                            $record->status,
                                    };
                                }
                            )
                            ->columnSpan(3),

                        Placeholder::make('current_step')
                            ->label('Current Step')
                            ->content(
                                function ($record): string {

                                    if (! $record) {
                                        return 'Document Preparation';
                                    }

                                    $transaction =
                                        ApprovalTransaction::query()
                                            ->where(
                                                'document_type',
                                                'MATERIAL_REQUISITION'
                                            )
                                            ->where(
                                                'document_id',
                                                $record->getKey()
                                            )
                                            ->latest('id')
                                            ->first();

                                    if (! $transaction) {
                                        return 'Document Preparation';
                                    }

                                    $step =
                                        $transaction
                                            ->steps()
                                            ->where(
                                                'approval_level',
                                                $transaction->current_level
                                            )
                                            ->first();

                                    if (! $step) {
                                        return 'Approval Completed';
                                    }

                                    return sprintf(
                                        'Level %d — %s',
                                        (int) $step->approval_level,
                                        $step->role_name
                                            ?? 'Approver'
                                    );
                                }
                            )
                            ->columnSpan(3),

                        Placeholder::make('next_step')
                            ->label('Next Step')
                            ->content(
                                function ($record): string {

                                    if (! $record) {
                                        return 'Submit Material Requisition';
                                    }

                                    $transaction =
                                        ApprovalTransaction::query()
                                            ->where(
                                                'document_type',
                                                'MATERIAL_REQUISITION'
                                            )
                                            ->where(
                                                'document_id',
                                                $record->getKey()
                                            )
                                            ->latest('id')
                                            ->first();

                                    if (! $transaction) {
                                        return 'Submit Material Requisition';
                                    }

                                    $master =
                                        ApprovalMaster::query()
                                            ->active()
                                            ->where(
                                                'code',
                                                'MR-APPROVAL'
                                            )
                                            ->with([
                                                'steps' => fn ($query) =>
                                                    $query
                                                        ->orderBy(
                                                            'approval_level'
                                                        )
                                                        ->with('role'),
                                            ])
                                            ->first();

                                    if (! $master) {
                                        return 'Approval Master Not Configured';
                                    }

                                    $nextStep =
                                        $master->steps
                                            ->first(
                                                fn ($step) =>
                                                    (int) $step->approval_level
                                                    >
                                                    (int) $transaction->current_level
                                            );

                                    if (! $nextStep) {

                                        if (
                                            $transaction->status
                                            === 'APPROVED'
                                        ) {
                                            return 'Approval Completed';
                                        }

                                        return 'Waiting';
                                    }

                                    return sprintf(
                                        'Approval %d — %s',
                                        (int) $nextStep->approval_level,
                                        $nextStep->role?->name
                                            ?? 'Approver'
                                    );
                                }
                            )
                            ->columnSpan(3),

                    ])
                    ->columnSpanFull(),

                /*
                |--------------------------------------------------------------------------
                | APPROVAL PROGRESS
                |--------------------------------------------------------------------------
                |
                | Dynamic Approval Master visualization.
                |
                | The number of approval levels is NOT hard-coded.
                |
                */

                Section::make('Approval Progress')
                    ->description(
                        'Approval levels and approval results for this Material Requisition.'
                    )
                    ->icon('heroicon-o-check-badge')
                    ->collapsible()
                    ->columns(12)
                    ->schema([

                        Placeholder::make('approval_progress')
                            ->label('')
                            ->content(
                                function ($record): \Illuminate\Support\HtmlString {

                                    if (! $record) {
                                        return new \Illuminate\Support\HtmlString(
                                            '<div class="text-sm text-gray-500">No approval information available.</div>'
                                        );
                                    }

                                    /*
                                    |--------------------------------------------------------------------------
                                    | APPROVAL MASTER
                                    |--------------------------------------------------------------------------
                                    */

                                    $master =
                                        ApprovalMaster::query()
                                            ->active()
                                            ->where(
                                                'code',
                                                'MR-APPROVAL'
                                            )
                                            ->with([
                                                'steps' => fn ($query) =>
                                                    $query
                                                        ->orderBy(
                                                            'approval_level'
                                                        )
                                                        ->with('role'),
                                            ])
                                            ->first();

                                    if (! $master) {

                                        return new \Illuminate\Support\HtmlString(
                                            '<div class="text-sm text-danger-600">Approval Master MR-APPROVAL is not configured.</div>'
                                        );
                                    }

                                    /*
                                    |--------------------------------------------------------------------------
                                    | TRANSACTION
                                    |--------------------------------------------------------------------------
                                    */

                                    $transaction =
                                        ApprovalTransaction::query()
                                            ->where(
                                                'document_type',
                                                'MATERIAL_REQUISITION'
                                            )
                                            ->where(
                                                'document_id',
                                                $record->getKey()
                                            )
                                            ->latest('id')
                                            ->with([
                                                'steps',
                                            ])
                                            ->first();

                                    /*
                                    |--------------------------------------------------------------------------
                                    | BUILD APPROVAL CARDS
                                    |--------------------------------------------------------------------------
                                    */

                                    $html =
                                        '<div class="grid grid-cols-1 gap-4 md:grid-cols-2">';

                                    foreach (
                                        $master->steps
                                            ->sortBy('approval_level')
                                        as $masterStep
                                    ) {

                                        $level =
                                            (int) $masterStep->approval_level;

                                        $transactionStep =
                                            $transaction?->steps
                                                ?->first(
                                                    fn ($step) =>
                                                        (int) $step->approval_level
                                                        === $level
                                                );

                                        /*
                                        |--------------------------------------------------------------------------
                                        | ROLE
                                        |--------------------------------------------------------------------------
                                        */

                                        $roleName =
                                            $transactionStep?->role_name
                                            ??
                                            $masterStep->role?->name
                                            ??
                                            'Approver';

                                        /*
                                        |--------------------------------------------------------------------------
                                        | STATUS
                                        |--------------------------------------------------------------------------
                                        */

                                        $status =
                                            $transactionStep?->status
                                            ?? 'PENDING';

                                        /*
                                        |--------------------------------------------------------------------------
                                        | APPROVER
                                        |--------------------------------------------------------------------------
                                        */

                                        $approverName = null;

                                        if (
                                            $transactionStep
                                            && $transactionStep->approved_by
                                        ) {

                                            $approverName =
                                                \App\Models\User::query()
                                                    ->where(
                                                        'id',
                                                        $transactionStep->approved_by
                                                    )
                                                    ->value('name');
                                        }

                                        /*
                                        |--------------------------------------------------------------------------
                                        | ACTION
                                        |--------------------------------------------------------------------------
                                        */

                                        $action =
                                            $transactionStep?->action;

                                        /*
                                        |--------------------------------------------------------------------------
                                        | ACTED AT
                                        |--------------------------------------------------------------------------
                                        */

                                        $actedAt =
                                            $transactionStep?->acted_at;

                                        /*
                                        |--------------------------------------------------------------------------
                                        | REMARKS
                                        |--------------------------------------------------------------------------
                                        */

                                        $remarks =
                                            $transactionStep?->remarks;

                                        /*
                                        |--------------------------------------------------------------------------
                                        | VISUAL STATE
                                        |--------------------------------------------------------------------------
                                        */

                                        $statusLabel =
                                            match ($status) {

                                                'APPROVED' =>
                                                    'APPROVED',

                                                'REJECTED' =>
                                                    'REJECTED',

                                                default =>
                                                    'PENDING',
                                            };

                                        $statusClass =
                                            match ($status) {

                                                'APPROVED' =>
                                                    'bg-success-50 text-success-700 ring-success-600/20',

                                                'REJECTED' =>
                                                    'bg-danger-50 text-danger-700 ring-danger-600/20',

                                                default =>
                                                    'bg-warning-50 text-warning-700 ring-warning-600/20',
                                            };

                                        $icon =
                                            match ($status) {

                                                'APPROVED' =>
                                                    'heroicon-o-check-circle',

                                                'REJECTED' =>
                                                    'heroicon-o-x-circle',

                                                default =>
                                                    'heroicon-o-clock',
                                            };

                                        /*
                                        |--------------------------------------------------------------------------
                                        | APPROVER DISPLAY
                                        |--------------------------------------------------------------------------
                                        */

                                        if ($approverName) {

                                            $approverDisplay =
                                                e($approverName);

                                        } elseif (
                                            $status === 'PENDING'
                                        ) {

                                            $approverDisplay =
                                                'Waiting for approval';

                                        } else {

                                            $approverDisplay =
                                                '-';
                                        }

                                        /*
                                        |--------------------------------------------------------------------------
                                        | DATE DISPLAY
                                        |--------------------------------------------------------------------------
                                        */

                                        $dateDisplay =
                                            $actedAt
                                                ? e(
                                                    $actedAt
                                                        ->timezone(
                                                            UserTimezone::timezone()
                                                        )
                                                        ->format(
                                                            'd M Y H:i'
                                                        )
                                                )
                                                : '-';

                                        /*
                                        |--------------------------------------------------------------------------
                                        | REMARK DISPLAY
                                        |--------------------------------------------------------------------------
                                        */

                                        $remarksDisplay =
                                            filled($remarks)
                                                ? e($remarks)
                                                : '-';

                                        /*
                                        |--------------------------------------------------------------------------
                                        | SAFE DISPLAY VALUES FOR HEREDOC
                                        |--------------------------------------------------------------------------
                                        */

                                        $actionDisplay =
                                            $action
                                                ? e($action)
                                                : '-';

                                        $totalLevels =
                                            $master->steps->count();

                                        /*
                                        |--------------------------------------------------------------------------
                                        | CARD
                                        |--------------------------------------------------------------------------
                                        */

                                        $html .= <<<HTML

                                            <div class="rounded-xl border border-gray-200 bg-white p-4 shadow-sm dark:border-gray-700 dark:bg-gray-900">

                                                <div class="flex items-start justify-between gap-4">

                                                    <div class="flex items-start gap-3">

                                                        <div class="flex h-10 w-10 shrink-0 items-center justify-center rounded-full bg-gray-100 dark:bg-gray-800">

                                                            <x-dynamic-component
                                                                component="{$icon}"
                                                                class="h-5 w-5 text-gray-500"
                                                            />

                                                        </div>

                                                        <div>

                                                            <div class="text-xs font-semibold uppercase tracking-wide text-gray-400">
                                                                Approval {$level} / {$totalLevels}
                                                            </div>

                                                            <div class="mt-1 text-base font-semibold text-gray-900 dark:text-white">
                                                                {$roleName}
                                                            </div>

                                                            <div class="mt-1 text-sm text-gray-600 dark:text-gray-300">
                                                                {$approverDisplay}
                                                            </div>

                                                        </div>

                                                    </div>

                                                    <span class="inline-flex items-center rounded-full px-2.5 py-1 text-xs font-semibold ring-1 {$statusClass}">
                                                        {$statusLabel}
                                                    </span>

                                                </div>

                                                <div class="mt-4 grid grid-cols-1 gap-3 border-t border-gray-100 pt-3 text-xs dark:border-gray-800 sm:grid-cols-2">

                                                    <div>

                                                        <div class="font-medium text-gray-400">
                                                            Approval Date
                                                        </div>

                                                        <div class="mt-1 text-gray-700 dark:text-gray-300">
                                                            {$dateDisplay}
                                                        </div>

                                                    </div>

                                                    <div>

                                                        <div class="font-medium text-gray-400">
                                                            Action
                                                        </div>

                                                        <div class="mt-1 text-gray-700 dark:text-gray-300">
                                                            {$actionDisplay}
                                                        </div>

                                                    </div>

                                                </div>

                                                <div class="mt-3 border-t border-gray-100 pt-3 dark:border-gray-800">

                                                    <div class="text-xs font-medium text-gray-400">
                                                        Approval Remarks
                                                    </div>

                                                    <div class="mt-1 text-sm text-gray-600 dark:text-gray-300">
                                                        {$remarksDisplay}
                                                    </div>

                                                </div>

                                            </div>

                                        HTML;
                                    }

                                    $html .= '</div>';

                                    return new \Illuminate\Support\HtmlString(
                                        $html
                                    );
                                }
                            )
                            ->columnSpanFull(),

                    ])
                    ->columnSpanFull(),

                /*
                |--------------------------------------------------------------------------
                | APPROVAL TIMELINE
                |--------------------------------------------------------------------------
                */

                Section::make('Approval Timeline')
                    ->description(
                        'Approval workflow generated from the active Approval Master transaction.'
                    )
                    ->icon('heroicon-o-clock')
                    ->collapsible()
                    ->columns(12)
                    ->schema([

                        Placeholder::make('created_step')
                            ->label('Created')
                            ->content(
                                fn ($record): string =>
                                    $record?->created_at
                                        ? 'Completed — '
                                            . $record->created_at
                                                ->timezone(
                                                    UserTimezone::timezone()
                                                )
                                                ->format(
                                                    'd M Y H:i'
                                                )
                                        : 'Completed'
                            )
                            ->columnSpan(3),

                        Placeholder::make('submitted_step')
                            ->label('Submitted')
                            ->content(
                                fn ($record): string =>
                                    $record?->submitted_at
                                        ? 'Completed — '
                                            . $record->submitted_at
                                                ->timezone(
                                                    UserTimezone::timezone()
                                                )
                                                ->format(
                                                    'd M Y H:i'
                                                )
                                        : 'Waiting'
                            )
                            ->columnSpan(3),

                        Placeholder::make('approval_progress_summary')
                            ->label('Approval Progress')
                            ->content(
                                function ($record): string {

                                    if (! $record) {
                                        return '-';
                                    }

                                    $master =
                                        ApprovalMaster::query()
                                            ->active()
                                            ->where(
                                                'code',
                                                'MR-APPROVAL'
                                            )
                                            ->with([
                                                'steps' => fn ($query) =>
                                                    $query->orderBy(
                                                        'approval_level'
                                                    ),
                                            ])
                                            ->first();

                                    $total =
                                        $master?->steps
                                            ->where(
                                                'is_required',
                                                true
                                            )
                                            ->count()
                                        ?? 0;

                                    $transaction =
                                        ApprovalTransaction::query()
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
                                        return 'Not Submitted';
                                    }

                                    $approved =
                                        $transaction->steps
                                            ->where(
                                                'status',
                                                'APPROVED'
                                            )
                                            ->count();

                                    return sprintf(
                                        '%d / %d Approved',
                                        $approved,
                                        $total
                                    );
                                }
                            )
                            ->columnSpan(3),

                        Placeholder::make('assignment_step')
                            ->label('Assignment Material Requisition')
                            ->content(
                                fn ($record): string =>
                                    $record
                                        ? (
                                            \App\Models\AssignmentMaterialRequisition::query()
                                                ->where(
                                                    'purchase_requisition_id',
                                                    $record->getKey()
                                                )
                                                ->exists()
                                                    ? 'Available'
                                                    : 'Waiting'
                                        )
                                        : 'Waiting'
                            )
                            ->columnSpan(3),

                    ])
                    ->columnSpanFull(),

                /*
                |--------------------------------------------------------------------------
                | Approval History
                |--------------------------------------------------------------------------
                |
                | Display approval history from the MATERIAL_REQUISITION
                | ApprovalTransaction snapshot.
                |
                | IMPORTANT:
                | - Do NOT re-resolve historical approver role from current master.
                | - Transaction step snapshot is the source of truth.
                | - Level 2 may not exist until AMR Submit activates it.
                |
                */

                Section::make('Approval History')
                    ->description(
                        'Approval activity and decision history for this Material Requisition.'
                    )
                    ->icon('heroicon-o-document-duplicate')
                    ->collapsible()
                    ->collapsed()
                    ->columns(12)
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
                                    | LOAD APPROVAL TRANSACTION
                                    |--------------------------------------------------------------------------
                                    */

                                    $transaction =
                                        ApprovalTransaction::query()
                                            ->where(
                                                'document_type',
                                                'MATERIAL_REQUISITION'
                                            )
                                            ->where(
                                                'document_id',
                                                $record->getKey()
                                            )
                                            ->latest('id')
                                            ->with([
                                                'steps',
                                            ])
                                            ->first();

                                    /*
                                    |--------------------------------------------------------------------------
                                    | NO TRANSACTION
                                    |--------------------------------------------------------------------------
                                    */

                                    if (! $transaction) {

                                        return new \Illuminate\Support\HtmlString(
                                            '<div class="rounded-lg border border-gray-200 '
                                            . 'bg-gray-50 p-4 text-sm text-gray-500 '
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
                                            . 'bg-gray-50 p-4 text-sm text-gray-500 '
                                            . 'dark:border-gray-700 dark:bg-gray-800">'
                                            . 'No approval steps available.'
                                            . '</div>'
                                        );
                                    }

                                    /*
                                    |--------------------------------------------------------------------------
                                    | BUILD HISTORY
                                    |--------------------------------------------------------------------------
                                    */

                                    $html =
                                        '<div class="space-y-3">';

                                    foreach ($steps as $step) {

                                        $level =
                                            (int) $step->approval_level;

                                        /*
                                        |--------------------------------------------------------------------------
                                        | SNAPSHOT VALUES
                                        |--------------------------------------------------------------------------
                                        */

                                        $roleName =
                                            filled($step->role_name)
                                                ? e($step->role_name)
                                                : 'Approver';

                                        $status =
                                            strtoupper(
                                                (string) (
                                                    $step->status
                                                    ?? 'PENDING'
                                                )
                                            );

                                        $action =
                                            filled($step->action)
                                                ? e($step->action)
                                                : '-';

                                        $remarks =
                                            filled($step->remarks)
                                                ? e($step->remarks)
                                                : '-';

                                        /*
                                        |--------------------------------------------------------------------------
                                        | APPROVER
                                        |--------------------------------------------------------------------------
                                        */

                                        $approverName = '-';

                                        if ($step->approved_by) {

                                            $approverName =
                                                e(
                                                    \App\Models\User::query()
                                                        ->where(
                                                            'id',
                                                            $step->approved_by
                                                        )
                                                        ->value('name')
                                                        ?? '-'
                                                );
                                        } elseif (
                                            $status === 'PENDING'
                                        ) {

                                            $approverName =
                                                'Waiting for approval';
                                        }

                                        /*
                                        |--------------------------------------------------------------------------
                                        | DATE
                                        |--------------------------------------------------------------------------
                                        */

                                        $dateDisplay = '-';

                                        if ($step->acted_at) {

                                            $dateDisplay =
                                                e(
                                                    $step->acted_at
                                                        ->timezone(
                                                            config(
                                                                'app.timezone',
                                                                'Asia/Jakarta'
                                                            )
                                                        )
                                                        ->format(
                                                            'd M Y H:i'
                                                        )
                                                );
                                        }

                                        /*
                                        |--------------------------------------------------------------------------
                                        | STATUS VISUAL
                                        |--------------------------------------------------------------------------
                                        */

                                        $statusLabel =
                                            match ($status) {

                                                'APPROVED' =>
                                                    'APPROVED',

                                                'REJECTED' =>
                                                    'REJECTED',

                                                default =>
                                                    'PENDING',
                                            };

                                        $statusClass =
                                            match ($status) {

                                                'APPROVED' =>
                                                    'bg-success-50 text-success-700 '
                                                    . 'ring-success-600/20',

                                                'REJECTED' =>
                                                    'bg-danger-50 text-danger-700 '
                                                    . 'ring-danger-600/20',

                                                default =>
                                                    'bg-warning-50 text-warning-700 '
                                                    . 'ring-warning-600/20',
                                            };

                                        $icon =
                                            match ($status) {

                                                'APPROVED' =>
                                                    'heroicon-o-check-circle',

                                                'REJECTED' =>
                                                    'heroicon-o-x-circle',

                                                default =>
                                                    'heroicon-o-clock',
                                            };

                                        /*
                                        |--------------------------------------------------------------------------
                                        | HISTORY CARD
                                        |--------------------------------------------------------------------------
                                        */

                                        $html .= <<<HTML

                                            <div class="rounded-xl border border-gray-200 bg-white p-4 shadow-sm dark:border-gray-700 dark:bg-gray-900">

                                                <div class="flex items-start justify-between gap-4">

                                                    <div class="flex items-start gap-3">

                                                        <div class="flex h-10 w-10 shrink-0 items-center justify-center rounded-full bg-gray-100 dark:bg-gray-800">

                                                            <x-dynamic-component
                                                                component="{$icon}"
                                                                class="h-5 w-5 text-gray-500"
                                                            />

                                                        </div>

                                                        <div>

                                                            <div class="text-xs font-semibold uppercase tracking-wide text-gray-400">
                                                                Approval Level {$level}
                                                            </div>

                                                            <div class="mt-1 text-base font-semibold text-gray-900 dark:text-white">
                                                                {$roleName}
                                                            </div>

                                                            <div class="mt-1 text-sm text-gray-600 dark:text-gray-300">
                                                                {$approverName}
                                                            </div>

                                                        </div>

                                                    </div>

                                                    <span class="inline-flex items-center rounded-full px-2.5 py-1 text-xs font-semibold ring-1 {$statusClass}">
                                                        {$statusLabel}
                                                    </span>

                                                </div>

                                                <div class="mt-4 grid grid-cols-1 gap-4 border-t border-gray-100 pt-4 md:grid-cols-3 dark:border-gray-800">

                                                    <div>
                                                        <div class="text-xs font-medium text-gray-400">
                                                            Date
                                                        </div>

                                                        <div class="mt-1 text-sm text-gray-700 dark:text-gray-200">
                                                            {$dateDisplay}
                                                        </div>
                                                    </div>

                                                    <div>
                                                        <div class="text-xs font-medium text-gray-400">
                                                            Action
                                                        </div>

                                                        <div class="mt-1 text-sm text-gray-700 dark:text-gray-200">
                                                            {$action}
                                                        </div>
                                                    </div>

                                                    <div>
                                                        <div class="text-xs font-medium text-gray-400">
                                                            Role Snapshot
                                                        </div>

                                                        <div class="mt-1 text-sm text-gray-700 dark:text-gray-200">
                                                            {$roleName}
                                                        </div>
                                                    </div>

                                                </div>

                                                <div class="mt-4 border-t border-gray-100 pt-4 dark:border-gray-800">

                                                    <div class="text-xs font-medium text-gray-400">
                                                        Remarks
                                                    </div>

                                                    <div class="mt-1 text-sm text-gray-700 dark:text-gray-200">
                                                        {$remarks}
                                                    </div>

                                                </div>

                                            </div>

                                        HTML;
                                    }

                                    $html .= '</div>';

                                    return new \Illuminate\Support\HtmlString(
                                        $html
                                    );
                                }
                            )
                            ->columnSpanFull(),

                    ])
                    ->columnSpanFull(),

            ]);
    }
}