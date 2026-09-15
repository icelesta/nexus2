<?php

declare(strict_types=1);

namespace App\Filament\Resources\DirectMarkets\Tables;

use App\Filament\Resources\DirectMarkets\Pages\PrintDirectMarket;
use Filament\Actions\Action;

use App\Filament\Resources\DirectMarkets\Pages\ListDirectMarkets;
use App\Models\DirectMarket;
use App\Models\AssignmentDirectMarket;
use App\Support\Timezone\UserTimezone;

use Carbon\Carbon;

use App\Services\Purchasing\DirectMarketService;
use Filament\Notifications\Notification;

use Filament\Actions\ActionGroup;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;

use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Filters\TrashedFilter;
use Filament\Tables\Table;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

class DirectMarketTable
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
                    ListDirectMarkets $livewire,
                ): Builder {

                    /*
                    |--------------------------------------------------------------------------
                    | DATA VISIBILITY
                    |--------------------------------------------------------------------------
                    |
                    | Keep Direct Market visibility aligned with Material Requisition:
                    | privileged purchasing roles may see all records; ordinary users
                    | may only see Direct Markets belonging to their own department.
                    |
                    | This is a visibility scope only. It does not change the DM
                    | workflow, permissions, actions, or business services.
                    |--------------------------------------------------------------------------
                    */

                    $user = auth()->user();

                    if (! $user) {
                        return $query->whereRaw('1 = 0');
                    }

                    if (
                        ! $user->hasAnyRole([
                            'Super Admin',
                            'Administrator',
                            'Purchasing PIC',
                            'Purchasing Manager',
                        ])
                    ) {
                        if ($user->department_id) {
                            $query->where(
                                'direct_markets.department_id',
                                $user->department_id
                            );
                        } else {
                            return $query->whereRaw('1 = 0');
                        }
                    }

                    /*
                    |--------------------------------------------------------------------------
                    | GTF ACCESS
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
                            'direct_markets.branch_id',
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
                            'direct_markets.department_id',
                            $livewire->globalDepartmentFilter
                        );
                    }

                    /*
                    |--------------------------------------------------------------------------
                    | REQUEST DATE FROM
                    |--------------------------------------------------------------------------
                    */

                    if (
                        filled($livewire->globalDateFrom)
                    ) {

                        $from = Carbon::parse(
                            $livewire->globalDateFrom,
                            UserTimezone::timezone()
                        )
                            ->startOfDay()
                            ->utc();

                        $query->where(
                            'direct_markets.request_date',
                            '>=',
                            $from
                        );
                    }

                    /*
                    |--------------------------------------------------------------------------
                    | REQUEST DATE TO
                    |--------------------------------------------------------------------------
                    */

                    if (
                        filled($livewire->globalDateTo)
                    ) {

                        $to = Carbon::parse(
                            $livewire->globalDateTo,
                            UserTimezone::timezone()
                        )
                            ->endOfDay()
                            ->utc();

                        $query->where(
                            'direct_markets.request_date',
                            '<=',
                            $to
                        );
                    }

                    /*
                    |--------------------------------------------------------------------------
                    | DM STATUS
                    |--------------------------------------------------------------------------
                    */

                    if (
                        filled($livewire->globalStatusFilter)
                    ) {

                        $query->where(
                            'direct_markets.status',
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
                'request_date',
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

                TextColumn::make('dm_no')
                    ->label('DM Number')
                    ->weight('bold')
                    ->copyable()
                    ->tooltip(
                        'Click to copy DM Number'
                    )
                    ->searchable()
                    ->sortable(),

                /*
                |--------------------------------------------------------------------------
                | BRANCH
                |--------------------------------------------------------------------------
                */

                TextColumn::make(
                    'branch.branch_name'
                )
                    ->label('Branch')
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
                    ->searchable()
                    ->sortable()
                    ->placeholder('-'),

                /*
                |--------------------------------------------------------------------------
                | REQUESTER
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

                TextColumn::make('request_date')
                    ->label('Request Date')
                    ->date('d M Y')
                    ->sortable()
                    ->placeholder('-'),

                /*
                |--------------------------------------------------------------------------
                | REQUIRED DATE
                |--------------------------------------------------------------------------
                */

                TextColumn::make('required_date')
                    ->label('Required Date')
                    ->date('d M Y')
                    ->sortable()
                    ->placeholder('-'),

                /*
                |--------------------------------------------------------------------------
                | ITEMS
                |--------------------------------------------------------------------------
                */

                TextColumn::make('items_count')
                    ->counts('items')
                    ->label('Items')
                    ->badge()
                    ->color('primary')
                    ->alignCenter(),
                    
                /*
                |--------------------------------------------------------------------------
                | STATUS
                |--------------------------------------------------------------------------
                |
                | GOLDEN DM STATUS
                |
                | Level 1 = DIRECT_MARKET
                | Level 2 = ASSIGNMENT_DIRECT_MARKET
                |
                | Direct Market List MUST display the same
                | DM approval progress as Assignment DM List.
                |
                */

                TextColumn::make('status')
                    ->label('Status')
                    ->badge()
                    ->sortable()
                    ->searchable()
                    ->state(
                        function (DirectMarket $record): string {

                            /*
                            |--------------------------------------------------------------------------
                            | LEVEL 1 - DIRECT MARKET
                            |--------------------------------------------------------------------------
                            */

                            $directMarketTransaction =
                                \App\Models\ApprovalTransaction::query()
                                    ->where(
                                        'document_type',
                                        'DIRECT_MARKET'
                                    )
                                    ->where(
                                        'document_id',
                                        $record->getKey()
                                    )
                                    ->latest('id')
                                    ->first();

                            /*
                            |--------------------------------------------------------------------------
                            | LEVEL 1 REJECTED
                            |--------------------------------------------------------------------------
                            */

                            if (
                                $directMarketTransaction?->status === 'REJECTED'
                            ) {
                                return 'Rejected';
                            }

                            /*
                            |--------------------------------------------------------------------------
                            | LEVEL 1 NOT APPROVED
                            |--------------------------------------------------------------------------
                            */

                            if (
                                ! $directMarketTransaction
                                ||
                                $directMarketTransaction->status !== 'APPROVED'
                            ) {
                                return (string) $record->status;
                            }

                            /*
                            |--------------------------------------------------------------------------
                            | LEVEL 1 APPROVED
                            |--------------------------------------------------------------------------
                            |
                            | At this point DM has passed Level 1.
                            | The next approval leg is ADM / Level 2.
                            |
                            */

                            /*
                            |--------------------------------------------------------------------------
                            | FIND ADM FROM THIS DM
                            |--------------------------------------------------------------------------
                            */

                            $assignmentDirectMarket =
                                AssignmentDirectMarket::query()
                                    ->where(
                                        'direct_market_id',
                                        $record->getKey()
                                    )
                                    ->latest('id')
                                    ->first();

                            /*
                            |--------------------------------------------------------------------------
                            | ADM NOT CREATED
                            |--------------------------------------------------------------------------
                            */

                            if (! $assignmentDirectMarket) {
                                return 'Approval 1/2';
                            }

                            /*
                            |--------------------------------------------------------------------------
                            | LEVEL 2 - ADM APPROVAL
                            |--------------------------------------------------------------------------
                            */

                            $assignmentTransaction =
                                \App\Models\ApprovalTransaction::query()
                                    ->where(
                                        'document_type',
                                        'ASSIGNMENT_DIRECT_MARKET'
                                    )
                                    ->where(
                                        'document_id',
                                        $assignmentDirectMarket->getKey()
                                    )
                                    ->latest('id')
                                    ->first();

                            /*
                            |--------------------------------------------------------------------------
                            | ADM REJECTED
                            |--------------------------------------------------------------------------
                            */

                            if (
                                $assignmentTransaction?->status === 'REJECTED'
                                ||
                                $assignmentDirectMarket->status ===
                                    AssignmentDirectMarket::STATUS_REJECTED
                            ) {
                                return 'Rejected';
                            }

                            /*
                            |--------------------------------------------------------------------------
                            | LEVEL 2 APPROVED
                            |--------------------------------------------------------------------------
                            |
                            | DM Level 1 + ADM Level 2 = Approval 2/2
                            |
                            */

                            if (
                                $assignmentTransaction?->status === 'APPROVED'
                            ) {
                                return 'Approval 2/2';
                            }

                            /*
                            |--------------------------------------------------------------------------
                            | ADM COMPLETED
                            |--------------------------------------------------------------------------
                            |
                            | Safety fallback for completed ADM.
                            |
                            */

                            if (
                                $assignmentDirectMarket->status ===
                                AssignmentDirectMarket::STATUS_COMPLETED
                            ) {
                                return 'Approval 2/2';
                            }

                            /*
                            |--------------------------------------------------------------------------
                            | LEVEL 2 WAITING
                            |--------------------------------------------------------------------------
                            */

                            return 'Approval 1/2';
                        }
                    )
                    ->color(
                        function (
                            string $state
                        ): string {

                            if (
                                $state === 'Approval 2/2'
                            ) {
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

                                DirectMarket::STATUS_DRAFT =>
                                    'gray',

                                'Waiting Approval' =>
                                    'warning',

                                DirectMarket::STATUS_SUBMITTED =>
                                    'warning',

                                DirectMarket::STATUS_APPROVED =>
                                    'success',

                                DirectMarket::STATUS_REJECTED =>
                                    'danger',

                                DirectMarket::STATUS_CANCELLED =>
                                    'danger',

                                DirectMarket::STATUS_CLOSED =>
                                    'primary',

                                'Rejected' =>
                                    'danger',

                                default =>
                                    'gray',
                            };
                        }
                    ),

                /*
                |--------------------------------------------------------------------------
                | CREATED BY
                |--------------------------------------------------------------------------
                */

                TextColumn::make(
                    'creator.name'
                )
                    ->label('Created By')
                    ->toggleable(
                        isToggledHiddenByDefault: true
                    )
                    ->sortable(),

            ])

            /*
            |--------------------------------------------------------------------------
            | LOCAL FILTERS
            |--------------------------------------------------------------------------
            */

            ->filters([

                SelectFilter::make('status')
                    ->label('Status')
                    ->options([

                        DirectMarket::STATUS_DRAFT =>
                            DirectMarket::STATUS_DRAFT,

                        DirectMarket::STATUS_SUBMITTED =>
                            DirectMarket::STATUS_SUBMITTED,

                        DirectMarket::STATUS_APPROVED =>
                            DirectMarket::STATUS_APPROVED,

                        DirectMarket::STATUS_REJECTED =>
                            DirectMarket::STATUS_REJECTED,

                        DirectMarket::STATUS_CANCELLED =>
                            DirectMarket::STATUS_CANCELLED,

                        DirectMarket::STATUS_CLOSED =>
                            DirectMarket::STATUS_CLOSED,

                    ]),

                Filter::make('request_date')
                    ->label('Request Date')
                    ->schema([

                        \Filament\Forms\Components\DatePicker::make(
                            'from'
                        )
                            ->label('From'),

                        \Filament\Forms\Components\DatePicker::make(
                            'until'
                        )
                            ->label('Until'),

                    ])
                    ->query(
                        function (
                            Builder $query,
                            array $data
                        ): Builder {

                            return $query

                                ->when(
                                    $data['from'] ?? null,
                                    fn (
                                        Builder $query,
                                        $date
                                    ): Builder =>
                                        $query->where(
                                            'request_date',
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
                                        Builder $query,
                                        $date
                                    ): Builder =>
                                        $query->where(
                                            'request_date',
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

                TrashedFilter::make(),

            ])

            /*
            |--------------------------------------------------------------------------
            | RECORD ACTIONS
            |--------------------------------------------------------------------------
            */

            ->recordActions([

                ActionGroup::make([


                    /*
                    |--------------------------------------------------------------------------
                    | SUBMIT
                    |--------------------------------------------------------------------------
                    */

                    Action::make('submit')
                        ->label('Submit')
                        ->icon('heroicon-o-paper-airplane')
                        ->color('primary')
                        ->requiresConfirmation()
                        ->visible(
                            fn (
                                DirectMarket $record
                            ): bool =>
                                $record->status ===
                                DirectMarket::STATUS_DRAFT
                                &&
                                $record->items()
                                    ->where(function ($query) {
                                        $query
                                            ->whereNull('item_id')
                                            ->orWhereNull('uom_id')
                                            ->orWhereNull('qty')
                                            ->orWhere('qty', '<=', 0)
                                            ->orWhereNull('required_date')
                                            ->orWhereNull('delivery_location')
                                            ->orWhere('delivery_location', '');
                                    })
                                    ->doesntExist()
                                &&
                                filled($record->request_date)
                                &&
                                filled($record->required_date)
                                &&
                                filled($record->company_id)
                                &&
                                filled($record->business_unit_id)
                                &&
                                filled($record->branch_id)
                                &&
                                filled($record->department_id)
                                &&
                                filled($record->cost_center_id)
                                &&
                                filled($record->requester_id)
                                &&
                                filled($record->delivery_location)
                                &&
                                $record->items()->exists()
                        )
                        ->authorize(
                            fn (
                                DirectMarket $record
                            ): bool =>
                                auth()->user()->can(
                                    'submit',
                                    $record
                                )
                        )
                        ->action(
                            function (
                                DirectMarket $record
                            ): void {

                                try {

                                    app(DirectMarketService::class)
                                        ->submit($record->id);

                                    Notification::make()
                                        ->success()
                                        ->title(
                                            'Direct Market submitted successfully.'
                                        )
                                        ->send();

                                } catch (\Throwable $e) {

                                    report($e);

                                    Notification::make()
                                        ->danger()
                                        ->title(
                                            'Direct Market submission failed.'
                                        )
                                        ->body(
                                            $e->getMessage()
                                        )
                                        ->send();
                                }
                            }
                        ),


                    /*
                    |--------------------------------------------------------------------------
                    | VIEW
                    |--------------------------------------------------------------------------
                    */

                    ViewAction::make()
                        ->label('View'),

                    /*
                    |--------------------------------------------------------------------------
                    | PREVIEW
                    |--------------------------------------------------------------------------
                    */

                    Action::make('preview')
                        ->label('Preview DM')
                        ->icon('heroicon-o-document')
                        ->color('primary')
                        ->url(
                            fn (Model $record): string =>
                                PrintDirectMarket::getUrl([
                                    'record' => $record,
                                ]),
                        ),
                        

                    /*
                    |--------------------------------------------------------------------------
                    | EDIT
                    |--------------------------------------------------------------------------
                    */

                    EditAction::make()
                        ->label('Edit')
                        ->icon(
                            fn (
                                DirectMarket $record
                            ): string =>
                                $record->status ===
                                DirectMarket::STATUS_DRAFT
                                    ? 'heroicon-o-pencil-square'
                                    : 'heroicon-o-lock-closed'
                        )
                        ->disabled(
                            fn (
                                DirectMarket $record
                            ): bool =>
                                $record->status !==
                                DirectMarket::STATUS_DRAFT
                        ),

                    /*
                    |--------------------------------------------------------------------------
                    | DELETE
                    |--------------------------------------------------------------------------
                    */

                    DeleteAction::make()
                        ->visible(
                            fn (
                                DirectMarket $record
                            ): bool =>
                                $record->status ===
                                DirectMarket::STATUS_DRAFT
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

            ->toolbarActions([

                BulkActionGroup::make([

                    DeleteBulkAction::make(),

                ]),

            ]);
    }
}
