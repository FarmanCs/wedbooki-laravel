<?php

namespace App\Filament\Resources\Vendors\Widgets;

use Filament\Widgets\StatsOverviewWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class VendorStates extends StatsOverviewWidget
{
    protected function getStats(): array
    {
        return [
            Stat::make('Total Vendors', '192.1k')
                ->description('Total Vendors')
                ->descriptionIcon('heroicon-m-arrow-trending-up')
                ->color('primary')
                ->chart([7, 2, 10, 3, 15, 4, 17]),
            Stat::make('Active Vendors', '192.1k'),
            Stat::make('Pending Vendors', '192.1k'),
            Stat::make('Banded Vendors', '192.1k'),

        ];
    }
}
