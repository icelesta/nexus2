<?php

declare(strict_types=1);

namespace App\Filament\Resources\AssignmentDirectMarketResource\Tables;

use App\Models\AssignmentDirectMarket;
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

use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Filters\TrashedFilter;
use Filament\Tables\Table;

use Illuminate\Database\Eloquent\Builder;

class AssignmentDirectMarketsTable
{
    public static function configure(
        Table $table
    ): Table {

        return $table

            /*
            |--------------------------------------------------------------------------
            | GLOBAL TRANSACTION FILTER
            |--------------------------------------------------------------------------
            */

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
                    Builder $query,
                    \App\Filament\Resources\AssignmentDirectMarketResource\Pages\ListAssignmentDirectMarkets $livewire,
                ): Builder {

                    /*
                    |--------------------------------------------------------------------------
                    | GLOBAL FILTER ACCESS
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
                    */

                    if (
                        $livewire->globalBranchFilter !== null
                    ) {

                        $query->where(
                            'assignment_direct_markets.branch_id',
                            $livewire->globalBranchFilter
                        );

                    }

                    /*
                    |--------------------------------------------------------------------------
                    | DEPARTMENT
                    |--------------------------------------------------------------------------
                    */

                    if (
                        $livewire->globalDepartmentFilter !== null
                    ) {

                        $query->where(
                            'assignment_direct_markets.department_id',
                            $livewire->globalDepartmentFilter
                        );

                    }

                    /*
                    |--------------------------------------------------------------------------
                    | DOCUMENT DATE FROM
                    |--------------------------------------------------------------------------
                    */

                    if (
                        filled(
                            $livewire->globalDateFrom
                        )
                    ) {

                        $from = Carbon::parse(
                            $livewire->globalDateFrom,
                            UserTimezone::timezone()
                        )
                            ->startOfDay()
                            ->toDateString();

                        $query->where(
                            'assignment_direct_markets.document_date',
                            '>=',
                            $from
                        );

                    }

                    /*
                    |--------------------------------------------------------------------------
                    | DOCUMENT DATE TO
                    |--------------------------------------------------------------------------
                    */

                    if (
                        filled(
                            $livewire->globalDateTo
                        )
                    ) {

                        $to = Carbon::parse(
                            $livewire->globalDateTo,
                            UserTimezone::timezone()
                        )
                            ->endOfDay()
                            ->toDateString();

                        $query->where(
                            'assignment_direct_markets.document_date',
                            '<=',
                            $to
                        );

                    }

                    /*
                    |--------------------------------------------------------------------------
                    | ADM STATUS
                    |--------------------------------------------------------------------------
                    */

                    if (
                        filled(
                            $livewire->globalStatusFilter
                        )
                    ) {

                        $query->where(
                            'assignment_direct_markets.status',
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
                'document_date',
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
                | DM NUMBER
                |--------------------------------------------------------------------------
                */

                TextColumn::make(
                    'directMarket.dm_no'
                )
                    ->label('DM No.')
                    ->weight('bold')
                    ->copyable()
                    ->tooltip(
                        'Click to copy DM Number'
                    )
                    ->searchable()
                    ->sortable()
                    ->placeholder('-'),


                /*
                |--------------------------------------------------------------------------
                | DEPARTMENT
                |--------------------------------------------------------------------------
                */

                TextColumn::make(
                    'department.display_name'
                )
                    ->label('Department')
                    ->searchable(
                        query: function ($query, string $search): void {

                            $query->whereHas(
                                'department',
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
                                        'assignment_direct_markets.department_id'
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
                    'directMarket.remarks'
                )
                    ->label('Remark')
                    ->searchable()
                    ->limit(45)
                    ->tooltip(
                        fn ($state): string =>
                            filled($state)
                                ? (string) $state
                                : '-'
                    )
                    ->placeholder('-'),


                /*
                |--------------------------------------------------------------------------
                | REQUESTED BY
                |--------------------------------------------------------------------------
                */

                TextColumn::make(
                    'requester.name'
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
                    'request_date'
                )
                    ->label('Request Date')
                    ->date('d M Y')
                    ->sortable()
                    ->placeholder('-'),

                /*
                |--------------------------------------------------------------------------
                | DM STATUS
                |--------------------------------------------------------------------------
                |
                | Display the COMPLETE Direct Market approval progress.
                |
                | Golden DM Flow:
                |
                |   Level 1 = Direct Market approval
                |   Level 2 = Assignment Direct Market approval
                |
                | Therefore this column must combine BOTH approval transactions.
                |
                |--------------------------------------------------------------------------
                */

                TextColumn::make(
                    'dm_approval_status'
                )
                    ->label('DM Status')
                    ->badge()
                    ->state(
                        function (
                            AssignmentDirectMarket $record
                        ): string {

                            $directMarket =
                                $record->directMarket;

                            if (! $directMarket) {
                                return '-';
                            }

                            /*
                            |--------------------------------------------------------------------------
                            | LEVEL 1
                            | DIRECT MARKET APPROVAL
                            |--------------------------------------------------------------------------
                            */

                            $directMarketTransaction =
                                \App\Models\ApprovalTransaction::query()
                                    ->with([
                                        'approvalMaster.steps',
                                        'steps',
                                    ])
                                    ->where(
                                        'document_type',
                                        'DIRECT_MARKET'
                                    )
                                    ->where(
                                        'document_id',
                                        $directMarket->getKey()
                                    )
                                    ->latest('id')
                                    ->first();

                            /*
                            |--------------------------------------------------------------------------
                            | LEVEL 2
                            | ASSIGNMENT DIRECT MARKET APPROVAL
                            |--------------------------------------------------------------------------
                            */

                            $assignmentTransaction =
                                \App\Models\ApprovalTransaction::query()
                                    ->with([
                                        'approvalMaster.steps',
                                        'steps',
                                    ])
                                    ->where(
                                        'document_type',
                                        'ASSIGNMENT_DIRECT_MARKET'
                                    )
                                    ->where(
                                        'document_id',
                                        $record->getKey()
                                    )
                                    ->latest('id')
                                    ->first();

                            /*
                            |--------------------------------------------------------------------------
                            | REJECTED
                            |--------------------------------------------------------------------------
                            */

                            if (
                                $directMarketTransaction?->status === 'REJECTED'
                                ||
                                $assignmentTransaction?->status === 'REJECTED'
                            ) {
                                return 'Rejected';
                            }

                            /*
                            |--------------------------------------------------------------------------
                            | LEVEL 1 APPROVED
                            |--------------------------------------------------------------------------
                            |
                            | The original Direct Market approval represents Level 1.
                            |
                            */

                            $level1Approved =
                                $directMarketTransaction?->status === 'APPROVED';

                            /*
                            |--------------------------------------------------------------------------
                            | LEVEL 2 APPROVED
                            |--------------------------------------------------------------------------
                            |
                            | The Assignment Direct Market approval represents Level 2.
                            |
                            */

                            $level2Approved =
                                $assignmentTransaction?->status === 'APPROVED';

                            /*
                            |--------------------------------------------------------------------------
                            | BOTH LEVELS APPROVED
                            |--------------------------------------------------------------------------
                            */

                            if (
                                $level1Approved
                                &&
                                $level2Approved
                            ) {
                                return 'Approval 2/2';
                            }

                            /*
                            |--------------------------------------------------------------------------
                            | LEVEL 1 APPROVED, LEVEL 2 WAITING
                            |--------------------------------------------------------------------------
                            |
                            | This is the current business condition:
                            |
                            |   DM Level 1     = APPROVED
                            |   ADM Level 2    = PENDING
                            |
                            | Therefore:
                            |
                            |   Approval 1/2
                            |
                            */

                            if ($level1Approved) {
                                return 'Approval 1/2';
                            }

                            /*
                            |--------------------------------------------------------------------------
                            | LEVEL 1 NOT YET APPROVED
                            |--------------------------------------------------------------------------
                            */

                            if (
                                $directMarketTransaction?->status === 'PENDING'
                            ) {
                                return 'Waiting Approval';
                            }

                            /*
                            |--------------------------------------------------------------------------
                            | COMPLETED SAFETY FALLBACK
                            |--------------------------------------------------------------------------
                            */

                            if (
                                $record->status ===
                                AssignmentDirectMarket::STATUS_COMPLETED
                            ) {
                                return 'Approval 2/2';
                            }

                            return (string) $directMarket->status;
                        }
                    )
                    ->color(
                        function (
                            string $state
                        ): string {

                            if ($state === 'Approval 2/2') {
                                return 'success';
                            }

                            if (
                                str_starts_with(
                                    $state,
                                    'Approval '
                                )
                            ) {
                                return 'warning';
                            }

                            return match ($state) {

                                'Approved' =>
                                    'success',

                                'Rejected' =>
                                    'danger',

                                'Waiting Approval' =>
                                    'warning',

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
                | ADM STATUS
                |--------------------------------------------------------------------------
                */

                TextColumn::make(
                    'status'
                )
                    ->label('Status ADM')
                    ->badge()
                    ->sortable()
                    ->searchable()
                    ->color(
                        fn (
                            string $state
                        ): string => match ($state) {

                            AssignmentDirectMarket::STATUS_DRAFT =>
                                'gray',

                            AssignmentDirectMarket::STATUS_SUBMITTED =>
                                'info',

                            AssignmentDirectMarket::STATUS_UPDATED =>
                                'info',

                            AssignmentDirectMarket::STATUS_WAITING_APPROVAL =>
                                'warning',

                            AssignmentDirectMarket::STATUS_APPROVED =>
                                'success',

                            AssignmentDirectMarket::STATUS_COMPLETED =>
                                'success',

                            AssignmentDirectMarket::STATUS_REJECTED =>
                                'danger',

                            AssignmentDirectMarket::STATUS_CANCELLED =>
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
                | ADM STATUS
                |--------------------------------------------------------------------------
                */

                SelectFilter::make(
                    'status'
                )
                    ->label('Status ADM')
                    ->options([

                        AssignmentDirectMarket::STATUS_DRAFT =>
                            'Draft',

                        AssignmentDirectMarket::STATUS_SUBMITTED =>
                            'Submitted',

                        AssignmentDirectMarket::STATUS_UPDATED =>
                            'Updated',

                        AssignmentDirectMarket::STATUS_WAITING_APPROVAL =>
                            'Waiting Approval',

                        AssignmentDirectMarket::STATUS_APPROVED =>
                            'Approved',

                        AssignmentDirectMarket::STATUS_COMPLETED =>
                            'Completed',

                        AssignmentDirectMarket::STATUS_REJECTED =>
                            'Rejected',

                        AssignmentDirectMarket::STATUS_CANCELLED =>
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
                | DIRECT MARKET
                |--------------------------------------------------------------------------
                */

                SelectFilter::make(
                    'direct_market_id'
                )
                    ->label('Direct Market')
                    ->relationship(
                        'directMarket',
                        'dm_no'
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
                    ->label('Assignment Date')
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
                                            )
                                                ->startOfDay()
                                                ->utc()
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
                                            )
                                                ->endOfDay()
                                                ->utc()
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
            | ADM follows AMR table interaction pattern.
            |
            | View
            | Edit
            | Delete
            |
            |--------------------------------------------------------------------------
            */

            ->actions([

                ActionGroup::make([

                    /*
                    |--------------------------------------------------------------------------
                    | VIEW
                    |--------------------------------------------------------------------------
                    */

                    ViewAction::make(),

                    /*
                    |--------------------------------------------------------------------------
                    | EDIT
                    |--------------------------------------------------------------------------
                    */

                    EditAction::make(),

                    /*
                    |--------------------------------------------------------------------------
                    | DELETE
                    |--------------------------------------------------------------------------
                    */

                    DeleteAction::make(),

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