<?php

declare(strict_types=1);

namespace App\Filament\Resources\AssignmentMaterialRequisitionResource\Tables;

use App\Services\Purchasing\AssignmentMaterialRequisitionService;

use Filament\Actions\Action;
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




class AssignmentMaterialRequisitionsTable
{
    public static function configure(Table $table): Table
    {
        return $table

            ->defaultSort('assigned_at', 'desc')

            ->columns([

                TextColumn::make('purchaseRequisition.pr_no')
                    ->label('PR Number')
                    ->weight(FontWeight::SemiBold)
                    ->copyable()
                    ->tooltip('Click to copy PR Number')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('assignedTo.name')
                    ->label('Assigned To')
                    ->searchable()
                    ->sortable()
                    ->placeholder('-'),

                TextColumn::make('assigned_at')
                    ->label('Assignment Date')
                    ->date('d M Y')
                    ->sortable(),

                TextColumn::make('status')
                    ->badge()
                    ->sortable()
                    ->searchable()
                    ->color(fn (string $state): string => match ($state) {

                        'Draft'             => 'gray',

                        'Assigned'          => 'info',

                        'Waiting Approval'  => 'warning',

                        'Approved'          => 'success',

                        'Completed'         => 'success',

                        'Rejected'          => 'danger',

                        'Cancelled'         => 'gray',

                        default             => 'gray',

                    }),

            ])

            ->filters([

                SelectFilter::make('status')
                    ->label('Status')
                    ->options([
                        'Draft' => 'Draft',
                        'Assigned' => 'Assigned',
                        'Waiting Approval' => 'Waiting Approval',
                        'Approved' => 'Approved',
                        'Completed' => 'Completed',
                        'Rejected' => 'Rejected',
                        'Cancelled' => 'Cancelled',
                    ]),

                SelectFilter::make('assigned_to')
                    ->label('Assigned To')
                    ->relationship('assignedTo', 'name')
                    ->searchable()
                    ->preload(),

                SelectFilter::make('purchase_requisition_id')
                    ->label('Purchase Requisition')
                    ->relationship('purchaseRequisition', 'pr_no')
                    ->searchable()
                    ->preload(),

                Filter::make('assigned_at')
                    ->label('Assignment Date')
                    ->schema([

                        DatePicker::make('from')
                            ->label('From'),

                        DatePicker::make('until')
                            ->label('Until'),

                    ])

                    ->query(function ($query, array $data) {

                        return $query
                            ->when(
                                $data['from'],
                                fn ($query, $date) => $query->whereDate('assigned_at', '>=', $date)
                            )
                            ->when(
                                $data['until'],
                                fn ($query, $date) => $query->whereDate('assigned_at', '<=', $date)
                            );

                    }),

                TrashedFilter::make(),

            ])

                ->recordActions([

                    ActionGroup::make([

                        ViewAction::make()
                            ->icon('heroicon-o-eye'),

                        EditAction::make()
                            ->icon('heroicon-o-pencil-square'),

                        Action::make('submit')
                            ->label('Submit')
                            ->icon('heroicon-o-paper-airplane')
                            ->color('warning')
                            ->requiresConfirmation()
                            ->visible(fn ($record) => $record->status === 'Draft')
                            ->modalHeading('Submit Assignment')
                            ->modalDescription(
                                'After submission, this Assignment Material Requisition will be locked for editing.'
                            )
                            ->action(function ($record): void {
                                app(AssignmentMaterialRequisitionService::class)
                                    ->submit($record->id);
                            }),

                        Action::make('generatePo')
                            ->label('Generate PO')
                            ->icon('heroicon-o-document-duplicate')
                            ->color('success')
                            ->requiresConfirmation()
                            ->visible(fn ($record) => $record->status === 'Assigned')
                            ->modalHeading('Generate Purchase Order')
                            ->modalDescription(
                                'Generate a Purchase Order from this Assignment Material Requisition.'
                            )
                            ->action(function ($record): void {
                                app(\App\Services\Purchasing\GeneratePurchaseOrderService::class)
                                    ->generate($record->id);
                            }),                            

                        DeleteAction::make()
                            ->icon('heroicon-o-trash')
                            ->color('danger')
                            ->label('Delete')
                            ->requiresConfirmation()
                            ->visible(fn ($record) => $record->canDelete())
                            ->modalHeading('Delete Assignment Material Requisition')
                            ->modalDescription(
                                'This Assignment Material Requisition will be moved to Trash (Soft Delete).'
                            )
                            ->modalSubmitActionLabel('Delete')
                            ->successNotificationTitle(
                                'Assignment Material Requisition deleted successfully.'
                            )
                            ->action(function ($record): void {
                                app(AssignmentMaterialRequisitionService::class)
                                    ->delete($record->id);
                            }),

                    ])
                        ->label('Actions')
                        ->icon('heroicon-m-ellipsis-vertical')
                        ->button(),

                ])


            ->toolbarActions([

                BulkActionGroup::make([

                    DeleteBulkAction::make(),

                    RestoreBulkAction::make(),

                    ForceDeleteBulkAction::make(),

                ])

            ]);
    }
}