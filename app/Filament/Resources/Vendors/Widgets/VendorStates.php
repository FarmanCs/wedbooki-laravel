<?php

namespace App\Filament\Resources\Vendors\Widgets;

use App\Models\Vendor\Vendor;
use Filament\Widgets\StatsOverviewWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class VendorStates extends StatsOverviewWidget
{
    protected function getStats(): array
    {
        $totalVendors = Vendor::count();
        $activeVendors = Vendor::where('is_active', true)->count();
        $pendingApprovals = Vendor::where('profile_verification', 'under_review')->count();
        $bannedVendors = Vendor::where('profile_verification', 'banned')->count();

        return [
            Stat::make('Total Vendors', $totalVendors)
                ->description('Total vendors registered')
                ->descriptionIcon('heroicon-m-arrow-trending-up')
                ->color('primary'),

            Stat::make('Active Vendors', $activeVendors)
                ->description('Currently active vendors')
                ->color('success'),

            Stat::make('Pending Approvals', $pendingApprovals)
                ->description('Vendors under review')
                ->color('warning'),

            Stat::make('Banned Vendors', $bannedVendors)
                ->description('Vendors banned from platform')
                ->color('danger'),
        ];
    }
}
