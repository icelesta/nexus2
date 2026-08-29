<?php

declare(strict_types=1);

namespace App\Filament\Resources\AssignmentMaterialRequisitionResource\RelationManagers;

use App\Models\AssignmentMaterialRequisitionItem;
use App\Services\Purchasing\AssignmentMaterialRequisitionItemService; 

use Filament\Actions\BulkActionGroup;
use Filament\Actions\CreateAction;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;

use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;

use Filament\Resources\RelationManagers\RelationManager;
use Filament\Schemas\Schema;

use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

use Illuminate\Database\Eloquent\Model;

class AssignmentMaterialRequisitionItemsRelationManager extends RelationManager
{
    /**
     * Relationship name.
     */
    protected static string $relationship = 'items';

    /**
     * Relation Manager title.
     */
    protected static ?string $title = 'Assignment Items';

    /**
     * Form Schema.
     */
    public function form(
        Schema $schema,
    ): Schema {

        return $schema
            ->components([

                Select::make('purchase_requisition_item_id')
                    ->relationship(
                        'purchaseRequisitionItem',
                        'item_name'
                    )
                    ->searchable()
                    ->preload()
                    ->required(),

                TextInput::make('unit_price')
                    ->label('Unit Price')
                    ->numeric()
                    ->minValue(0)
                    ->prefix('Rp')
                    ->required(),

                Select::make('supplier_id')
                    ->relationship(
                        name: 'supplier',
                        titleAttribute: 'supplier_name',
                        modifyQueryUsing: fn ($query) => $query
                            ->where('is_active', true)
                            ->where('is_blacklisted', false)
                            ->where('allow_purchase', true)
                            ->orderBy('supplier_code'),
                    )
                    ->getOptionLabelFromRecordUsing(
                        fn (\App\Models\Supplier $record): string =>
                            "{$record->supplier_code} - {$record->supplier_name}"
                    )
                    ->searchable(['supplier_code', 'supplier_name'])
                    ->preload()
                    ->placeholder('Select Supplier'),

                Textarea::make('remarks')
                    ->rows(3),

            ]);
    }
    

    /**
     * Table Schema.
     */
    public function table(
        Table $table,
    ): Table {

        return $table
            ->columns([

                TextColumn::make('purchaseRequisitionItem.item_code')
                    ->label('Item Code')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('purchaseRequisitionItem.item_name')
                    ->label('Item Name')
                    ->searchable()
                    ->wrap(),

                TextColumn::make('supplier.supplier_name')
                    ->label('Supplier')
                    ->formatStateUsing(
                        fn ($state, AssignmentMaterialRequisitionItem $record): string =>
                            $record->supplier
                                ? "{$record->supplier->supplier_code} - {$state}"
                                : '-'
                    )
                    ->searchable(),

                TextColumn::make('assigned_qty')
                    ->label('Qty')
                    ->numeric(4)
                    ->alignEnd(),

                TextColumn::make('quoted_price')
                    ->label('Unit Price')
                    ->money('IDR', divideBy: 1)
                    ->alignEnd(),

                TextColumn::make('line_total')
                    ->label('Line Total')
                    ->money('IDR')
                    ->alignEnd(),

                IconColumn::make('is_selected_supplier')
                    ->label('Selected')
                    ->boolean()
                    ->alignCenter(),

                TextColumn::make('status')
                    ->badge()
                    ->sortable(),

            ])

            ->filters([
                //
            ])

            ->headerActions([

            CreateAction::make()
                ->label('Add Item')
                ->icon('heroicon-m-plus')
                ->slideOver()
                ->visible(fn (): bool => auth()->user()->can(
                    'create',
                    AssignmentMaterialRequisitionItem::class,
                ))
                ->authorize(fn (): bool => auth()->user()->can(
                    'create',
                    AssignmentMaterialRequisitionItem::class,
                )),

            ])

            ->recordActions([

                ViewAction::make()
                    ->authorize(fn (Model $record): bool => auth()->user()->can(
                        'view',
                        $record,
                    )),

                EditAction::make()
                    ->slideOver()
                    ->visible(fn (Model $record): bool => $record->canEdit())
                    ->disabled(fn (Model $record): bool => ! $record->canEdit())
                    ->authorize(fn (Model $record): bool => auth()->user()->can(
                        'update',
                        $record,
                    ))
                    ->using(function (
                        Model $record,
                        array $data,
                    ): Model {

                        $service = app(
                            AssignmentMaterialRequisitionItemService::class
                        );

                        return $service->updateAssignmentItem(
                            itemId: (int) $record->getKey(),
                            supplierId: isset($data['supplier_id'])
                                ? (int) $data['supplier_id']
                                : null,
                            assignedQty: (float) $data['assigned_qty'],
                            unitPrice: (float) $data['unit_price'],
                        );
                    }),

                DeleteAction::make()
                    ->visible(fn (Model $record): bool => $record->canDelete())
                    ->disabled(fn (Model $record): bool => ! $record->canDelete())
                    ->authorize(fn (Model $record): bool => auth()->user()->can(
                        'delete',
                        $record,
                    )),

            ])

            ->bulkActions([

                BulkActionGroup::make([

                    DeleteBulkAction::make()
                    ->authorize(fn (): bool => auth()->user()->can(
                        'deleteAny',
                        AssignmentMaterialRequisitionItem::class,
                    )),

                ]),

            ])            


            ->toolbarActions([
                //
            ]);
    }


    /**
     * Determine whether the relation manager should be displayed.
     */
    public static function canViewForRecord(
        Model $ownerRecord,
        string $pageClass,
    ): bool {

        return auth()->user()->can(
            'view',
            $ownerRecord,
        );
    }

}