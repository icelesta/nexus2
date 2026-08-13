<?php

declare(strict_types=1);

namespace App\Filament\Resources\PurchaseRequisitions\Tables;

use App\Filament\Resources\PurchaseRequisitions\Pages\PrintPurchaseRequisition;
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
use Illuminate\Database\Eloquent\Model;
use Throwable;
use Filament\Support\Icons\Heroicon;

class PurchaseRequisitionTable
{
    public static function configure(
        Table $table,
    ): Table {

        return $table

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

                TextColumn::make('status')
                    ->label('Status')
                    ->badge()
                    ->sortable()
                    ->searchable()
                    ->alignCenter()
                    ->color(
                        fn (string $state): string => match ($state) {
                            'Draft'            => 'gray',
                            'Submitted'        => 'info',
                            'Pending Approval' => 'warning',
                            'Approved'         => 'success',
                            'Rejected'         => 'danger',
                            'Cancelled'        => 'danger',
                            'Closed'           => 'primary',
                            default            => 'gray',
                        },
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

                /*
                |--------------------------------------------------------------------------
                | Document Filters
                |--------------------------------------------------------------------------
                */

                SelectFilter::make('document_type')
                    ->label('Document Type'),

                Filter::make('document_date')
                    ->label('Document Date'),

                Filter::make('required_date')
                    ->label('Required Date'),

                /*
                |--------------------------------------------------------------------------
                | Organization Filters
                |--------------------------------------------------------------------------
                */

                SelectFilter::make('company_id')
                    ->label('Company'),

                SelectFilter::make('business_unit_id')
                    ->label('Business Unit'),

                SelectFilter::make('department_id')
                    ->label('Department'),

                SelectFilter::make('warehouse_id')
                    ->label('Warehouse'),

                /*
                |--------------------------------------------------------------------------
                | Requester Filters
                |--------------------------------------------------------------------------
                */

                SelectFilter::make('requester_id')
                    ->label('Requester'),

                SelectFilter::make('requested_for_id')
                    ->label('Requested For'),

                /*
                |--------------------------------------------------------------------------
                | Status Filters
                |--------------------------------------------------------------------------
                */

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
            |
            | Nexus Standard:
            | All document actions are grouped inside the yellow
            | "Actions" button.
            |
            */

            ->recordActions([

                ActionGroup::make([

                    /*
                    |--------------------------------------------------------------------------
                    | VIEW
                    |--------------------------------------------------------------------------
                    */

                    ViewAction::make(),

                    /*
                    |--------------------------------------------------------------------------
                    | PREVIEW
                    |--------------------------------------------------------------------------
                    */

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

                    /*
                    |--------------------------------------------------------------------------
                    | EDIT
                    |--------------------------------------------------------------------------
                    |
                    | Nexus ERP Rule:
                    |
                    | Draft            → Editable
                    | Submitted        → Locked
                    | Pending Approval → Locked
                    | Approved         → Locked
                    | Rejected         → Locked
                    | Cancelled        → Locked
                    | Completed        → Locked
                    | Closed           → Locked
                    |
                    */

                    EditAction::make()
                        ->icon(
                            fn (Model $record): string =>
                                $record->status === 'Draft'
                                    ? 'heroicon-o-pencil-square'
                                    : 'heroicon-o-lock-closed'
                        )
                        ->disabled(
                            fn (Model $record): bool =>
                                $record->status !== 'Draft'
                        )
                        ->tooltip(
                            fn (Model $record): ?string =>
                                $record->status !== 'Draft'
                                    ? 'Material Requisition cannot be edited after submission.'
                                    : 'Edit Material Requisition'
                        ),

                    /*
                    |--------------------------------------------------------------------------
                    | SUBMIT MATERIAL REQUISITION
                    |--------------------------------------------------------------------------
                    |
                    | AM-5.1
                    |
                    | Submit is intentionally placed inside the standard
                    | Nexus "Actions" menu.
                    |
                    */

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

                                    /*
                                    |--------------------------------------------------------------------------
                                    | ERP VALIDATION
                                    |--------------------------------------------------------------------------
                                    | A Material Requisition cannot be submitted
                                    | without at least one material item.
                                    |--------------------------------------------------------------------------
                                    */

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

                    /*
                    |--------------------------------------------------------------------------
                    | DELETE
                    |--------------------------------------------------------------------------
                    |
                    | Nexus ERP Rule:
                    |
                    | Draft            → Deletable
                    | Submitted+      → Locked
                    |
                    */

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

                    /*
                    |--------------------------------------------------------------------------
                    | NEXUS ACTION BUTTON STANDARD
                    |--------------------------------------------------------------------------
                    */

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