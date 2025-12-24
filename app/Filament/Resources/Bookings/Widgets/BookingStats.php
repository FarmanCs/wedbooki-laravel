<?php

namespace App\Filament\Resources\Bookings\Widgets;

use App\Models\Vendor\Booking;
use Filament\Widgets\StatsOverviewWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class BookingStats extends StatsOverviewWidget
{
    protected int|string|array $columnSpan = 'full';

    protected function getColumns(): int
    {
        return 4;
    }

    protected function getStats(): array
    {
        return [
            // 🔹 Total Overview
            Stat::make('Total Bookings', Booking::count())
                ->color('Green')
                ->icon('heroicon-o-clipboard-document-list'),

            Stat::make('Accepted', Booking::where('status', 'accepted')->count())
                ->description('Pending finalization')
                ->color('success')
                ->icon('heroicon-o-check-circle'),

            Stat::make('Pending Approval', Booking::where('status', 'pending')->count())
                ->description('Requires attention')
                ->descriptionIcon('heroicon-m-exclamation-circle')
                ->color('warning')
                ->icon('heroicon-o-clock'),

            Stat::make('Paid Bookings', function () {
                return   Booking::whereIn('payment_status', ['advancePaid','fullyPaid'])->count();

            })
                ->description('Total paid bookings')
                ->color('success')
                ->icon('heroicon-o-currency-dollar'),


            Stat::make('Cancelled', Booking::whereIn('status', ['rejected', 'cancelled'])->count())
                ->description('cancelled bookings')
                ->color('danger')
                ->icon('heroicon-o-x-circle'),

            Stat::make('Unpaid  ', Booking::where('payment_status', 'unpaid')->count())
                ->description('No payment received')
                ->color('danger')
                ->icon('heroicon-o-credit-card'),

            Stat::make('Refunded', Booking::where('payment_status', 'refunded')->count())
                ->description('Returned funds')
                ->color('primary')
                ->icon('heroicon-o-arrow-uturn-left'),

            Stat::make('Revenue', function () {
                $revenue = Booking::whereIn('payment_status', ['advancePaid', 'fullyPaid'])->sum('final_amount');
                return '$' . number_format($revenue, 2);
            })
                ->description('Total revenue')
                ->color('success')
                ->icon('heroicon-o-sparkles')
        ];
    }
}
