<?php

declare(strict_types=1);

namespace App\Filament\Resources\PurchaseRequisitions\Tables;

use Filament\Tables\Table;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Filters\SelectFilter;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\ActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\Action;
use App\Filament\Resources\PurchaseRequisitions\Pages\PrintPurchaseRequisition;

class PurchaseRequisitionTable
{
    public static function configure(
        Table $table,
    ): Table {

        return $table

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
                    ->color(fn (string $state): string => match ($state) {
                        'Draft'             => 'gray',
                        'Submitted'         => 'info',
                        'Pending Approval'  => 'warning',
                        'Approved'          => 'success',
                        'Rejected'          => 'danger',
                        'Cancelled'         => 'danger',
                        'Closed'            => 'primary',
                        default             => 'gray',
                    }),

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

/*                TextColumn::make('businessUnit.display_name')
                    ->label('Business Unit'),*/

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

            ->defaultSort(
                'created_at',
                'desc',
            )


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


            ->recordActions([

                ActionGroup::make([

                    ViewAction::make(),

                    Action::make('preview')
                        ->label('Preview MR')
                        ->icon('heroicon-o-document')
                        ->color('primary')
                        ->url(fn ($record): string => PrintPurchaseRequisition::getUrl([
                            'record' => $record,
                        ])),

                    EditAction::make(),

                    DeleteAction::make(),

                ])
                    ->label('Actions')
                    ->button()
                    ->color('warning')
                    ->icon('heroicon-m-chevron-down'),

            ])

            ->toolbarActions([

                /*
                |--------------------------------------------------------------------------
                | Bulk Document Actions
                |--------------------------------------------------------------------------
                */

                BulkActionGroup::make([

                    DeleteBulkAction::make(),

                ]),

            ]);

    }
}