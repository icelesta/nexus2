<?php

declare(strict_types=1);

namespace App\Filament\Pages;

//use App\Filament\Widgets\LowStockWidget;
use App\Filament\Widgets\WelcomeWidget;
use App\Filament\Widgets\ItemStatsWidget;
use Filament\Pages\Dashboard as BaseDashboard;
use App\Filament\Widgets\Approval\ApprovalKpiWidget;
use App\Filament\Widgets\Approval\MyPendingApprovalsWidget;
use App\Filament\Widgets\Approval\RecentApprovalActivityWidget;

class Dashboard extends BaseDashboard
{
    /*
    |--------------------------------------------------------------------------
    | Navigation
    |--------------------------------------------------------------------------
    */

    protected static ?string $navigationLabel = 'DASHBOARD';

    protected static ?string $title = 'Welcome To Nexus ERP System';

    public function getWidgets(): array
    {
        return [
            WelcomeWidget::class,

            /*
            |--------------------------------------------------------------------------
            | Approval Center
            |--------------------------------------------------------------------------
            */

            ApprovalKpiWidget::class,
            ItemStatsWidget::class,

            MyPendingApprovalsWidget::class,
            RecentApprovalActivityWidget::class,

            /*
            |--------------------------------------------------------------------------
            | Inventory
            |--------------------------------------------------------------------------
            */

            
            // LowStockWidget::class,
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