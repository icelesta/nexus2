<?php

declare(strict_types=1);

namespace App\Filament\Resources\AssignmentMaterialRequisitionResource\Tables;

use App\Models\ApprovalMaster;
use App\Models\ApprovalTransaction;
use App\Models\AssignmentMaterialRequisition;
use App\Models\Department;

use App\Support\Timezone\UserTimezone;
use Carbon\Carbon;

use Filament\Actions\ActionGroup;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ForceDeleteBulkAction;
use Filament\Actions\RestoreBulkAction;
use Filament\Actions\ViewAction;

use Filament\Forms\Components\DatePicker;

use Filament\Support\Enums\FontWeight;

use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Filters\TrashedFilter;
use Filament\Tables\Table;

use Illuminate\Database\Eloquent\Model;


class AssignmentMaterialRequisitionsTable
{
    public static function configure(
        Table $table
    ): Table {

        return $table


            ->header(
                fn ($livewire) => view(
                    'filament.components.global-transaction-filters',
                    [
                        'livewire' => $livewire,
                    ]
                )
            )

            ->modifyQueryUsing(
                function (
                    \Illuminate\Database\Eloquent\Builder $query,
                    \App\Filament\Resources\AssignmentMaterialRequisitionResource\Pages\ListAssignmentMaterialRequisitions $livewire,
                ): \Illuminate\Database\Eloquent\Builder {

                    /*
                    |--------------------------------------------------------------------------
                    | GLOBAL TRANSACTION FILTER
                    |--------------------------------------------------------------------------
                    */

                    if (
                        ! $livewire->canUseGlobalTransactionFilters()
                    ) {
                        return $query;
                    }


                    /*
                    |--------------------------------------------------------------------------
                    | BRANCH
                    |--------------------------------------------------------------------------
                    |
                    | AMR does not store branch directly.
                    | Branch belongs to the source Material Requisition.
                    |
                    */

                    if (
                        $livewire->globalBranchFilter !== null
                    ) {

                        $query->whereHas(
                            'purchaseRequisition',
                            function (
                                \Illuminate\Database\Eloquent\Builder $mrQuery
                            ) use ($livewire): void {

                                $mrQuery->where(
                                    'branch_id',
                                    $livewire->globalBranchFilter
                                );

                            }
                        );

                    }


                    /*
                    |--------------------------------------------------------------------------
                    | DEPARTMENT
                    |--------------------------------------------------------------------------
                    |
                    | AMR Department is inherited from Material Requisition.
                    |
                    */

                    if (
                        $livewire->globalDepartmentFilter !== null
                    ) {

                        $query->whereHas(
                            'purchaseRequisition',
                            function (
                                \Illuminate\Database\Eloquent\Builder $mrQuery
                            ) use ($livewire): void {

                                $mrQuery->where(
                                    'department_id',
                                    $livewire->globalDepartmentFilter
                                );

                            }
                        );

                    }


                    /*
                    |--------------------------------------------------------------------------
                    | ASSIGNMENT DATE FROM
                    |--------------------------------------------------------------------------
                    */

                    if (
                        filled($livewire->globalDateFrom)
                    ) {

                        $from = Carbon::parse(
                            $livewire->globalDateFrom,
                            UserTimezone::timezone()
                        )->startOfDay()->utc();

                        $query->where(
                            'assignment_material_requisitions.assigned_at',
                            '>=',
                            $from
                        );

                    }

                    /*
                    |--------------------------------------------------------------------------
                    | ASSIGNMENT DATE TO
                    |--------------------------------------------------------------------------
                    */

                    if (
                        filled($livewire->globalDateTo)
                    ) {

                        $to = Carbon::parse(
                            $livewire->globalDateTo,
                            UserTimezone::timezone()
                        )->endOfDay()->utc();

                        $query->where(
                            'assignment_material_requisitions.assigned_at',
                            '<=',
                            $to
                        );

                    }


                    /*
                    |--------------------------------------------------------------------------
                    | AMR STATUS
                    |--------------------------------------------------------------------------
                    */

                    if (
                        filled($livewire->globalStatusFilter)
                    ) {

                        $query->where(
                            'assignment_material_requisitions.status',
                            $livewire->globalStatusFilter
                        );

                    }


                    return $query;
                }
            )
            

            /*
            |--------------------------------------------------------------------------
            | DEFAULT SORT
            |--------------------------------------------------------------------------
            */

            ->defaultSort(
                'assigned_at',
                'desc'
            )

            /*
            |--------------------------------------------------------------------------
            | COLUMNS
            |--------------------------------------------------------------------------
            */

            ->columns([

                /*
                |--------------------------------------------------------------------------
                | PR NUMBER
                |--------------------------------------------------------------------------
                */

                TextColumn::make(
                    'purchaseRequisition.pr_no'
                )
                    ->label('PR Number')
                    ->weight(
                        FontWeight::SemiBold
                    )
                    ->copyable()
                    ->tooltip(
                        'Click to copy PR Number'
                    )
                    ->searchable()
                    ->sortable(),

                /*
                |--------------------------------------------------------------------------
                | DEPARTMENT
                |--------------------------------------------------------------------------
                */

                TextColumn::make(
                    'purchaseRequisition.department.display_name'
                )
                    ->label('Department')
                    ->searchable(
                        query: function ($query, string $search): void {

                            $query->whereHas(
                                'purchaseRequisition.department',
                                function ($query) use ($search): void {

                                    $query
                                        ->where(
                                            'department_code',
                                            'like',
                                            "%{$search}%"
                                        )
                                        ->orWhere(
                                            'department_name',
                                            'like',
                                            "%{$search}%"
                                        );
                                }
                            );
                        }
                    )
                    ->sortable(
                        query: function ($query, string $direction): void {

                            $query->orderBy(
                                Department::query()
                                    ->select('department_name')
                                    ->whereColumn(
                                        'departments.id',
                                        'purchase_requisitions.department_id'
                                    ),
                                $direction
                            );
                        }
                    )
                    ->placeholder('-'),

                /*
                |--------------------------------------------------------------------------
                | REMARK
                |--------------------------------------------------------------------------
                */

                TextColumn::make(
                    'purchaseRequisition.remarks'
                )
                    ->label('Remark')
                    ->searchable()
                    ->limit(45)
                    ->tooltip(
                        fn ($record): ?string =>
                            $record
                                ->purchaseRequisition
                                ?->remarks
                    )
                    ->placeholder('-'),

                /*
                |--------------------------------------------------------------------------
                | REQUESTED BY
                |--------------------------------------------------------------------------
                */

                TextColumn::make(
                    'purchaseRequisition.requester.name'
                )
                    ->label('Requested By')
                    ->searchable()
                    ->sortable()
                    ->placeholder('-'),

                /*
                |--------------------------------------------------------------------------
                | REQUEST DATE
                |--------------------------------------------------------------------------
                */

                TextColumn::make(
                    'purchaseRequisition.request_date'
                )
                    ->label('Request Date')
                    ->date('d M Y')
                    ->sortable()
                    ->placeholder('-'),

                /*
                |--------------------------------------------------------------------------
                | MR STATUS
                |--------------------------------------------------------------------------
                |
                | The MR workflow status is derived from:
                |
                | Approval Master
                |        +
                | Approval Transaction
                |        +
                | Approval Transaction Steps
                |
                | Examples:
                |
                | Draft
                | Waiting Approval
                | Approval 1/2
                | Approval 2/2
                | Approval 1/3
                | Approval 2/3
                | Approval 3/3
                |
                */

                TextColumn::make(
                    'mr_approval_status'
                )
                    ->label('MR Status')
                    ->badge()
                    ->state(
                        function (
                            Model $record
                        ): string {

                            $purchaseRequisition =
                                $record->purchaseRequisition;

                            /*
                            |--------------------------------------------------------------------------
                            | No MR
                            |--------------------------------------------------------------------------
                            */

                            if (! $purchaseRequisition) {
                                return '-';
                            }

                            /*
                            |--------------------------------------------------------------------------
                            | Terminal / Initial MR Status
                            |--------------------------------------------------------------------------
                            */

                            if (
                                in_array(
                                    $purchaseRequisition->status,
                                    [
                                        'Draft',
                                        'Rejected',
                                        'Cancelled',
                                        'Closed',
                                    ],
                                    true
                                )
                            ) {
                                return $purchaseRequisition->status;
                            }

                            /*
                            |--------------------------------------------------------------------------
                            | Active MR Approval Master
                            |--------------------------------------------------------------------------
                            |
                            | Approval levels are dynamically read
                            | from Approval Master.
                            |
                            */

                            static $totalApprovals = null;

                            if (
                                $totalApprovals === null
                            ) {

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
                            }

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
                                        $purchaseRequisition->getKey()
                                    )
                                    ->latest('id')
                                    ->first();

                            /*
                            |--------------------------------------------------------------------------
                            | No Approval Transaction
                            |--------------------------------------------------------------------------
                            */

                            if (! $transaction) {

                                if (
                                    $purchaseRequisition->status
                                    === 'Pending Approval'
                                ) {
                                    return 'Waiting Approval';
                                }

                                return $purchaseRequisition->status;
                            }

                            /*
                            |--------------------------------------------------------------------------
                            | Rejected
                            |--------------------------------------------------------------------------
                            */

                            if (
                                $transaction->status
                                === 'REJECTED'
                            ) {
                                return 'Rejected';
                            }

                            /*
                            |--------------------------------------------------------------------------
                            | Count Approved Steps
                            |--------------------------------------------------------------------------
                            |
                            | Do NOT use current_level as the
                            | displayed approval progress.
                            |
                            | We count completed approval steps.
                            |
                            */

                            $approvedCount =
                                $transaction
                                    ->steps()
                                    ->where(
                                        'status',
                                        'APPROVED'
                                    )
                                    ->count();

                            /*
                            |--------------------------------------------------------------------------
                            | No Approval Completed
                            |--------------------------------------------------------------------------
                            */

                            if (
                                $approvedCount === 0
                            ) {
                                return 'Waiting Approval';
                            }

                            /*
                            |--------------------------------------------------------------------------
                            | Approval Progress
                            |--------------------------------------------------------------------------
                            */

                            return sprintf(
                                'Approval %d/%d',
                                min(
                                    $approvedCount,
                                    $totalApprovals
                                ),
                                $totalApprovals
                            );
                        }
                    )
                    ->color(
                        function (
                            string $state
                        ): string {

                            /*
                            |--------------------------------------------------------------------------
                            | Empty
                            |--------------------------------------------------------------------------
                            */

                            if (
                                $state === '-'
                            ) {
                                return 'gray';
                            }

                            /*
                            |--------------------------------------------------------------------------
                            | Draft
                            |--------------------------------------------------------------------------
                            */

                            if (
                                $state === 'Draft'
                            ) {
                                return 'gray';
                            }

                            /*
                            |--------------------------------------------------------------------------
                            | Waiting Approval
                            |--------------------------------------------------------------------------
                            */

                            if (
                                $state === 'Waiting Approval'
                            ) {
                                return 'warning';
                            }

                            /*
                            |--------------------------------------------------------------------------
                            | Approval Progress
                            |--------------------------------------------------------------------------
                            */

                            if (
                                str_starts_with(
                                    $state,
                                    'Approval '
                                )
                            ) {

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

                            /*
                            |--------------------------------------------------------------------------
                            | Other States
                            |--------------------------------------------------------------------------
                            */

                            return match ($state) {

                                'Rejected' =>
                                    'danger',

                                'Cancelled' =>
                                    'danger',

                                'Closed' =>
                                    'primary',

                                default =>
                                    'gray',

                            };
                        }
                    ),

                /*
                |--------------------------------------------------------------------------
                | ASSIGNMENT DATE
                |--------------------------------------------------------------------------
                */

                TextColumn::make(
                    'assigned_at'
                )
                    ->label('Assignment Date')
                    ->date('d M Y')
                    ->sortable()
                    ->placeholder('-'),

                /*
                |--------------------------------------------------------------------------
                | AMR STATUS
                |--------------------------------------------------------------------------
                */

                TextColumn::make(
                    'status'
                )
                    ->label('Status AMR')
                    ->badge()
                    ->sortable()
                    ->searchable()
                    ->color(
                        fn (
                            string $state
                        ): string => match ($state) {

                            'Draft' =>
                                'gray',

                            'Assigned' =>
                                'info',

                            'Waiting Approval' =>
                                'warning',

                            'Approved' =>
                                'success',

                            'Completed' =>
                                'success',

                            'Rejected' =>
                                'danger',

                            'Cancelled' =>
                                'gray',

                            default =>
                                'gray',

                        }
                    ),

            ])

            /*
            |--------------------------------------------------------------------------
            | FILTERS
            |--------------------------------------------------------------------------
            */

            ->filters([

                /*
                |--------------------------------------------------------------------------
                | AMR STATUS
                |--------------------------------------------------------------------------
                */

                SelectFilter::make(
                    'status'
                )
                    ->label('Status AMR')
                    ->options([

                        'Draft' =>
                            'Draft',

                        'Assigned' =>
                            'Assigned',

                        'Waiting Approval' =>
                            'Waiting Approval',

                        'Approved' =>
                            'Approved',

                        'Completed' =>
                            'Completed',

                        'Rejected' =>
                            'Rejected',

                        'Cancelled' =>
                            'Cancelled',

                    ]),

                /*
                |--------------------------------------------------------------------------
                | ASSIGNED TO
                |--------------------------------------------------------------------------
                */

                SelectFilter::make(
                    'assigned_to'
                )
                    ->label('Assigned To')
                    ->relationship(
                        'assignedTo',
                        'name'
                    )
                    ->searchable()
                    ->preload(),

                /*
                |--------------------------------------------------------------------------
                | PURCHASE REQUISITION
                |--------------------------------------------------------------------------
                */

                SelectFilter::make(
                    'purchase_requisition_id'
                )
                    ->label(
                        'Purchase Requisition'
                    )
                    ->relationship(
                        'purchaseRequisition',
                        'pr_no'
                    )
                    ->searchable()
                    ->preload(),

                /*
                |--------------------------------------------------------------------------
                | ASSIGNMENT DATE
                |--------------------------------------------------------------------------
                */

                Filter::make(
                    'assigned_at'
                )
                    ->label(
                        'Assignment Date'
                    )
                    ->schema([

                        DatePicker::make(
                            'from'
                        )
                            ->label('From'),

                        DatePicker::make(
                            'until'
                        )
                            ->label('Until'),

                    ])

                    ->query(
                        function (
                            $query,
                            array $data
                        ) {

                            return $query

                                ->when(
                                    $data['from'] ?? null,
                                    fn (
                                        $query,
                                        $date
                                    ) =>
                                        $query->where(
                                            'assigned_at',
                                            '>=',
                                            Carbon::parse(
                                                $date,
                                                UserTimezone::timezone()
                                            )->startOfDay()->utc()
                                        )
                                )

                                ->when(
                                    $data['until'] ?? null,
                                    fn (
                                        $query,
                                        $date
                                    ) =>
                                        $query->where(
                                            'assigned_at',
                                            '<=',
                                            Carbon::parse(
                                                $date,
                                                UserTimezone::timezone()
                                            )->endOfDay()->utc()
                                        )
                                );

                        }
                    ),

                /*
                |--------------------------------------------------------------------------
                | TRASHED
                |--------------------------------------------------------------------------
                */

                TrashedFilter::make(),

            ])

            /*
            |--------------------------------------------------------------------------
            | ROW ACTIONS
            |--------------------------------------------------------------------------
            |
            | AMR is no longer an approval workspace.
            |
            | AMR responsibilities:
            |
            | Draft
            |   ↓
            | Updated
            |   ↓
            | Submit
            |   ↓
            | Waiting Approval
            |
            | Therefore:
            |
            | NO Approve
            | NO Reject
            | NO Complete
            | NO Generate PO
            |
            | Approval and Generate PO are handled
            | by the next workflow workspace.
            |
            */

            ->actions([

                ActionGroup::make([

                    /*
                    |--------------------------------------------------------------------------
                    | View
                    |--------------------------------------------------------------------------
                    */

                    ViewAction::make(),

                    /*
                    |--------------------------------------------------------------------------
                    | Edit
                    |--------------------------------------------------------------------------
                    */

                    EditAction::make(),

                    /*
                    |--------------------------------------------------------------------------
                    | Delete
                    |--------------------------------------------------------------------------
                    */

                    DeleteAction::make()
                        ->visible(
                            fn (
                                AssignmentMaterialRequisition $record
                            ): bool =>
                                $record->canDelete()
                        ),

                ])
                    ->label('Actions')
                    ->button()
                    ->color('warning'),

            ])

            /*
            |--------------------------------------------------------------------------
            | BULK ACTIONS
            |--------------------------------------------------------------------------
            */

            ->bulkActions([

                BulkActionGroup::make([

                    DeleteBulkAction::make(),

                    ForceDeleteBulkAction::make(),

                    RestoreBulkAction::make(),

                ]),

            ]);
    }
}