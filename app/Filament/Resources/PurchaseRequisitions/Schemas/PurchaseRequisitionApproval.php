<?php

declare(strict_types=1);

namespace App\Filament\Resources\PurchaseRequisitions\Schemas;

use App\Models\ApprovalTransaction;
use App\Models\ApprovalTransactionStep;
use App\Models\PurchaseRequisition;
use App\Services\Approval\ApprovalTransactionService;
use Filament\Forms\Components\Placeholder;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Illuminate\Support\HtmlString;

class PurchaseRequisitionApproval
{
    /**
     * Configure Material Requisition Approval Information.
     *
     * AM-5.6.1-B
     *
     * IMPORTANT:
     * This method receives the existing Schema instance and adds
     * components directly.
     *
     * It MUST NOT call itself recursively.
     */
    public static function configure(
        Schema $schema,
        ?PurchaseRequisition $record = null,
    ): Schema {

        /*
        |--------------------------------------------------------------------------
        | Resolve Approval Transaction
        |--------------------------------------------------------------------------
        */

        $transaction = null;

        if ($record?->getKey()) {

            $transaction = ApprovalTransaction::query()
                ->with([
                    'approvalMaster',
                    'steps',
                ])
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
        }

        /*
        |--------------------------------------------------------------------------
        | Ordered Approval Steps
        |--------------------------------------------------------------------------
        */

        $steps = $transaction
            ? $transaction->steps
                ->sortBy('approval_level')
                ->values()
            : collect();

        /*
        |--------------------------------------------------------------------------
        | Current Step
        |--------------------------------------------------------------------------
        */

        $currentStep = $transaction
            ? $steps->firstWhere(
                'approval_level',
                $transaction->current_level
            )
            : null;

        /*
        |--------------------------------------------------------------------------
        | Current Approvers
        |--------------------------------------------------------------------------
        */

        $currentApprovers = collect();

        if (
            $transaction
            && $transaction->isPending()
            && $currentStep
        ) {

            try {

                $currentApprovers = app(
                    ApprovalTransactionService::class
                )->getCurrentApprovers(
                    $transaction
                );

            } catch (\Throwable) {

                /*
                |--------------------------------------------------------------------------
                | UI must remain readable even if approver lookup fails.
                |--------------------------------------------------------------------------
                */

                $currentApprovers = collect();
            }
        }

        /*
        |--------------------------------------------------------------------------
        | Approval Master
        |--------------------------------------------------------------------------
        */

        $masterName = $transaction?->approvalMaster?->name
            ?? $transaction?->approvalMaster?->code
            ?? '-';

        /*
        |--------------------------------------------------------------------------
        | Current Approver Display
        |--------------------------------------------------------------------------
        */

        $currentApproverDisplay = '-';

        if ($currentStep) {

            $roleName = $currentStep->role_name
                ?? 'Unassigned';

            $approverNames = $currentApprovers
                ->pluck('name')
                ->filter()
                ->values()
                ->implode(', ');

            if ($approverNames !== '') {

                $currentApproverDisplay =
                    $roleName
                    . ' — '
                    . $approverNames;

            } else {

                $currentApproverDisplay =
                    $roleName;
            }
        }

        /*
        |--------------------------------------------------------------------------
        | Workflow Status
        |--------------------------------------------------------------------------
        */

        $documentStatus = $record?->status
            ?? 'Draft';

        $workflowStatus = match ($documentStatus) {

            'Draft' =>
                'Waiting for Submission',

            'Submitted' =>
                'Submitted',

            'Pending Approval' =>
                $transaction?->status === 'PENDING'
                    ? 'Pending Approval'
                    : 'Approval Transaction Not Active',

            'Approved' =>
                'Approval Completed',

            'Rejected' =>
                'Approval Rejected',

            'Cancelled' =>
                'Cancelled',

            'Closed' =>
                'Closed',

            default =>
                $documentStatus,
        };

        /*
        |--------------------------------------------------------------------------
        | Current Step Display
        |--------------------------------------------------------------------------
        */

        $currentStepDisplay = '-';

        if ($currentStep) {

            $currentStepDisplay =
                'Level '
                . $currentStep->approval_level
                . ' — '
                . ($currentStep->role_name ?? 'Unassigned');
        }

        /*
        |--------------------------------------------------------------------------
        | Next Step
        |--------------------------------------------------------------------------
        */

        $nextStepDisplay = '-';

        if ($transaction && $steps->isNotEmpty()) {

            $nextStep = $steps->first(
                fn (
                    ApprovalTransactionStep $step
                ): bool =>
                    (int) $step->approval_level
                    >
                    (int) $transaction->current_level
            );

            if ($nextStep) {

                $nextStepDisplay =
                    'Level '
                    . $nextStep->approval_level
                    . ' — '
                    . ($nextStep->role_name ?? 'Unassigned');

            } elseif (
                $transaction->status === 'APPROVED'
            ) {

                $nextStepDisplay = 'Approval Completed';

            } elseif (
                $transaction->status === 'REJECTED'
            ) {

                $nextStepDisplay = 'Approval Rejected';

            } else {

                $nextStepDisplay = 'Final Approval';
            }
        } elseif ($documentStatus === 'Draft') {

            $nextStepDisplay =
                'Submit Material Requisition';

        } elseif ($documentStatus === 'Pending Approval') {

            $nextStepDisplay =
                'Approval Transaction';
        }

        /*
        |--------------------------------------------------------------------------
        | Approval Result
        |--------------------------------------------------------------------------
        */

        $approvalResult =
            $currentStep?->status
            ?? $transaction?->status
            ?? 'PENDING';

        /*
        |--------------------------------------------------------------------------
        | Approval Date
        |--------------------------------------------------------------------------
        */

        $approvalDate = '-';

        if ($currentStep?->acted_at) {

            $approvalDate =
                $currentStep->acted_at
                    ->timezone(
                        config(
                            'app.timezone',
                            'Asia/Jakarta'
                        )
                    )
                    ->format('d M Y H:i');
        } elseif ($transaction?->completed_at) {

            $approvalDate =
                $transaction->completed_at
                    ->timezone(
                        config(
                            'app.timezone',
                            'Asia/Jakarta'
                        )
                    )
                    ->format('d M Y H:i');
        }

        /*
        |--------------------------------------------------------------------------
        | Approval Remarks
        |--------------------------------------------------------------------------
        */

        $approvalRemarks =
            $currentStep?->remarks
            ?: '-';

        /*
        |--------------------------------------------------------------------------
        | Build Dynamic Timeline
        |--------------------------------------------------------------------------
        */

        $timelineComponents = [];

        /*
        |--------------------------------------------------------------------------
        | Created
        |--------------------------------------------------------------------------
        */

        $timelineComponents[] =
            Placeholder::make('created_step')
                ->label('Created')
                ->content(
                    $record?->created_at
                        ? 'Completed — '
                            . $record->created_at
                                ->timezone(
                                    config(
                                        'app.timezone',
                                        'Asia/Jakarta'
                                    )
                                )
                                ->format('d M Y H:i')
                        : 'Pending'
                )
                ->columnSpan(3);

        /*
        |--------------------------------------------------------------------------
        | Submitted
        |--------------------------------------------------------------------------
        */

        $timelineComponents[] =
            Placeholder::make('submitted_step')
                ->label('Submitted')
                ->content(
                    $transaction?->submitted_at
                        ? 'Completed — '
                            . $transaction->submitted_at
                                ->timezone(
                                    config(
                                        'app.timezone',
                                        'Asia/Jakarta'
                                    )
                                )
                                ->format('d M Y H:i')
                        : 'Waiting'
                )
                ->columnSpan(3);

        /*
        |--------------------------------------------------------------------------
        | Dynamic Approval Levels
        |--------------------------------------------------------------------------
        */

        foreach (
            $steps
            as $step
        ) {

            $level = (int) $step->approval_level;

            $role = $step->role_name
                ?? 'Unassigned';

            $status = strtoupper(
                (string) $step->status
            );

            $content = match ($status) {

                'APPROVED' =>
                    'Approved'
                    . (
                        $step->acted_at
                            ? ' — '
                                . $step->acted_at
                                    ->timezone(
                                        config(
                                            'app.timezone',
                                            'Asia/Jakarta'
                                        )
                                    )
                                    ->format('d M Y H:i')
                            : ''
                    ),

                'REJECTED' =>
                    'Rejected'
                    . (
                        $step->acted_at
                            ? ' — '
                                . $step->acted_at
                                    ->timezone(
                                        config(
                                            'app.timezone',
                                            'Asia/Jakarta'
                                        )
                                    )
                                    ->format('d M Y H:i')
                            : ''
                    ),

                default =>
                    $transaction
                    && $transaction->isPending()
                    && $level === (int) $transaction->current_level
                        ? 'Current — Pending'
                        : 'Waiting',
            };

            $timelineComponents[] =
                Placeholder::make(
                    'approval_level_' . $level
                )
                    ->label(
                        'Level '
                        . $level
                        . ' — '
                        . $role
                    )
                    ->content($content)
                    ->columnSpan(3);
        }

        /*
        |--------------------------------------------------------------------------
        | Assignment Integration Milestone
        |--------------------------------------------------------------------------
        */

        if (
            $record
            && $documentStatus === 'Approved'
        ) {

            $timelineComponents[] =
                Placeholder::make(
                    'assignment_step'
                )
                    ->label(
                        'Assignment Material Requisition'
                    )
                    ->content('Available')
                    ->columnSpan(3);
        }

        /*
        |--------------------------------------------------------------------------
        | Return Schema
        |--------------------------------------------------------------------------
        */

        return $schema
            ->components([

                /*
                |--------------------------------------------------------------------------
                | Workflow Information
                |--------------------------------------------------------------------------
                */

                Section::make(
                    'Workflow Information'
                )
                    ->description(
                        'Current workflow status of this Material Requisition.'
                    )
                    ->icon(
                        'heroicon-o-arrow-path'
                    )
                    ->collapsible()
                    ->columns(12)
                    ->schema([

                        Placeholder::make(
                            'document_status'
                        )
                            ->label(
                                'Document Status'
                            )
                            ->content(
                                $documentStatus
                            )
                            ->columnSpan(3),

                        Placeholder::make(
                            'workflow_status'
                        )
                            ->label(
                                'Workflow Status'
                            )
                            ->content(
                                $workflowStatus
                            )
                            ->columnSpan(3),

                        Placeholder::make(
                            'current_step'
                        )
                            ->label(
                                'Current Step'
                            )
                            ->content(
                                $currentStepDisplay
                            )
                            ->columnSpan(3),

                        Placeholder::make(
                            'next_step'
                        )
                            ->label(
                                'Next Step'
                            )
                            ->content(
                                $nextStepDisplay
                            )
                            ->columnSpan(3),

                    ])
                    ->columnSpanFull(),

                /*
                |--------------------------------------------------------------------------
                | Approval Information
                |--------------------------------------------------------------------------
                */

                Section::make(
                    'Approval Information'
                )
                    ->description(
                        'Current approval assignment and approval result.'
                    )
                    ->icon(
                        'heroicon-o-check-badge'
                    )
                    ->collapsible()
                    ->columns(12)
                    ->schema([

                        Placeholder::make(
                            'approval_master'
                        )
                            ->label(
                                'Approval Master'
                            )
                            ->content(
                                $masterName
                            )
                            ->columnSpan(3),

                        Placeholder::make(
                            'approval_level'
                        )
                            ->label(
                                'Approval Level'
                            )
                            ->content(
                                $currentStep
                                    ? 'Level '
                                        . $currentStep->approval_level
                                    : '-'
                            )
                            ->columnSpan(3),

                        Placeholder::make(
                            'current_approver'
                        )
                            ->label(
                                'Current Approver'
                            )
                            ->content(
                                $currentApproverDisplay
                            )
                            ->columnSpan(3),

                        Placeholder::make(
                            'approval_result'
                        )
                            ->label(
                                'Approval Result'
                            )
                            ->content(
                                $approvalResult
                            )
                            ->columnSpan(3),

                        Placeholder::make(
                            'approval_date'
                        )
                            ->label(
                                'Approval Date'
                            )
                            ->content(
                                $approvalDate
                            )
                            ->columnSpan(3),

                        Placeholder::make(
                            'approval_remarks'
                        )
                            ->label(
                                'Approval Remarks'
                            )
                            ->content(
                                $approvalRemarks
                            )
                            ->columnSpanFull(),

                    ])
                    ->columnSpanFull(),

                /*
                |--------------------------------------------------------------------------
                | Dynamic Approval Timeline
                |--------------------------------------------------------------------------
                */

                Section::make(
                    'Approval Timeline'
                )
                    ->description(
                        'Approval workflow generated from the active Approval Master transaction.'
                    )
                    ->icon(
                        'heroicon-o-clock'
                    )
                    ->collapsible()
                    ->columns(12)
                    ->schema(
                        $timelineComponents
                    )
                    ->columnSpanFull(),

                /*
                |--------------------------------------------------------------------------
                | Approval History
                |--------------------------------------------------------------------------
                */

                Section::make(
                    'Approval History'
                )
                    ->description(
                        'Approval activity log for this Material Requisition.'
                    )
                    ->icon(
                        'heroicon-o-document-duplicate'
                    )
                    ->collapsible()
                    ->collapsed()
                    ->columns(12)
                    ->schema([

                        Placeholder::make(
                            'submitted_by'
                        )
                            ->label(
                                'Submitted By'
                            )
                            ->content(
                                $record?->submittedBy?->name
                                    ?? '-'
                            )
                            ->columnSpan(3),

                        Placeholder::make(
                            'submitted_at'
                        )
                            ->label(
                                'Submitted At'
                            )
                            ->content(
                                $transaction?->submitted_at
                                    ? $transaction
                                        ->submitted_at
                                        ->timezone(
                                            config(
                                                'app.timezone',
                                                'Asia/Jakarta'
                                            )
                                        )
                                        ->format(
                                            'd M Y H:i'
                                        )
                                    : '-'
                            )
                            ->columnSpan(3),

                        Placeholder::make(
                            'last_action'
                        )
                            ->label(
                                'Last Action'
                            )
                            ->content(
                                $steps
                                    ->filter(
                                        fn (
                                            ApprovalTransactionStep $step
                                        ): bool =>
                                            filled(
                                                $step->acted_at
                                            )
                                    )
                                    ->sortByDesc(
                                        'acted_at'
                                    )
                                    ->first()
                                    ?->action
                                    ?? 'SUBMITTED'
                            )
                            ->columnSpan(3),

                        Placeholder::make(
                            'last_action_at'
                        )
                            ->label(
                                'Last Action At'
                            )
                            ->content(
                                $steps
                                    ->filter(
                                        fn (
                                            ApprovalTransactionStep $step
                                        ): bool =>
                                            filled(
                                                $step->acted_at
                                            )
                                    )
                                    ->sortByDesc(
                                        'acted_at'
                                    )
                                    ->first()
                                    ?->acted_at
                                    ?->timezone(
                                        config(
                                            'app.timezone',
                                            'Asia/Jakarta'
                                        )
                                    )
                                    ->format(
                                        'd M Y H:i'
                                    )
                                    ?? (
                                        $transaction?->submitted_at
                                            ?->timezone(
                                                config(
                                                    'app.timezone',
                                                    'Asia/Jakarta'
                                                )
                                            )
                                            ?->format(
                                                'd M Y H:i'
                                            )
                                    )
                                    ?? '-'
                            )
                            ->columnSpan(3),

                        Placeholder::make(
                            'last_comment'
                        )
                            ->label(
                                'Latest Comment'
                            )
                            ->content(
                                $steps
                                    ->filter(
                                        fn (
                                            ApprovalTransactionStep $step
                                        ): bool =>
                                            filled(
                                                $step->acted_at
                                            )
                                    )
                                    ->sortByDesc(
                                        'acted_at'
                                    )
                                    ->first()
                                    ?->remarks
                                    ?? '-'
                            )
                            ->columnSpanFull(),

                    ])
                    ->columnSpanFull(),

            ]);
    }
}