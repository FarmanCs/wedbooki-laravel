<?php

namespace App\Filament\Resources\Bookings\Schemas;

use Filament\Infolists\Components\TextEntry;
use Filament\Infolists\Components\IconEntry;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class BookingInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                // 🎫 Booking Overview
                Section::make('Booking Overview')
                    ->icon('heroicon-o-clipboard-document-list')
                    ->iconColor('indigo')
                    ->columns(3)
                    ->schema([
                        TextEntry::make('custom_booking_id')
                            ->label('Booking ID')
                            ->icon('heroicon-o-hashtag')
                            ->iconColor('indigo')
                            ->badge()
                            ->color('indigo')
                            ->copyable()
                            ->copyMessage('Booking ID copied!')
                            ->size('lg')
                            ->weight('bold'),

                        TextEntry::make('status')
                            ->label('Booking Status')
                            ->icon('heroicon-o-signal')
                            ->badge()
                            ->size('lg')
                            ->color(fn(string $state): string => match ($state) {
                                'pending' => 'warning',
                                'accepted' => 'success',
                                'rejected' => 'danger',
                                'cancelled' => 'gray',
                                'confirmed' => 'info',
                                'completed' => 'success',
                                default => 'gray',
                            })
                            ->formatStateUsing(fn(string $state): string => ucfirst($state)),

                        TextEntry::make('payment_status')
                            ->label('Payment Status')
                            ->icon('heroicon-o-credit-card')
                            ->badge()
                            ->size('lg')
                            ->color(fn(string $state): string => match ($state) {
                                'unpaid' => 'warning',
                                'advancePaid' => 'info',
                                'fullyPaid' => 'success',
                                'refunded' => 'gray',
                                default => 'gray',
                            })
                            ->formatStateUsing(fn(string $state): string => match ($state) {
                                'advancePaid' => 'Advance Paid',
                                'fullyPaid' => 'Fully Paid',
                                'unpaid' => 'Unpaid',
                                'refunded' => 'Refunded',
                                default => $state,
                            }),

                        TextEntry::make('approved_at')
                            ->label('Approved At')
                            ->icon('heroicon-o-check-circle')
                            ->iconColor('success')
                            ->dateTime('M d, Y h:i A')
                            ->placeholder('Not approved yet')
                            ->color('success'),

                        TextEntry::make('payment_completed_at')
                            ->label('Payment Completed')
                            ->icon('heroicon-o-banknotes')
                            ->iconColor('green')
                            ->dateTime('M d, Y h:i A')
                            ->placeholder('Payment not completed')
                            ->color('success'),

                        IconEntry::make('is_synced_with_calendar')
                            ->label('Calendar Synced')
                            ->boolean()
                            ->trueIcon('heroicon-o-check-circle')
                            ->falseIcon('heroicon-o-x-circle')
                            ->trueColor('success')
                            ->falseColor('danger'),
                    ]),

                // 📅 Event Details
                Section::make('Event Details')
                    ->icon('heroicon-o-calendar-days')
                    ->iconColor('cyan')
                    ->columns(3)
                    ->schema([
                        TextEntry::make('event_date')
                            ->label('Event Date')
                            ->icon('heroicon-o-calendar')
                            ->iconColor('cyan')
                            ->date('l, F d, Y')
                            ->size('lg')
                            ->weight('bold')
                            ->color('cyan'),

                        TextEntry::make('time_slot')
                            ->label('Time Slot')
                            ->icon('heroicon-o-clock')
                            ->iconColor('blue')
                            ->badge()
                            ->color('blue')
                            ->size('lg')
                            ->formatStateUsing(fn(string $state): string => ucfirst($state)),

                        TextEntry::make('timezone')
                            ->label('Timezone')
                            ->icon('heroicon-o-globe-alt')
                            ->iconColor('slate')
                            ->placeholder('UTC'),

                        TextEntry::make('start_time')
                            ->label('Start Time')
                            ->icon('heroicon-o-play-circle')
                            ->iconColor('green')
                            ->dateTime('h:i A')
                            ->color('success')
                            ->weight('bold'),

                        TextEntry::make('end_time')
                            ->label('End Time')
                            ->icon('heroicon-o-stop-circle')
                            ->iconColor('red')
                            ->dateTime('h:i A')
                            ->color('danger')
                            ->weight('bold'),

                        TextEntry::make('guests')
                            ->label('Number of Guests')
                            ->icon('heroicon-o-user-group')
                            ->iconColor('purple')
                            ->suffix(' guests')
                            ->numeric()
                            ->size('lg')
                            ->color('purple'),
                    ]),

                // 💰 Payment Information
                Section::make('Payment Information')
                    ->icon('heroicon-o-currency-dollar')
                    ->iconColor('emerald')
                    ->columns(4)
                    ->schema([
                        TextEntry::make('amount')
                            ->label('Total Amount')
                            ->icon('heroicon-o-banknotes')
                            ->iconColor('emerald')
                            ->money('PKR')
                            ->size('xl')
                            ->weight('bold')
                            ->color('success')
                            ->columnSpan(2),

                        TextEntry::make('final_amount')
                            ->label('Final Amount')
                            ->icon('heroicon-o-receipt-percent')
                            ->iconColor('green')
                            ->money('PKR')
                            ->size('lg')
                            ->weight('bold')
                            ->color('success')
                            ->columnSpan(2),

                        TextEntry::make('advance_percentage')
                            ->label('Advance Percentage')
                            ->icon('heroicon-o-calculator')
                            ->iconColor('blue')
                            ->suffix('%')
                            ->numeric()
                            ->color('info'),

                        TextEntry::make('advance_amount')
                            ->label('Advance Amount')
                            ->icon('heroicon-o-currency-dollar')
                            ->iconColor('blue')
                            ->money('PKR')
                            ->weight('bold')
                            ->color('info'),

                        IconEntry::make('advance_paid')
                            ->label('Advance Payment Status')
                            ->boolean()
                            ->trueIcon('heroicon-o-check-circle')
                            ->falseIcon('heroicon-o-x-circle')
                            ->trueColor('success')
                            ->falseColor('danger'),

                        IconEntry::make('final_paid')
                            ->label('Final Payment Status')
                            ->boolean()
                            ->trueIcon('heroicon-o-check-circle')
                            ->falseIcon('heroicon-o-x-circle')
                            ->trueColor('success')
                            ->falseColor('danger'),

                        TextEntry::make('advance_due_date')
                            ->label('Advance Due Date')
                            ->icon('heroicon-o-calendar-days')
                            ->iconColor('amber')
                            ->date('M d, Y')
                            ->color(fn($state) => $state && $state->isPast() ? 'danger' : 'success')
                            ->weight(fn($state) => $state && $state->isPast() ? 'bold' : 'normal'),

                        TextEntry::make('final_due_date')
                            ->label('Final Due Date')
                            ->icon('heroicon-o-calendar-days')
                            ->iconColor('orange')
                            ->date('M d, Y')
                            ->color(fn($state) => $state && $state->isPast() ? 'danger' : 'success')
                            ->weight(fn($state) => $state && $state->isPast() ? 'bold' : 'normal'),
                    ]),

                // 🏢 Related Information
                Section::make('Related Information')
                    ->icon('heroicon-o-building-office-2')
                    ->iconColor('slate')
                    ->columns(2)
                    ->schema([
                        TextEntry::make('host.full_name')
                            ->label('Host')
                            ->icon('heroicon-o-user')
                            ->iconColor('violet')
                            ->formatStateUsing(fn($record) => $record->host?->full_name ?? 'No host assigned')
                            ->color('violet')
                            ->weight('bold'),

                        TextEntry::make('business_id')
                            ->label('Business')
                            ->icon('heroicon-o-building-storefront')
                            ->iconColor('blue')
                            ->formatStateUsing(fn($record) => $record->business?->name ?? 'No business assigned')
                            ->color('blue')
                            ->weight('bold'),

                        TextEntry::make('vendor_id')
                            ->label('Vendor')
                            ->icon('heroicon-o-user-circle')
                            ->iconColor('indigo')
                            ->formatStateUsing(fn($record) => $record->vendor?->name ?? 'No vendor assigned')
                            ->color('indigo')
                            ->weight('bold'),

                        TextEntry::make('package_id')
                            ->label('Package')
                            ->icon('heroicon-o-gift')
                            ->iconColor('pink')
                            ->formatStateUsing(fn($record) => $record->package?->name ?? 'No package selected')
                            ->color('pink')
                            ->weight('bold'),
                    ]),

            ]);
    }
}
