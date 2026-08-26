<?php

declare(strict_types=1);

namespace App\Filament\Resources\GeneratePurchaseOrderResource\Tables;

use App\Filament\Resources\GeneratePurchaseOrderResource\GeneratePurchaseOrderResource;
use App\Models\AssignmentMaterialRequisition;

use Filament\Actions\Action;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class GeneratePurchaseOrdersTable
{
    public static function configure(
        Table $table
    ): Table {

        return $table

            ->columns([

                /*
                |--------------------------------------------------------------------------
                | AMR Number
                |--------------------------------------------------------------------------
                */

                TextColumn::make('document_no')
                    ->label('AMR No.')
                    ->searchable()
                    ->sortable()
                    ->weight('bold'),

                /*
                |--------------------------------------------------------------------------
                | Material Requisition
                |--------------------------------------------------------------------------
                */

                TextColumn::make(
                    'purchaseRequisition.pr_no'
                )
                    ->label('MR No.')
                    ->searchable()
                    ->sortable(),

                /*
                |--------------------------------------------------------------------------
                | MR Status
                |--------------------------------------------------------------------------
                */

                TextColumn::make(
                    'purchaseRequisition.status'
                )
                    ->label('MR Status')
                    ->badge()
                    ->color(
                        fn (?string $state): string =>
                            match ($state) {
                                'Approved' => 'success',
                                'Rejected' => 'danger',
                                default => 'warning',
                            }
                    ),

                /*
                |--------------------------------------------------------------------------
                | AMR Status
                |--------------------------------------------------------------------------
                */

                TextColumn::make('status')
                    ->label('AMR Status')
                    ->badge()
                    ->color(
                        fn (?string $state): string =>
                            match ($state) {
                                AssignmentMaterialRequisition::STATUS_WAITING_APPROVAL
                                    => 'warning',

                                AssignmentMaterialRequisition::STATUS_APPROVED
                                    => 'success',

                                default
                                    => 'gray',
                            }
                    ),


                /*
                |--------------------------------------------------------------------------
                | Items
                |--------------------------------------------------------------------------
                */

                TextColumn::make('items_count')
                    ->label('Items')
                    ->counts('items')
                    ->alignCenter(),

                /*
                |--------------------------------------------------------------------------
                | Created
                |--------------------------------------------------------------------------
                */

                TextColumn::make('created_at')
                    ->label('Created')
                    ->dateTime('d M Y H:i')
                    ->timezone(
                        fn (): string =>
                            \App\Support\Timezone\UserTimezone::timezone()
                    )
                    ->sortable(),

            ])

            /*
            |--------------------------------------------------------------------------
            | Default Sort
            |--------------------------------------------------------------------------
            */

            ->defaultSort(
                'created_at',
                'desc'
            )

            /*
            |--------------------------------------------------------------------------
            | Record Actions
            |--------------------------------------------------------------------------
            */

            ->recordActions([

                Action::make('open')
                    ->label('Open')
                    ->icon('heroicon-o-arrow-right')
                    ->url(
                        fn (
                            AssignmentMaterialRequisition $record
                        ): string =>
                            GeneratePurchaseOrderResource::getUrl(
                                'edit',
                                [
                                    'record' => $record,
                                ]
                            )
                    ),

            ])

            /*
            |--------------------------------------------------------------------------
            | Empty State
            |--------------------------------------------------------------------------
            */

            ->emptyStateHeading(
                'No Purchase Orders Ready'
            )

            ->emptyStateDescription(
                'No Assignment Material Requisition is currently waiting for final approval or Purchase Order generation.'
            );
    }
}