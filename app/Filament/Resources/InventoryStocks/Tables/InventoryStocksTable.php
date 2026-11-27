<?php

declare(strict_types=1);

namespace App\Filament\Resources\InventoryStocks\Tables;

use App\Models\Brand;
use App\Models\ItemCategory;
use App\Models\Warehouse;
use Filament\Tables\Table;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Filters\SelectFilter;
use Illuminate\Database\Eloquent\Builder;
use Filament\Tables\Enums\FiltersLayout;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Illuminate\Support\HtmlString;

class InventoryStocksTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->defaultSort('item_id')

            ->header(fn ($livewire) => view(
                'filament.components.inventory-stock-filters',
                ['livewire' => $livewire]
            ))

            ->columns([
                TextColumn::make('item.item_code')
                    ->label('Item Code')
                    ->searchable()
                    ->sortable()
                    ->copyable()
                    ->weight('bold'),

                TextColumn::make('item.item_name')
                    ->label('Item Name')
                    ->searchable()
                    ->sortable()
                    ->wrap(),

                TextColumn::make('item.category.category_name')
                    ->label('Category')
                    ->sortable()
                    ->badge()
                    ->placeholder('-'),

                TextColumn::make('item.brand.brand_name')
                    ->label('Brand')
                    ->sortable()
                    ->badge()
                    ->placeholder('-'),

                TextColumn::make('warehouse.warehouse_name')
                    ->label('Warehouse')
                    ->sortable()
                    ->badge()
                    ->placeholder('-'),

                TextColumn::make('item.baseUom.uom_name')
                    ->label('UOM')
                    ->badge(),

                TextColumn::make('qty_on_hand')
                    ->label('Qty On Hand')
                    ->formatStateUsing(fn ($state) =>
                        rtrim(rtrim(number_format((float) $state, 4, '.', ','), '0'), '.')
                    )
                    ->alignEnd()
                    ->sortable(),

                TextColumn::make('qty_reserved')
                    ->label('Qty Reserved')
                    ->formatStateUsing(fn ($state) =>
                        rtrim(rtrim(number_format((float) $state, 4, '.', ','), '0'), '.')
                    )
                    ->alignEnd()
                    ->sortable(),

                TextColumn::make('qty_available')
                    ->label('Qty Available')
                    ->formatStateUsing(fn ($state) =>
                        rtrim(rtrim(number_format((float) $state, 4, '.', ','), '0'), '.')
                    )
                    ->alignEnd()
                    ->sortable()
                    ->weight('bold'),

                TextColumn::make('last_purchase_unit_cost')
                    ->label('Last Purchase')
                    ->state(function ($record): ?string {
                        if ($record->last_purchase_unit_cost === null) {
                            return null;
                        }

                        $currency = $record->last_purchase_currency ?? '';

                        return $currency . ' ' . number_format(
                            (float) $record->last_purchase_unit_cost,
                            2,
                            ',',
                            '.'
                        );
                    })
                    ->placeholder('-')
                    ->alignEnd()
                    ->toggleable(),

                TextColumn::make('avg_unit_cost')
                    ->label('Avg Unit Cost')
                    ->formatStateUsing(fn ($state) => $state === null
                        ? '-'
                        : 'Rp ' . number_format(
                            (float) $state,
                            2,
                            ',',
                            '.'
                        )
                    )
                    ->alignEnd()
                    ->placeholder('-')
                    ->toggleable(),

                TextColumn::make('total_value')
                    ->label('Total Value')
                    ->state(function ($record): ?float {
                        if ($record->avg_unit_cost === null) {
                            return null;
                        }

                        return round(
                            (float) $record->qty_on_hand
                            * (float) $record->avg_unit_cost,
                            2
                        );
                    })
                    ->formatStateUsing(fn ($state) => $state === null
                        ? '-'
                        : 'Rp ' . number_format(
                            (float) $state,
                            2,
                            ',',
                            '.'
                        )
                    )
                    ->alignEnd()
                    ->placeholder('-')
                    ->weight('bold')
                    ->toggleable(),

            ])

            ->filters([
                SelectFilter::make('warehouse_id')
                    // Warehouse
                    ->label(new HtmlString(
                        '<span class="font-semibold text-violet-600">Warehouse</span>'
                    ))
                    ->columnSpan(2)
                    ->options(fn (): array => Warehouse::query()
                        ->where('is_active', true)
                        ->orderBy('warehouse_name')
                        ->pluck('warehouse_name', 'id')
                        ->all()
                    )
                    ->placeholder('All Warehouses')
                    ->searchable(),

                SelectFilter::make('category_id')
                    // Category
                    ->label(new HtmlString(
                        '<span class="font-semibold text-orange-600">Category</span>'
                    ))
                    ->columnSpan(1)
                    ->options(fn (): array => ItemCategory::query()
                        ->orderBy('category_name')
                        ->pluck('category_name', 'id')
                        ->all()
                    )
                    ->placeholder('All Categories')
                    ->query(function (Builder $query, array $data): Builder {
                        if (blank($data['value'] ?? null)) {
                            return $query;
                        }

                        return $query->whereHas(
                            'item',
                            fn (Builder $itemQuery) => $itemQuery->where(
                                'category_id',
                                $data['value']
                            )
                        );
                    })
                    ->searchable(),

                SelectFilter::make('brand_id')
                    // Brand
                    ->label(new HtmlString(
                        '<span class="font-semibold text-cyan-600">Brand</span>'
                    ))
                    ->columnSpan(1)
                    ->options(fn (): array => Brand::query()
                        ->whereHas('items', function (Builder $itemQuery): void {
                            $itemQuery->whereHas('stocks');
                        })
                        ->orderBy('brand_name')
                        ->pluck('brand_name', 'id')
                        ->all()
                    )
                    ->placeholder('All Brands')
                    ->query(function (Builder $query, array $data): Builder {
                        if (blank($data['value'] ?? null)) {
                            return $query;
                        }

                        return $query->whereHas(
                            'item',
                            fn (Builder $itemQuery) => $itemQuery->where(
                                'brand_id',
                                $data['value']
                            )
                        );
                    })
                    ->searchable(),

                Filter::make('quantity_range')
                    ->label('Quantity Range')
                    ->columnSpan(1)
                    ->schema([
                    TextInput::make('qty_min')
                        ->label(new HtmlString(
                            '<span class="font-semibold text-emerald-600">Qty Min</span>'
                        ))
                        ->numeric()
                        ->minValue(0)
                        ->placeholder('Minimum'),

                    TextInput::make('qty_max')
                        ->label(new HtmlString(
                            '<span class="font-semibold text-emerald-600">Qty Max</span>'
                        ))
                        ->numeric()
                        ->minValue(0)
                        ->placeholder('Maximum'),
                    ])
                    ->columns(2)
                    ->query(function (Builder $query, array $data): Builder {
                        if (filled($data['qty_min'] ?? null)) {
                            $query->where(
                                'qty_on_hand',
                                '>=',
                                $data['qty_min']
                            );
                        }

                        if (filled($data['qty_max'] ?? null)) {
                            $query->where(
                                'qty_on_hand',
                                '<=',
                                $data['qty_max']
                            );
                        }

                        return $query;
                    }),

                Filter::make('show_zero_stock')
                    ->label(new HtmlString(
                        '<span class="font-semibold text-rose-600">Include Zero</span>'
                    ))
                    ->columnSpan(1)
                    ->schema([
                        Toggle::make('isActive')
                            ->label('Include Zero')
                            ->default(false)
                            ->inline(false),
                    ])
                    ->default(['isActive' => false])
                    ->query(function (Builder $query, array $data): Builder {
                        if (! ($data['isActive'] ?? false)) {
                            $query->where('qty_on_hand', '>', 0);
                        }

                        return $query;
                    }),
            ])

            ->filtersLayout(FiltersLayout::Hidden)
            ->filtersFormColumns([
                'default' => 1,
                'md' => 3,
                'xl' => 6,
            ])
            ->filtersApplyAction(
                fn ($action) => $action
                    ->label('Apply Filters')
                    ->color('primary')
                    ->icon('heroicon-o-funnel')
            )
            ->filtersRemoveAllAction(
                fn ($action) => $action
                    ->label('Clear Filters')
                    ->color('gray')
                    ->icon('heroicon-o-x-mark')
            )

            ->recordActions([])
            ->toolbarActions([])

            ->emptyStateHeading('No Inventory Stock Found')
            ->emptyStateDescription(
                'Stock balances will appear here after Receiving Records are submitted with a Warehouse.'
            )

            ->defaultPaginationPageOption(25)
            ->paginationPageOptions([10, 25, 50, 100]);
    }
}