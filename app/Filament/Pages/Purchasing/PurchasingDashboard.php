<?php

declare(strict_types=1);

namespace App\Filament\Pages\Purchasing;

use BackedEnum;
use Filament\Pages\Page;
use UnitEnum;

class PurchasingDashboard extends Page
{
    /**
     * Navigation
     */
    protected static ?string $navigationLabel = 'Dashboard';

    protected static string|UnitEnum|null $navigationGroup = 'Purchasing';

    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-chart-pie';

    protected static ?int $navigationSort = 1;

    protected static ?string $slug = 'purchasing/dashboard';

    /**
     * Remove Filament Default Heading
     */
    protected static ?string $title = '';

    protected string $view = 'filament.pages.purchasing.purchasing-dashboard';

    public function getHeading(): string
    {
        return '';
    }

    public function getSubheading(): ?string
    {
        return null;
    }
}