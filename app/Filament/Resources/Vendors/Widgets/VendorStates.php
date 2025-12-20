<?php

namespace App\Filament\Resources\Vendors\Widgets;

use App\Models\Vendor\Vendor;
use Filament\Widgets\StatsOverviewWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class VendorStates extends StatsOverviewWidget
{
    protected function getStats(): array
    {
        $total_vendors = Vendor::query()->count();
        $active_vendors = Vendor::where('is_active', 1)->count();
        $pending_approvals = Vendor::where('profile_verification', 'pending')->count();
        $banned_vendors = Vendor::where('profile_verification', 'banned')->count();

        return [
            Stat::make('Total Vendors', $total_vendors)
                ->description('Total Vendors')
                ->descriptionIcon('heroicon-m-arrow-trending-up')
                ->color('primary'),
//                ->chart([7, 2, 10, 3, 15, 4, 17]),
            Stat::make('Active Vendors', $active_vendors)
            ->description('Active Vendors')
            ->color('success'),
            Stat::make('Pending Approvals', $pending_approvals)
            ->description('Pending Approvals')
            ->color('warning'),
            Stat::make('Banded Vendors', $banned_vendors)
            ->description('Banned Vendors')
            ->color('danger'),

        ];
    }
}
