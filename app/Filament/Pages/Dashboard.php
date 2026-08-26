<?php

declare(strict_types=1);

namespace App\Filament\Pages;

use App\Filament\Widgets\Approval\ApprovalKpiWidget;
use App\Filament\Widgets\Approval\MyPendingApprovalsWidget;
use App\Filament\Widgets\Approval\RecentApprovalActivityWidget;
use App\Filament\Widgets\ItemStatsWidget;
use App\Filament\Widgets\WelcomeWidget;
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
    | DASHBOARD GRID
    |--------------------------------------------------------------------------
    |
    | Keep the outer dashboard grid to ONE column.
    |
    | Each widget therefore receives the full dashboard width.
    |
    | Internal layouts are controlled by the widgets themselves.
    |
    */

    public function getColumns(): int | array
    {
        return 1;
    }


    /*
    |--------------------------------------------------------------------------
    | WIDGETS
    |--------------------------------------------------------------------------
    */

    public function getWidgets(): array
    {
        return [

            /*
            |--------------------------------------------------------------------------
            | HERO / WELCOME
            |--------------------------------------------------------------------------
            |
            | Greeting + BMS Global Hero Banner
            |
            */

            WelcomeWidget::class,


            /*
            |--------------------------------------------------------------------------
            | APPROVAL CENTER
            |--------------------------------------------------------------------------
            */

            ApprovalKpiWidget::class,

            ItemStatsWidget::class,

            MyPendingApprovalsWidget::class,

            RecentApprovalActivityWidget::class,


            /*
            |--------------------------------------------------------------------------
            | INVENTORY
            |--------------------------------------------------------------------------
            */

            // LowStockWidget::class,

        ];
    }


    /*
    |--------------------------------------------------------------------------
    | PAGE WIDTH
    |--------------------------------------------------------------------------
    */

    public function getMaxContentWidth(): ?string
    {
        return null;
    }


    /*
    |--------------------------------------------------------------------------
    | PAGE HEADING
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