<?php

declare(strict_types=1);

namespace App\Filament\Widgets;

use App\Models\Item;
use Filament\Widgets\StatsOverviewWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class LowStockWidget extends StatsOverviewWidget
{
    protected static ?int $sort = 20;

    protected ?string $pollingInterval = '60s';

    protected function getStats(): array
    {
        $lowStock = Item::query()
            ->whereColumn('current_stock', '<=', 'minimum_stock')
            ->count();

        $outOfStock = Item::query()
            ->where('current_stock', '<=', 0)
            ->count();

        return [
            Stat::make('Low Stock', number_format($lowStock))
                ->description('Items below minimum stock')
                ->descriptionIcon('heroicon-m-exclamation-triangle')
                ->color($lowStock > 0 ? 'warning' : 'success'),

            Stat::make('Out of Stock', number_format($outOfStock))
                ->description('Items with zero stock')
                ->descriptionIcon('heroicon-m-x-circle')
                ->color($outOfStock > 0 ? 'danger' : 'success'),
        ];
    }
}