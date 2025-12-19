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
        $accpted_booking = Booking::where('status', 'Accepted')->count();
        $pending_booking = Booking::where('status', 'Pending')->count();
        $paid_booking = Booking::where('status', 'confirm')->count();
        return [
            Stat::make('Total Bookings', $booking),
            Stat::make('Accepted Bookings', $accpted_booking),
            Stat::make('Pending Bookings', $pending_booking),
            Stat::make('Paid Booking', $paid_booking),
        ];
    }
}
