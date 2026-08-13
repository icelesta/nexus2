<?php

declare(strict_types=1);

namespace App\Filament\Widgets\Approval;

use App\Services\Approval\ApprovalDashboardService;
use Filament\Widgets\StatsOverviewWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Illuminate\Contracts\View\View;

class ApprovalKpiWidget extends StatsOverviewWidget
{
    protected ?string $heading = 'Approval Overview';

    protected function getStats(): array
    {
        $user = auth()->user();

        if (! $user) {
            return [];
        }

        $summary = app(
            ApprovalDashboardService::class
        )->summary($user);

        return [

            Stat::make(
                'Pending My Approval',
                number_format(
                    $summary['pending']
                )
            )
                ->description(
                    'Documents waiting for your approval'
                )
                ->descriptionIcon(
                    'heroicon-m-clock'
                )
                ->color(
                    $summary['pending'] > 0
                        ? 'warning'
                        : 'gray'
                ),

            Stat::make(
                'Approved',
                number_format(
                    $summary['approved']
                )
            )
                ->description(
                    'Completed approval transactions'
                )
                ->descriptionIcon(
                    'heroicon-m-check-circle'
                )
                ->color(
                    'success'
                ),

            Stat::make(
                'Rejected',
                number_format(
                    $summary['rejected']
                )
            )
                ->description(
                    'Rejected approval transactions'
                )
                ->descriptionIcon(
                    'heroicon-m-x-circle'
                )
                ->color(
                    'danger'
                ),

            Stat::make(
                'Unread Notifications',
                number_format(
                    $summary['unread_notifications']
                )
            )
                ->description(
                    'Notifications requiring attention'
                )
                ->descriptionIcon(
                    'heroicon-m-bell'
                )
                ->color(
                    $summary['unread_notifications'] > 0
                        ? 'primary'
                        : 'gray'
                ),

        ];
    }
}