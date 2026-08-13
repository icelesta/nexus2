<?php

declare(strict_types=1);

namespace App\Filament\Widgets;

use App\Models\Item;
use App\Models\ItemCategory;
use Filament\Widgets\StatsOverviewWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class ItemStatsWidget extends StatsOverviewWidget
{
    protected static ?int $sort = 10;

    protected ?string $pollingInterval = '30s';

    protected function getStats(): array
    {
        $totalItems = Item::count();

        $activeItems = Item::where('is_active', true)->count();

        $inactiveItems = Item::where('is_active', false)->count();

        $categories = ItemCategory::count();

        return [

            Stat::make('📦 Total Items', number_format($totalItems))
                ->description('Registered Inventory Items')
                ->descriptionIcon('heroicon-m-cube')
                ->color('primary')
                ->chart([8, 10, 9, 12, 11, 13, 15]),

            Stat::make('✅ Active Items', number_format($activeItems))
                ->description('Available for Business Process')
                ->descriptionIcon('heroicon-m-check-badge')
                ->color('success')
                ->chart([7, 8, 9, 10, 11, 12, 13]),

            Stat::make('🚫 Inactive Items', number_format($inactiveItems))
                ->description('Disabled / Archived')
                ->descriptionIcon('heroicon-m-x-circle')
                ->color('danger')
                ->chart([3, 2, 3, 4, 3, 2, 1]),

            Stat::make('🗂 Categories', number_format($categories))
                ->description('Inventory Categories')
                ->descriptionIcon('heroicon-m-squares-2x2')
                ->color('warning')
                ->chart([1, 2, 2, 3, 4, 4, 5]),

        ];
    }
}