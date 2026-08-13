<?php

declare(strict_types=1);

namespace App\Filament\Pages;

//use App\Filament\Widgets\LowStockWidget;
use App\Filament\Widgets\WelcomeWidget;
use App\Filament\Widgets\ItemStatsWidget;
use Filament\Pages\Dashboard as BaseDashboard;

class Dashboard extends BaseDashboard
{
    /*
    |--------------------------------------------------------------------------
    | Navigation
    |--------------------------------------------------------------------------
    */

    protected static ?string $navigationLabel = 'DASHBOARD';

    protected static ?string $title = 'Welcome To Nexus ERP System';

    /*
    |--------------------------------------------------------------------------
    | Widgets
    |--------------------------------------------------------------------------
    */

    public function getWidgets(): array
    {
        return [
            WelcomeWidget::class,
            ItemStatsWidget::class,
            //LowStockWidget::class,
        ];
    }

    /*
    |--------------------------------------------------------------------------
    | Layout
    |--------------------------------------------------------------------------
    */

    public function getMaxContentWidth(): ?string
    {
        return null;
    }

    /*
    |--------------------------------------------------------------------------
    | Heading
    |--------------------------------------------------------------------------
    */

    public function getHeading(): string
    {
        return '';
    }

    public function getSubheading(): ?string
    {
        return null;
    }
}