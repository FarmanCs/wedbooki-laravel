<?php

namespace App\Filament\Resources\Bookings\Schemas;

use Filament\Infolists\Components\TextEntry;
use Filament\Infolists\Components\IconEntry;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Group;

class BookingInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([

                Grid::make(3)->schema([
                    TextEntry::make('custom_booking_id')
                        ->label('Booking ID')
                        ->icon('heroicon-o-hashtag')
                        ->iconColor('yellow')
                        ->badge()
                        ->color('indigo')
                        ->copyable()
                        ->size('sm')
                        ->weight('bold'),

                    TextEntry::make('status')
                        ->label('Status')
                        ->icon('heroicon-o-signal')
                        ->badge()
                        ->size('sm')
                        ->color(fn(string $state) => match ($state) {
                            'pending' => 'warning',
                            'accepted', 'completed' => 'success',
                            'rejected' => 'danger',
                            default => 'gray',
                        }),

                    TextEntry::make('payment_status')
                        ->label('Payment')
                        ->icon('heroicon-o-credit-card')
                        ->badge()
                        ->size('sm')
                        ->color(fn(string $state) => match ($state) {
                            'unpaid' => 'warning',
                            'advancePaid' => 'info',
                            'fullyPaid' => 'success',
                            default => 'gray',
                        }),
                ]),

                Grid::make(4)->schema([
                    TextEntry::make('event_date')
                        ->label('Event Date')
                        ->icon('heroicon-o-calendar')
                        ->date('D, d M Y')
                        ->weight('bold')
                        ->color('cyan'),

                    TextEntry::make('time_slot')
                        ->label('Slot')
                        ->icon('heroicon-o-clock')
                        ->badge()
                        ->color('blue'),

                    TextEntry::make('start_time')
                        ->label('Start')
                        ->icon('heroicon-o-play-circle')
                        ->dateTime('h:i A')
                        ->color('success'),

                    TextEntry::make('end_time')
                        ->label('End')
                        ->icon('heroicon-o-stop-circle')
                        ->dateTime('h:i A')
                        ->color('danger'),
                ]),
                Grid::make(3)->schema([
                    TextEntry::make('amount')
                        ->label('Total')
                        ->money('PKR')
                        ->icon('heroicon-o-banknotes')
                        ->size('xl')
                        ->weight('bold')
                        ->color('success'),

                    TextEntry::make('advance_amount')
                        ->label('Advance')
                        ->money('PKR')
                        ->icon('heroicon-o-currency-dollar')
                        ->color('info'),

                    TextEntry::make('final_amount')
                        ->label('Final')
                        ->money('PKR')
                        ->icon('heroicon-o-receipt-percent')
                        ->color('success'),
                ]),
                Grid::make(4)->schema([
                    IconEntry::make('advance_paid')
                        ->label('Advance Paid')
                        ->boolean(),

                    IconEntry::make('final_paid')
                        ->label('Final Paid')
                        ->boolean(),

                    TextEntry::make('advance_due_date')
                        ->label('Advance Due')
                        ->date('d M Y')
                        ->color(fn($state) => $state?->isPast() ? 'danger' : 'success'),

                    TextEntry::make('final_due_date')
                        ->label('Final Due')
                        ->date('d M Y')
                        ->color(fn($state) => $state?->isPast() ? 'danger' : 'success'),
                ]),
                Grid::make(4)->schema([
                    IconEntry::make('advance_paid')
                        ->label('Advance Paid')
                        ->boolean(),

                    IconEntry::make('final_paid')
                        ->label('Final Paid')
                        ->boolean(),

                    TextEntry::make('advance_due_date')
                        ->label('Advance Due')
                        ->date('d M Y')
                        ->color(fn($state) => $state?->isPast() ? 'danger' : 'success'),

                    TextEntry::make('final_due_date')
                        ->label('Final Due')
                        ->date('d M Y')
                        ->color(fn($state) => $state?->isPast() ? 'danger' : 'success'),
                ]),
                Grid::make(4)->schema([
                    TextEntry::make('host.full_name')
                        ->label('Host')
                        ->icon('heroicon-o-user')
                        ->weight('bold'),

                    TextEntry::make('vendor.full_name')
                        ->label('Vendor')
                        ->icon('heroicon-o-user-circle')
                        ->weight('bold'),

                    TextEntry::make('business.company_name')
                        ->label('Business')
                        ->icon('heroicon-o-building-storefront')
                        ->weight('bold'),

                    TextEntry::make('package.name')
                        ->label('Package')
                        ->icon('heroicon-o-gift')
                        ->weight('bold'),
                ]),
            ]);

    }
}
