<?php

namespace App\Filament\Resources\Bookings\Widgets;

use App\Models\Vendor\Booking;
use Filament\Widgets\StatsOverviewWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class BookingStats extends StatsOverviewWidget
{
    protected function getStats(): array
    {
        $booking = Booking::count();
        return [
            Stat::make('Total Bookings', $booking),
            Stat::make('active_hosts', '343'),
            Stat::make('active_hosts', '344'),
            Stat::make('active_hosts', '345'),
        ];
    }
}
