<?php

namespace App\Filament\Resources\InventoryStockMovements\Tables;

use App\Models\InventoryStockMovement;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use App\Filament\Resources\InventoryStockMovements\Pages\ListInventoryStockMovements;

class InventoryStockMovementsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->defaultSort('created_at', 'desc')


            ->header(
                fn ($livewire) => view(
                    'filament.components.stock-movement-filters',
                    [
                        'livewire' => $livewire,
                    ]
                )
            )

            ->modifyQueryUsing(
                function (
                    Builder $query,
                    ListInventoryStockMovements $livewire,
                ): Builder {
                    return $livewire->applyStockMovementFiltersToQuery($query);
                }
            )

            ->columns([
                TextColumn::make('created_at')
                    ->label('Date & Time')
                    ->dateTime('d M Y H:i:s')
                    ->sortable()
                    ->toggleable(),

                TextColumn::make('reference_number')
                    ->label('Reference')
                    ->state(function (InventoryStockMovement $record): string {
                        $receipt = $record->goodsReceipt;

                        if ($receipt) {
                            return $receipt->grn_no
                                ?: $receipt->document_number
                                ?: '-';
                        }

                        if ($record->reference_type && $record->reference_id) {
                            return $record->reference_type . ' #' . $record->reference_id;
                        }

                        return '-';
                    })
                    ->searchable(
                        query: function (Builder $query, string $search): Builder {
                            return $query->whereHas(
                                'goodsReceipt',
                                fn (Builder $receiptQuery) => $receiptQuery
                                    ->where('grn_no', 'like', "%{$search}%")
                                    ->orWhere('document_number', 'like', "%{$search}%")
                            );
                        }
                    )
                    ->copyable(),

                TextColumn::make('movement_type')
                    ->label('Movement Type')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'RECEIPT' => 'success',
                        default => 'gray',
                    })
                    ->searchable()
                    ->sortable(),

                TextColumn::make('item.item_code')
                    ->label('Item Code')
                    ->searchable()
                    ->sortable()
                    ->weight('bold')
                    ->copyable(),

                TextColumn::make('item.item_name')
                    ->label('Item Name')
                    ->searchable()
                    ->sortable()
                    ->wrap(),

                TextColumn::make('warehouse.warehouse_name')
                    ->label('Warehouse')
                    ->badge()
                    ->sortable(),

                TextColumn::make('item.baseUom.uom_name')
                    ->label('UOM')
                    ->badge(),

                TextColumn::make('quantity')
                    ->label('Quantity')
                    ->formatStateUsing(fn ($state) =>
                        rtrim(rtrim(number_format((float) $state, 4, '.', ','), '0'), '.')
                    )
                    ->alignEnd()
                    ->sortable()
                    ->weight('bold'),

                TextColumn::make('balance_before')
                    ->label('Balance Before')
                    ->formatStateUsing(fn ($state) =>
                        rtrim(rtrim(number_format((float) $state, 4, '.', ','), '0'), '.')
                    )
                    ->alignEnd()
                    ->toggleable(),

                TextColumn::make('balance_after')
                    ->label('Balance After')
                    ->formatStateUsing(fn ($state) =>
                        rtrim(rtrim(number_format((float) $state, 4, '.', ','), '0'), '.')
                    )
                    ->alignEnd()
                    ->weight('bold'),

                TextColumn::make('creator.name')
                    ->label('Created By')
                    ->placeholder('-')
                    ->toggleable(),

                TextColumn::make('purchase_unit_cost')
                    ->label('Purchase Unit Cost')
                    ->state(fn (InventoryStockMovement $record) =>
                        $record->purchase_unit_cost === null
                            ? null
                            : ($record->purchase_currency_code ?? '')
                                . ' '
                                . number_format((float) $record->purchase_unit_cost, 2, ',', '.')
                    )
                    ->placeholder('-')
                    ->alignEnd()
                    ->toggleable(isToggledHiddenByDefault: true),

                TextColumn::make('exchange_rate')
                    ->label('Exchange Rate')
                    ->placeholder('-')
                    ->alignEnd()
                    ->toggleable(isToggledHiddenByDefault: true),

                TextColumn::make('unit_cost_idr')
                    ->label('Unit Cost (IDR)')
                    ->formatStateUsing(fn ($state) => $state === null
                        ? '-'
                        : 'Rp ' . number_format((float) $state, 2, ',', '.')
                    )
                    ->alignEnd()
                    ->toggleable(isToggledHiddenByDefault: true),

                TextColumn::make('avg_cost_before')
                    ->label('Avg Cost Before')
                    ->formatStateUsing(fn ($state) => $state === null
                        ? '-'
                        : 'Rp ' . number_format((float) $state, 2, ',', '.')
                    )
                    ->alignEnd()
                    ->toggleable(isToggledHiddenByDefault: true),

                TextColumn::make('avg_cost_after')
                    ->label('Avg Cost After')
                    ->formatStateUsing(fn ($state) => $state === null
                        ? '-'
                        : 'Rp ' . number_format((float) $state, 2, ',', '.')
                    )
                    ->alignEnd()
                    ->toggleable(isToggledHiddenByDefault: true),

            ])

            ->recordActions([])
            ->toolbarActions([])

            ->emptyStateHeading('No Stock Movement Found')
            ->emptyStateDescription(
                'Stock movement history will appear here after inventory transactions are posted.'
            )

            ->defaultPaginationPageOption(25)
            ->paginationPageOptions([10, 25, 50, 100]);
    }
}