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
                | APPROVAL HISTORY
                |--------------------------------------------------------------------------
                */

                Section::make('Approval History')
                    ->description(
                        'Approval activity log for this Material Requisition.'
                    )
                    ->icon('heroicon-o-document-duplicate')
                    ->collapsible()
                    ->collapsed()
                    ->columns(12)
                    ->schema([

                        Placeholder::make('submitted_by')
                            ->label('Submitted By')
                            ->content(
                                fn ($record): string =>
                                    $record?->submittedBy?->name
                                        ?? '-'
                            )
                            ->columnSpan(3),

                        Placeholder::make('submitted_at')
                            ->label('Submitted At')
                            ->content(
                                fn ($record): string =>
                                    $record?->submitted_at
                                        ? $record->submitted_at
                                            ->timezone(
                                                UserTimezone::timezone()
                                            )
                                            ->format(
                                                'd M Y H:i'
                                            )
                                        : '-'
                            )
                            ->columnSpan(3),

                        Placeholder::make('last_action')
                            ->label('Last Action')
                            ->content(
                                function ($record): string {

                                    if (! $record) {
                                        return 'Draft';
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
                                            ->with('steps')
                                            ->first();

                                    if (! $transaction) {
                                        return 'Draft';
                                    }

                                    return $transaction
                                        ->steps
                                        ->sortByDesc('acted_at')
                                        ->first()?->action
                                        ?? $transaction->status;
                                }
                            )
                            ->columnSpan(3),

                        Placeholder::make('last_action_at')
                            ->label('Last Action At')
                            ->content(
                                function ($record): string {

                                    if (! $record) {
                                        return '-';
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
                                            ->with('steps')
                                            ->first();

                                    $actedAt =
                                        $transaction
                                            ?->steps
                                            ?->sortByDesc('acted_at')
                                            ->first()
                                            ?->acted_at;

                                    return $actedAt
                                        ? $actedAt
                                            ->timezone(
                                                UserTimezone::timezone()
                                            )
                                            ->format(
                                                'd M Y H:i'
                                            )
                                        : '-';
                                }
                            )
                            ->columnSpan(3),

                        Placeholder::make('last_comment')
                            ->label('Latest Comment')
                            ->content(
                                function ($record): string {

                                    if (! $record) {
                                        return '-';
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
                                            ->with('steps')
                                            ->first();

                                    return $transaction
                                        ?->steps
                                        ?->sortByDesc('acted_at')
                                        ->first()
                                        ?->remarks
                                        ?? '-';
                                }
                            )
                            ->columnSpanFull(),

                    ])
                    ->columnSpanFull(),

            ]);
    }
}