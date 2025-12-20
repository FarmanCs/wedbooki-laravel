<?php

namespace App\Filament\Resources\Hosts\Widgets;

use App\Models\Host\Host;
use Filament\Widgets\StatsOverviewWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class HostStats extends StatsOverviewWidget
{
    protected function getStats(): array
    {
        $total_host= Host::count();
        $active_host= Host::where('account_deactivated', 1)->count();
        $pending_host= Host::where('status', 'Pending')->count();
        $blocked_host= Host::where('status', 'Banned')->count();
        return [
            Stat::make('Total Hosts', $total_host)
            ->label('Total Hosts')
            ->color('primary')
            ->description('Total hosts')
            ->icon('heroicon-s-users'),
            Stat::make('Active Hosts ', $active_host)
            ->label('Active Hosts')
            ->color('green')
            ->description('Active hosts')
            ->icon('heroicon-s-users')
            ->color('success'),
            Stat::make('Pending Approval ', $pending_host)
            ->label('Pending Approval')
            ->color('warning')
            ->description('Pending hosts')
            ->icon('heroicon-s-users')
            ->color('warning'),
            Stat::make('Banded Hosts', $blocked_host)
            ->label('Banded Hosts')
            ->color('danger')
            ->description('Banded hosts')
            ->icon('heroicon-s-users')
        ];
    }
}
