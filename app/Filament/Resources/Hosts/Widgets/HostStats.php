<?php

namespace App\Filament\Resources\Hosts\Widgets;

use Filament\Widgets\StatsOverviewWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class HostStats extends StatsOverviewWidget
{
    protected function getStats(): array
    {
        return [
            Stat::make('Total Hosts', '132')
            ->label('Total Hosts')
            ->color('primary')
            ->description('Total hosts')
            ->icon('heroicon-s-users'),
            Stat::make('Active Hosts ', '132')
            ->label('Active Hosts')
            ->color('green')
            ->description('Active hosts')
            ->icon('heroicon-s-users')
            ->color('success'),
            Stat::make('Pending Hosts ', '132')
            ->label('Pending Hosts')
            ->color('warning')
            ->description('Pending hosts')
            ->icon('heroicon-s-users')
            ->color('warning'),
            Stat::make('Banded Hosts', '132')
            ->label('Banded Hosts')
            ->color('danger')
            ->description('Banded hosts')
            ->icon('heroicon-s-users')
        ];
    }
}
