<?php

declare(strict_types=1);

namespace App\Filament\Resources\PurchaseRequisitions\Tables;

use App\Filament\Resources\PurchaseRequisitions\Pages\PrintPurchaseRequisition;
use App\Models\ApprovalMaster;
use App\Models\ApprovalTransaction;
use App\Services\Purchasing\PurchaseRequisitionService;
use Filament\Actions\Action;
use Filament\Actions\ActionGroup;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Notifications\Notification;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Throwable;
use Filament\Support\Icons\Heroicon;

use App\Support\Timezone\UserTimezone;
use Carbon\Carbon;

class PurchaseRequisitionTable
{

    protected static function isLevelOneApprovalEditAllowed(
        Model $record,
    ): bool {
        if (
            $record->status !== \App\Models\PurchaseRequisition::STATUS_WAITING_APPROVAL
        ) {
            return false;
        }

        $user = auth()->user();

        if (! $user) {
            return false;
        }

        $transaction = ApprovalTransaction::query()
            ->where('document_type', 'MATERIAL_REQUISITION')
            ->where('document_id', $record->getKey())
            ->where('status', 'PENDING')
            ->latest('id')
            ->first();

        if (
            ! $transaction
            || (int) $transaction->current_level !== 1
        ) {
            return false;
        }

        $step = $transaction->steps()
            ->where('approval_level', 1)
            ->where('status', 'PENDING')
            ->first();

        if (! $step || ! $step->role_id) {
            return false;
        }

        return $user->roles()
            ->where('roles.id', $step->role_id)
            ->where('roles.guard_name', 'web')
            ->where('roles.is_active', true)
            ->exists();
    }

    
    public static function configure(
        Table $table,
    ): Table {

        return $table

            /*
            |--------------------------------------------------------------------------
            | GLOBAL TRANSACTION FILTER
            |--------------------------------------------------------------------------
            |
            | Display the reusable Global Transaction Filter in the
            | table header.
            |
            | IMPORTANT:
            |
            | This only renders the filter UI.
            | Existing MR visibility/security logic remains untouched.
            |
            */

            ->header(
                fn ($livewire) => view(
                    'filament.components.global-transaction-filters',
                    [
                        'livewire' => $livewire,
                    ]
                )
            )

            /*
            |--------------------------------------------------------------------------
            | DATA VISIBILITY
            |--------------------------------------------------------------------------
            |
            | Nexus ERP 2.0 Material Requisition Visibility Rule:
            |
            | 1. Super Admin
            |    -> All Material Requisitions
            |
            | 2. Purchasing PIC
            |    -> All Material Requisitions
            |
            | 3. Purchasing Manager
            |    -> All Material Requisitions
            |
            | 4. Approval PIC
            |    -> Material Requisitions currently waiting for
            |       approval by one of the user's active roles.
            |
            | 5. Ordinary Department User
            |    -> Only Material Requisitions belonging to
            |       the user's own Department.
            |
            |--------------------------------------------------------------------------
            */

            ->modifyQueryUsing(
                function (
                    Builder $query,
                    \App\Filament\Resources\PurchaseRequisitions\Pages\ListPurchaseRequisitions $livewire,
                ): Builder {

                    $user = auth()->user();

                    /*
                    |--------------------------------------------------------------------------
                    | SAFETY
                    |--------------------------------------------------------------------------
                    */

                    if (! $user) {

                        return $query->whereRaw('1 = 0');

                    }


                    /*
                    |--------------------------------------------------------------------------
                    | GTF-1.1-C
                    | GLOBAL TRANSACTION FILTER — MR
                    |--------------------------------------------------------------------------
                    |
                    | Only users allowed to use Global Transaction Filters
                    | can apply these filters.
                    |
                    */

                    if ($livewire->canUseGlobalTransactionFilters()) {

                        /*
                        |--------------------------------------------------------------------------
                        | Branch
                        |--------------------------------------------------------------------------
                        */

                        if ($livewire->globalBranchFilter !== null) {

                            $query->where(
                                'purchase_requisitions.branch_id',
                                $livewire->globalBranchFilter
                            );

                        }


                        /*
                        |--------------------------------------------------------------------------
                        | Department
                        |--------------------------------------------------------------------------
                        */

                        if ($livewire->globalDepartmentFilter !== null) {

                            $query->where(
                                'purchase_requisitions.department_id',
                                $livewire->globalDepartmentFilter
                            );

                        }


                        /*
                        |--------------------------------------------------------------------------
                        | Request Date From
                        |--------------------------------------------------------------------------
                        */

                        if (filled($livewire->globalDateFrom)) {

                            $from = Carbon::parse(
                                $livewire->globalDateFrom,
                                UserTimezone::timezone()
                            )->startOfDay()->utc();

                            $query->where(
                                'purchase_requisitions.request_date',
                                '>=',
                                $from
                            );

                        }


                        /*
                        |--------------------------------------------------------------------------
                        | Request Date To
                        |--------------------------------------------------------------------------
                        */

                        if (filled($livewire->globalDateTo)) {

                            $to = Carbon::parse(
                                $livewire->globalDateTo,
                                UserTimezone::timezone()
                            )->endOfDay()->utc();

                            $query->where(
                                'purchase_requisitions.request_date',
                                '<=',
                                $to
                            );

                        }


                        /*
                        |--------------------------------------------------------------------------
                        | MR Status
                        |--------------------------------------------------------------------------
                        */

                        if (filled($livewire->globalStatusFilter)) {

                            $query->where(
                                'purchase_requisitions.status',
                                $livewire->globalStatusFilter
                            );

                        }

                    }


                    return $query;

                }
            )

            /*
            |--------------------------------------------------------------------------
            | COLUMNS
            |--------------------------------------------------------------------------
            */

            ->columns([

                TextColumn::make('pr_no')
                    ->label('PR Number')
                    ->searchable()
                    ->sortable(),

                /*
                |--------------------------------------------------------------------------
                | APPROVAL STATUS
                |--------------------------------------------------------------------------
                |
                | Nexus ERP Approval Display Standard:
                |
                | Draft
                | Approval 1/2
                | Approval 2/2
                |
                | The total number of approval levels is dynamically
                | retrieved from the active MR Approval Master.
                |
                | IMPORTANT:
                |
                | The database status is NOT modified here.
                | This is presentation / workflow progress only.
                |
                */

                TextColumn::make('status')
                    ->label('Status')
                    ->badge()
                    ->sortable()
                    ->searchable()
                    ->alignCenter()

                    ->getStateUsing(
                        function (Model $record): string {

                            /*
                            |--------------------------------------------------------------------------
                            | Terminal / Non-Approval States
                            |--------------------------------------------------------------------------
                            */

                            if (
                                in_array(
                                    $record->status,
                                    [
                                        'Draft',
                                        'Rejected',
                                        'Cancelled',
                                        'Closed',
                                    ],
                                    true
                                )
                            ) {
                                return $record->status;
                            }


                            /*
                            |--------------------------------------------------------------------------
                            | Approval Master
                            |--------------------------------------------------------------------------
                            |
                            | Total approval levels are always determined dynamically
                            | from the active Material Requisition Approval Master.
                            |
                            */

                            $totalApprovals =
                                ApprovalMaster::query()
                                    ->active()
                                    ->where(
                                        'code',
                                        'MR-APPROVAL'
                                    )
                                    ->withCount([
                                        'steps as required_approval_count' =>
                                            fn ($query) =>
                                                $query->where(
                                                    'is_required',
                                                    true
                                                ),
                                    ])
                                    ->value(
                                        'required_approval_count'
                                    );

                            $totalApprovals =
                                max(
                                    1,
                                    (int) $totalApprovals
                                );


                            /*
                            |--------------------------------------------------------------------------
                            | Approval Transaction
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
                                    ->first();


                            /*
                            |--------------------------------------------------------------------------
                            | No Approval Transaction
                            |--------------------------------------------------------------------------
                            */

                            if (! $transaction) {
                                return $record->status;
                            }


                            /*
                            |--------------------------------------------------------------------------
                            | Rejected
                            |--------------------------------------------------------------------------
                            */

                            if (
                                $transaction->status === 'REJECTED'
                            ) {
                                return 'Rejected';
                            }


                            /*
                            |--------------------------------------------------------------------------
                            | Count Completed Approval Levels
                            |--------------------------------------------------------------------------
                            |
                            | IMPORTANT:
                            |
                            | We do NOT use current_level as the completed
                            | approval count.
                            |
                            | current_level means the level currently waiting
                            | for action, not the number of approvals completed.
                            |
                            */

                            $approvedCount =
                                $transaction->steps()
                                    ->where(
                                        'status',
                                        'APPROVED'
                                    )
                                    ->count();


                            /*
                            |--------------------------------------------------------------------------
                            | No Approval Completed Yet
                            |--------------------------------------------------------------------------
                            |
                            | Transaction exists and is waiting for the
                            | first approver.
                            |
                            */

                            if (
                                $transaction->status === 'PENDING'
                                && $approvedCount === 0
                            ) {
                                return 'Waiting Approval';
                            }


                            /*
                            |--------------------------------------------------------------------------
                            | Approval Progress
                            |--------------------------------------------------------------------------
                            |
                            | Example:
                            |
                            | 1 approved of 2
                            | => Approval 1/2
                            |
                            | 2 approved of 2
                            | => Approval 2/2
                            |
                            */

                            if (
                                $transaction->status === 'PENDING'
                                || $transaction->status === 'APPROVED'
                            ) {

                                $completed =
                                    min(
                                        $approvedCount,
                                        $totalApprovals
                                    );

                                if ($completed > 0) {
                                    return sprintf(
                                        'Approval %d/%d',
                                        $completed,
                                        $totalApprovals
                                    );
                                }
                            }


                            /*
                            |--------------------------------------------------------------------------
                            | Fallback
                            |--------------------------------------------------------------------------
                            */

                            return $record->status;
                        }
                    )

                    ->color(
                        function (
                            string $state
                        ): string {

                            if ($state === 'Draft') {
                                return 'gray';
                            }

                            if (
                                str_starts_with(
                                    $state,
                                    'Approval '
                                )
                            ) {

                                /*
                                |--------------------------------------------------------------------------
                                | Completed Final Approval
                                |--------------------------------------------------------------------------
                                */

                                if (
                                    preg_match(
                                        '/Approval (\d+)\/(\d+)/',
                                        $state,
                                        $matches
                                    )
                                ) {

                                    $current =
                                        (int) $matches[1];

                                    $total =
                                        (int) $matches[2];

                                    if (
                                        $current >= $total
                                    ) {
                                        return 'success';
                                    }
                                }

                                return 'warning';
                            }

                            return match ($state) {
                                'Rejected'  => 'danger',
                                'Cancelled' => 'danger',
                                'Closed'    => 'primary',
                                default     => 'gray',
                            };
                        }
                    ),

                TextColumn::make('request_date')
                    ->label('Request Date')
                    ->date()
                    ->sortable(),

                TextColumn::make('required_date')
                    ->label('Required Date')
                    ->date()
                    ->sortable(),

                TextColumn::make('branch.branch_name')
                    ->label('Branch'),

                TextColumn::make('department.display_name')
                    ->label('Department'),

                TextColumn::make('requester.name')
                    ->label('Requester'),

                TextColumn::make('items_count')
                    ->counts('items')
                    ->label('Items')
                    ->badge()
                    ->color('primary')
                    ->alignCenter(),

                TextColumn::make('createdBy.name')
                    ->label('Created By'),

            ])

            /*
            |--------------------------------------------------------------------------
            | DEFAULT SORT
            |--------------------------------------------------------------------------
            */

            ->defaultSort(
                'created_at',
                'desc',
            )

            /*
            |--------------------------------------------------------------------------
            | FILTERS
            |--------------------------------------------------------------------------
            */

            ->filters([

                SelectFilter::make('document_type')
                    ->label('Document Type'),

                Filter::make('document_date')
                    ->label('Document Date'),

                Filter::make('required_date')
                    ->label('Required Date'),

                SelectFilter::make('company_id')
                    ->label('Company'),

                SelectFilter::make('business_unit_id')
                    ->label('Business Unit'),

                SelectFilter::make('department_id')
                    ->label('Department'),

                SelectFilter::make('warehouse_id')
                    ->label('Warehouse'),

                SelectFilter::make('requester_id')
                    ->label('Requester'),

                SelectFilter::make('requested_for_id')
                    ->label('Requested For'),

                SelectFilter::make('priority')
                    ->label('Priority'),

                SelectFilter::make('status')
                    ->label('Document Status'),

                SelectFilter::make('approval_status')
                    ->label('Approval Status'),

            ])

            /*
            |--------------------------------------------------------------------------
            | RECORD ACTIONS
            |--------------------------------------------------------------------------
            */

            ->recordActions([

                ActionGroup::make([

                    ViewAction::make(),

                    Action::make('preview')
                        ->label('Preview MR')
                        ->icon('heroicon-o-document')
                        ->color('primary')
                        ->url(
                            fn (Model $record): string =>
                                PrintPurchaseRequisition::getUrl([
                                    'record' => $record,
                                ]),
                        ),

                    EditAction::make()
                        ->icon(
                            fn (Model $record): string =>
                                $record->status === 'Draft'
                                || self::isLevelOneApprovalEditAllowed($record)
                                    ? 'heroicon-o-pencil-square'
                                    : 'heroicon-o-lock-closed'
                        )
                        ->disabled(
                            fn (Model $record): bool =>
                                $record->status !== 'Draft'
                                && ! self::isLevelOneApprovalEditAllowed($record)
                        )
                        ->tooltip(
                            fn (Model $record): ?string =>
                                $record->status !== 'Draft'
                                && ! self::isLevelOneApprovalEditAllowed($record)
                                    ? 'Material Requisition cannot be edited after submission.'
                                    : (
                                        $record->status === 'Draft'
                                            ? 'Edit Material Requisition'
                                            : 'Edit quantity before Level 1 approval'
                                    )
                        ),

                    Action::make('submit')
                        ->label('Submit')
                        ->icon('heroicon-o-paper-airplane')
                        ->color('success')

                        ->visible(
                            fn (Model $record): bool =>
                                $record->status === 'Draft'
                                && $record->items()->exists()
                        )

                        ->requiresConfirmation()

                        ->modalHeading(
                            'Submit Material Requisition'
                        )

                        ->modalDescription(
                            fn (Model $record): string =>
                                "Submit Material Requisition {$record->pr_no} for approval?"
                        )

                        ->modalSubmitActionLabel(
                            'Submit for Approval'
                        )

                        ->action(
                            function (Model $record): void {

                                try {

                                    if (! $record->items()->exists()) {

                                        Notification::make()
                                            ->danger()
                                            ->title(
                                                'Cannot submit Material Requisition'
                                            )
                                            ->body(
                                                'Please add at least one Material Requisition Item before submitting.'
                                            )
                                            ->send();

                                        return;
                                    }

                                    app(
                                        PurchaseRequisitionService::class
                                    )->submit(
                                        $record->getKey()
                                    );

                                    Notification::make()
                                        ->success()
                                        ->title(
                                            'Material Requisition submitted'
                                        )
                                        ->body(
                                            "Material Requisition {$record->pr_no} has been submitted for approval."
                                        )
                                        ->send();

                                } catch (Throwable $exception) {

                                    Notification::make()
                                        ->danger()
                                        ->title(
                                            'Unable to submit Material Requisition'
                                        )
                                        ->body(
                                            $exception->getMessage()
                                        )
                                        ->send();
                                }
                            }
                        ),

                    DeleteAction::make()
                        ->icon(
                            fn (Model $record): string =>
                                $record->status === 'Draft'
                                    ? 'heroicon-o-trash'
                                    : 'heroicon-o-lock-closed'
                        )
                        ->disabled(
                            fn (Model $record): bool =>
                                $record->status !== 'Draft'
                        )
                        ->tooltip(
                            fn (Model $record): ?string =>
                                $record->status !== 'Draft'
                                    ? 'Material Requisition cannot be deleted after submission.'
                                    : 'Delete Material Requisition'
                        ),

                ])

                    ->label('Actions')
                    ->button()
                    ->color('warning')
                    ->icon(Heroicon::OutlinedEllipsisVertical),

            ])

            /*
            |--------------------------------------------------------------------------
            | TOOLBAR ACTIONS
            |--------------------------------------------------------------------------
            */

            ->toolbarActions([

                BulkActionGroup::make([

                    DeleteBulkAction::make(),

                ]),

            ]);
    }
}