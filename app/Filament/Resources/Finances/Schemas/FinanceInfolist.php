<?php

namespace App\Filament\Resources\Finances\Schemas;

use Filament\Infolists\Components\ImageEntry;
use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Group;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Support\Enums\FontWeight;

class FinanceInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->schema([
                // Main Transaction Overview
                Section::make('Transaction Overview')
                    ->schema([
                        Grid::make(2)
                            ->schema([
                                Group::make([
                                    TextEntry::make('id')
                                        ->label('Transaction ID')
                                        ->badge()
                                        ->color('gray')
                                        ->weight(FontWeight::Bold),

                                    TextEntry::make('status')
                                        ->label('Status')
                                        ->badge()
                                        ->size('lg')
                                        ->icon(fn(string $state): string => match ($state) {
                                            'pending' => 'heroicon-o-clock',
                                            'processing' => 'heroicon-o-arrow-path',
                                            'completed' => 'heroicon-o-check-circle',
                                            'failed' => 'heroicon-o-x-circle',
                                            'refunded' => 'heroicon-o-arrow-uturn-left',
                                            'cancelled' => 'heroicon-o-x-mark',
                                            default => 'heroicon-o-question-mark-circle',
                                        })
                                        ->color(fn(string $state): string => match ($state) {
                                            'pending' => 'warning',
                                            'processing' => 'info',
                                            'completed' => 'success',
                                            'failed' => 'danger',
                                            'refunded' => 'gray',
                                            'cancelled' => 'danger',
                                            default => 'gray',
                                        }),

                                    TextEntry::make('payment_method')
                                        ->label('Payment Method')
                                        ->badge()
                                        ->icon('heroicon-o-credit-card')
                                        ->color('primary')
                                        ->formatStateUsing(fn(string $state): string => str_replace('_', ' ', ucwords($state, '_'))),
                                ]),

                                Group::make([
                                    TextEntry::make('amount')
                                        ->label('Total Amount')
                                        ->money('USD')
                                        ->size('lg')
                                        ->weight(FontWeight::Bold)
                                        ->icon('heroicon-o-banknotes')
                                        ->color('success'),

                                    TextEntry::make('paid_at')
                                        ->label('Payment Date')
                                        ->dateTime('M d, Y h:i A')
                                        ->icon('heroicon-o-calendar')
                                        ->color('gray')
                                        ->placeholder('Not paid yet'),

                                    TextEntry::make('payment_reference')
                                        ->label('Reference Number')
                                        ->icon('heroicon-o-document-text')
                                        ->color('primary')
                                        ->copyable()
                                        ->copyMessage('Reference copied!')
                                        ->placeholder('N/A'),
                                ]),
                            ]),
                    ])
                    ->icon('heroicon-o-currency-dollar')
                    ->iconColor('success')
                    ->collapsible(),

                // Booking Details
                Section::make('Booking Information')
                    ->schema([
                        Grid::make(3)
                            ->schema([
                                TextEntry::make('booking.custom_booking_id')
                                    ->label('Booking ID')
                                    ->icon('heroicon-o-ticket')
                                    ->color('warning')
                                    ->badge()
                                    ->weight(FontWeight::Bold)
                                    ->placeholder('N/A'),

                                TextEntry::make('booking.status')
                                    ->label('Booking Status')
                                    ->badge()
                                    ->icon('heroicon-o-check-badge')
                                    ->color(fn($state) => match ($state) {
                                        'pending' => 'warning',
                                        'confirmed' => 'success',
                                        'completed' => 'success',
                                        'cancelled' => 'danger',
                                        default => 'gray',
                                    })
                                    ->placeholder('N/A'),

                                TextEntry::make('booking.payment_status')
                                    ->label('Payment Status')
                                    ->badge()
                                    ->icon('heroicon-o-credit-card')
                                    ->color(fn($state) => match ($state) {
                                        'paid' => 'success',
                                        'partial' => 'warning',
                                        'unpaid' => 'danger',
                                        default => 'gray',
                                    })
                                    ->placeholder('N/A'),
                            ]),

                        Grid::make(2)
                            ->schema([
                                TextEntry::make('booking.event_date')
                                    ->label('Event Date')
                                    ->date('M d, Y')
                                    ->icon('heroicon-o-calendar-days')
                                    ->color('primary')
                                    ->weight(FontWeight::SemiBold)
                                    ->placeholder('N/A'),

                                TextEntry::make('booking.time_slot')
                                    ->label('Time Slot')
                                    ->icon('heroicon-o-clock')
                                    ->color('gray')
                                    ->placeholder('N/A'),

                                TextEntry::make('booking.guests')
                                    ->label('Number of Guests')
                                    ->icon('heroicon-o-user-group')
                                    ->color('info')
                                    ->numeric()
                                    ->placeholder('N/A'),

                                TextEntry::make('booking.timezone')
                                    ->label('Timezone')
                                    ->icon('heroicon-o-globe-alt')
                                    ->color('gray')
                                    ->placeholder('N/A'),
                            ]),

                        Grid::make(3)
                            ->schema([
                                TextEntry::make('booking.advance_amount')
                                    ->label('Advance Amount')
                                    ->money('USD')
                                    ->icon('heroicon-o-banknotes')
                                    ->color('warning')
                                    ->placeholder('N/A'),

                                TextEntry::make('booking.advance_percentage')
                                    ->label('Advance %')
                                    ->suffix('%')
                                    ->icon('heroicon-o-calculator')
                                    ->color('gray')
                                    ->placeholder('N/A'),

                                TextEntry::make('booking.final_amount')
                                    ->label('Final Amount')
                                    ->money('USD')
                                    ->icon('heroicon-o-banknotes')
                                    ->color('success')
                                    ->weight(FontWeight::Bold)
                                    ->placeholder('N/A'),
                            ]),

                        Grid::make(2)
                            ->schema([
                                TextEntry::make('booking.advance_due_date')
                                    ->label('Advance Due Date')
                                    ->date('M d, Y')
                                    ->icon('heroicon-o-calendar')
                                    ->color('warning')
                                    ->placeholder('N/A'),

                                TextEntry::make('booking.final_due_date')
                                    ->label('Final Due Date')
                                    ->date('M d, Y')
                                    ->icon('heroicon-o-calendar')
                                    ->color('danger')
                                    ->placeholder('N/A'),
                            ]),
                    ])
                    ->icon('heroicon-o-ticket')
                    ->iconColor('warning')
                    ->collapsible()
                    ->visible(fn($record) => $record->booking_id !== null),

                // Host Information
                Section::make('Host Information')
                    ->schema([
                        Grid::make(3)
                            ->schema([
                                ImageEntry::make('host.profile_image')
                                    ->label('Profile')
                                    ->circular()
                                    ->defaultImageUrl(fn($record) => 'https://ui-avatars.com/api/?name=' . urlencode($record->host?->full_name ?? 'Host'))
                                    ->placeholder('No image'),

                                Group::make([
                                    TextEntry::make('host.full_name')
                                        ->label('Full Name')
                                        ->icon('heroicon-o-user')
                                        ->color('primary')
                                        ->weight(FontWeight::Bold)
                                        ->size('lg')
                                        ->placeholder('N/A'),

                                    TextEntry::make('host.email')
                                        ->label('Email')
                                        ->icon('heroicon-o-envelope')
                                        ->color('gray')
                                        ->copyable()
                                        ->placeholder('N/A'),

                                    TextEntry::make('host.phone_no')
                                        ->label('Phone')
                                        ->icon('heroicon-o-phone')
                                        ->color('gray')
                                        ->copyable()
                                        ->placeholder('N/A'),
                                ])->columnSpan(2),
                            ]),

                        Grid::make(3)
                            ->schema([
                                TextEntry::make('host.country')
                                    ->label('Country')
                                    ->icon('heroicon-o-globe-alt')
                                    ->color('info')
                                    ->placeholder('N/A'),

                                TextEntry::make('host.event_type')
                                    ->label('Event Type')
                                    ->badge()
                                    ->icon('heroicon-o-sparkles')
                                    ->color('purple')
                                    ->placeholder('N/A'),

                                TextEntry::make('host.estimated_guests')
                                    ->label('Estimated Guests')
                                    ->icon('heroicon-o-user-group')
                                    ->numeric()
                                    ->color('gray')
                                    ->placeholder('N/A'),
                            ]),

                        Grid::make(2)
                            ->schema([
                                TextEntry::make('host.wedding_date')
                                    ->label('Wedding Date')
                                    ->date('M d, Y')
                                    ->icon('heroicon-o-heart')
                                    ->color('rose')
                                    ->placeholder('N/A'),

                                TextEntry::make('host.event_budget')
                                    ->label('Event Budget')
                                    ->money('USD')
                                    ->icon('heroicon-o-currency-dollar')
                                    ->color('success')
                                    ->placeholder('N/A'),
                            ]),

                        TextEntry::make('host.about')
                            ->label('About Host')
                            ->icon('heroicon-o-information-circle')
                            ->color('gray')
                            ->placeholder('No information available')
                            ->columnSpanFull(),
                    ])
                    ->icon('heroicon-o-user-circle')
                    ->iconColor('primary')
                    ->collapsible()
                    ->visible(fn($record) => $record->host_id !== null),

                // Vendor Information
                Section::make('Vendor Information')
                    ->schema([
                        Grid::make(3)
                            ->schema([
                                ImageEntry::make('vendor.profile_image')
                                    ->label('Profile')
                                    ->circular()
                                    ->defaultImageUrl(fn($record) => 'https://ui-avatars.com/api/?name=' . urlencode($record->vendor?->full_name ?? 'Vendor'))
                                    ->placeholder('No image'),

                                Group::make([
                                    TextEntry::make('vendor.full_name')
                                        ->label('Full Name')
                                        ->icon('heroicon-o-user')
                                        ->color('success')
                                        ->weight(FontWeight::Bold)
                                        ->size('lg')
                                        ->placeholder('N/A'),

                                    TextEntry::make('vendor.email')
                                        ->label('Email')
                                        ->icon('heroicon-o-envelope')
                                        ->color('gray')
                                        ->copyable()
                                        ->placeholder('N/A'),

                                    TextEntry::make('vendor.phone_no')
                                        ->label('Phone')
                                        ->icon('heroicon-o-phone')
                                        ->color('gray')
                                        ->copyable()
                                        ->placeholder('N/A'),
                                ])->columnSpan(2),
                            ]),

                        Grid::make(3)
                            ->schema([
                                TextEntry::make('vendor.custom_vendor_id')
                                    ->label('Vendor ID')
                                    ->badge()
                                    ->icon('heroicon-o-identification')
                                    ->color('gray')
                                    ->placeholder('N/A'),

                                TextEntry::make('vendor.category.name')
                                    ->label('Category')
                                    ->badge()
                                    ->icon('heroicon-o-tag')
                                    ->color('info')
                                    ->placeholder('N/A'),

                                TextEntry::make('vendor.years_of_experience')
                                    ->label('Experience')
                                    ->suffix(' years')
                                    ->icon('heroicon-o-academic-cap')
                                    ->color('warning')
                                    ->placeholder('N/A'),
                            ]),

                        Grid::make(3)
                            ->schema([
                                TextEntry::make('vendor.country')
                                    ->label('Country')
                                    ->icon('heroicon-o-globe-alt')
                                    ->color('info')
                                    ->placeholder('N/A'),

                                TextEntry::make('vendor.city')
                                    ->label('City')
                                    ->icon('heroicon-o-map-pin')
                                    ->color('gray')
                                    ->placeholder('N/A'),

                                TextEntry::make('vendor.team_members')
                                    ->label('Team Members')
                                    ->icon('heroicon-o-user-group')
                                    ->numeric()
                                    ->color('gray')
                                    ->placeholder('N/A'),
                            ]),

                        Grid::make(2)
                            ->schema([
                                TextEntry::make('vendor.languages')
                                    ->label('Languages')
                                    ->badge()
                                    ->icon('heroicon-o-language')
                                    ->color('blue')
                                    ->placeholder('N/A'),

                                TextEntry::make('vendor.specialties')
                                    ->label('Specialties')
                                    ->badge()
                                    ->icon('heroicon-o-star')
                                    ->color('yellow')
                                    ->placeholder('N/A'),
                            ]),

                        Grid::make(2)
                            ->schema([
                                TextEntry::make('vendor.profile_verification')
                                    ->label('Profile Verification')
                                    ->badge()
                                    ->icon(fn($state) => $state === 'verified' ? 'heroicon-o-check-badge' : 'heroicon-o-x-circle')
                                    ->color(fn($state) => $state === 'verified' ? 'success' : 'warning')
                                    ->placeholder('N/A'),

                                TextEntry::make('vendor.email_verified')
                                    ->label('Email Verified')
                                    ->badge()
                                    ->icon(fn($state) => $state ? 'heroicon-o-check-circle' : 'heroicon-o-x-circle')
                                    ->color(fn($state) => $state ? 'success' : 'danger')
                                    ->formatStateUsing(fn($state) => $state ? 'Verified' : 'Not Verified')
                                    ->placeholder('N/A'),
                            ]),

                        TextEntry::make('vendor.about')
                            ->label('About Vendor')
                            ->icon('heroicon-o-information-circle')
                            ->color('gray')
                            ->placeholder('No information available')
                            ->columnSpanFull(),
                    ])
                    ->icon('heroicon-o-briefcase')
                    ->iconColor('success')
                    ->collapsible()
                    ->visible(fn($record) => $record->vendor_id !== null),

                // Business Information
                Section::make('Business Information')
                    ->schema([
                        Grid::make(3)
                            ->schema([
                                ImageEntry::make('booking.business.logo')
                                    ->label('Business Logo')
                                    ->circular()
                                    ->defaultImageUrl(fn($record) => 'https://ui-avatars.com/api/?name=' . urlencode($record->booking?->business?->business_name ?? 'Business'))
                                    ->placeholder('No logo'),

                                Group::make([
                                    TextEntry::make('booking.business.business_name')
                                        ->label('Business Name')
                                        ->icon('heroicon-o-building-storefront')
                                        ->color('orange')
                                        ->weight(FontWeight::Bold)
                                        ->size('lg')
                                        ->placeholder('N/A'),

                                    TextEntry::make('booking.business.email')
                                        ->label('Email')
                                        ->icon('heroicon-o-envelope')
                                        ->color('gray')
                                        ->copyable()
                                        ->placeholder('N/A'),

                                    TextEntry::make('booking.business.phone_no')
                                        ->label('Phone')
                                        ->icon('heroicon-o-phone')
                                        ->color('gray')
                                        ->copyable()
                                        ->placeholder('N/A'),
                                ])->columnSpan(2),
                            ]),

                        Grid::make(3)
                            ->schema([
                                TextEntry::make('booking.business.business_type')
                                    ->label('Business Type')
                                    ->badge()
                                    ->icon('heroicon-o-tag')
                                    ->color('purple')
                                    ->placeholder('N/A'),

                                TextEntry::make('booking.business.category.name')
                                    ->label('Category')
                                    ->badge()
                                    ->icon('heroicon-o-squares-2x2')
                                    ->color('info')
                                    ->placeholder('N/A'),

                                TextEntry::make('booking.business.verification_status')
                                    ->label('Verification')
                                    ->badge()
                                    ->icon(fn($state) => $state === 'verified' ? 'heroicon-o-check-badge' : 'heroicon-o-clock')
                                    ->color(fn($state) => match ($state) {
                                        'verified' => 'success',
                                        'pending' => 'warning',
                                        'rejected' => 'danger',
                                        default => 'gray',
                                    })
                                    ->placeholder('N/A'),
                            ]),

                        Grid::make(2)
                            ->schema([
                                TextEntry::make('booking.business.address')
                                    ->label('Address')
                                    ->icon('heroicon-o-map-pin')
                                    ->color('gray')
                                    ->placeholder('N/A'),

                                TextEntry::make('booking.business.website')
                                    ->label('Website')
                                    ->icon('heroicon-o-globe-alt')
                                    ->color('primary')
                                    ->url(fn($state) => $state)
                                    ->openUrlInNewTab()
                                    ->placeholder('N/A'),
                            ]),
                    ])
                    ->icon('heroicon-o-building-office-2')
                    ->iconColor('orange')
                    ->collapsible()
                    ->visible(fn($record) => $record->booking?->business_id !== null),

                // Package Information
                Section::make('Package Details')
                    ->schema([
                        Grid::make(3)
                            ->schema([
                                TextEntry::make('booking.package.name')
                                    ->label('Package Name')
                                    ->icon('heroicon-o-cube')
                                    ->color('indigo')
                                    ->weight(FontWeight::Bold)
                                    ->placeholder('N/A'),

                                TextEntry::make('booking.package.price')
                                    ->label('Package Price')
                                    ->money('USD')
                                    ->icon('heroicon-o-currency-dollar')
                                    ->color('success')
                                    ->weight(FontWeight::SemiBold)
                                    ->placeholder('N/A'),

                                TextEntry::make('booking.package.type')
                                    ->label('Package Type')
                                    ->badge()
                                    ->icon('heroicon-o-tag')
                                    ->color('info')
                                    ->placeholder('N/A'),
                            ]),

                        TextEntry::make('booking.package.description')
                            ->label('Description')
                            ->icon('heroicon-o-document-text')
                            ->color('gray')
                            ->placeholder('No description available')
                            ->columnSpanFull(),
                    ])
                    ->icon('heroicon-o-cube-transparent')
                    ->iconColor('indigo')
                    ->collapsible()
                    ->visible(fn($record) => $record->booking?->package_id !== null),

                // Sender Information (Polymorphic)
                Section::make('Sender Information')
                    ->schema([
                        Grid::make(3)
                            ->schema([
                                TextEntry::make('sender_name')
                                    ->label('Name')
                                    ->icon('heroicon-o-user')
                                    ->color('primary')
                                    ->weight(FontWeight::SemiBold)
                                    ->placeholder('N/A'),

                                TextEntry::make('sender_type')
                                    ->label('Type')
                                    ->badge()
                                    ->icon('heroicon-o-tag')
                                    ->color('info')
                                    ->formatStateUsing(fn($state): string => $state ? class_basename($state) : 'N/A'),

                                TextEntry::make('sender_id')
                                    ->label('ID')
                                    ->icon('heroicon-o-identification')
                                    ->color('gray')
                                    ->placeholder('N/A'),
                            ]),
                    ])
                    ->icon('heroicon-o-arrow-up-circle')
                    ->iconColor('primary')
                    ->collapsible(),

                // Receiver Information (Polymorphic)
                Section::make('Receiver Information')
                    ->schema([
                        Grid::make(3)
                            ->schema([
                                TextEntry::make('receiver_name')
                                    ->label('Name')
                                    ->icon('heroicon-o-user')
                                    ->color('success')
                                    ->weight(FontWeight::SemiBold)
                                    ->placeholder('N/A'),

                                TextEntry::make('receiver_type')
                                    ->label('Type')
                                    ->badge()
                                    ->icon('heroicon-o-tag')
                                    ->color('success')
                                    ->formatStateUsing(fn($state): string => $state ? class_basename($state) : 'N/A'),

                                TextEntry::make('receiver_id')
                                    ->label('ID')
                                    ->icon('heroicon-o-identification')
                                    ->color('gray')
                                    ->placeholder('N/A'),
                            ]),
                    ])
                    ->icon('heroicon-o-arrow-down-circle')
                    ->iconColor('success')
                    ->collapsible(),

                // Payment Gateway Details
                Section::make('Payment Gateway Details')
                    ->schema([
                        Grid::make(2)
                            ->schema([
                                TextEntry::make('acquirer_ref')
                                    ->label('Acquirer Reference')
                                    ->icon('heroicon-o-finger-print')
                                    ->color('gray')
                                    ->copyable()
                                    ->placeholder('N/A'),

                                TextEntry::make('profile_id')
                                    ->label('Profile ID')
                                    ->icon('heroicon-o-user-circle')
                                    ->color('gray')
                                    ->placeholder('N/A'),

                                TextEntry::make('tran_type')
                                    ->label('Transaction Type')
                                    ->icon('heroicon-o-arrows-right-left')
                                    ->badge()
                                    ->color('info')
                                    ->placeholder('N/A'),

                                TextEntry::make('tran_class')
                                    ->label('Transaction Class')
                                    ->icon('heroicon-o-squares-2x2')
                                    ->badge()
                                    ->color('primary')
                                    ->placeholder('N/A'),

                                TextEntry::make('cart_id')
                                    ->label('Cart ID')
                                    ->icon('heroicon-o-shopping-cart')
                                    ->color('gray')
                                    ->placeholder('N/A'),

                                TextEntry::make('cart_currency')
                                    ->label('Currency')
                                    ->icon('heroicon-o-currency-dollar')
                                    ->badge()
                                    ->color('success')
                                    ->placeholder('N/A'),
                            ]),

                        TextEntry::make('redirect_url')
                            ->label('Redirect URL')
                            ->icon('heroicon-o-link')
                            ->color('primary')
                            ->url(fn($state) => $state)
                            ->openUrlInNewTab()
                            ->placeholder('N/A')
                            ->columnSpanFull(),
                    ])
                    ->icon('heroicon-o-server-stack')
                    ->iconColor('primary')
                    ->collapsed()
                    ->collapsible(),

                // Additional Information
                Section::make('Additional Information')
                    ->schema([
                        TextEntry::make('comments')
                            ->label('Comments')
                            ->icon('heroicon-o-chat-bubble-left-right')
                            ->color('gray')
                            ->placeholder('No comments')
                            ->columnSpanFull(),

                        Grid::make(1)
                            ->schema([
                                TextEntry::make('request_body')
                                    ->label('Request Body')
                                    ->icon('heroicon-o-code-bracket')
                                    ->color('slate')
                                    ->formatStateUsing(fn($state) => $state ? json_encode($state, JSON_PRETTY_PRINT) : 'N/A')
                                    ->copyable()
                                    ->columnSpanFull(),

                                TextEntry::make('click_pay_response')
                                    ->label('ClickPay Response')
                                    ->icon('heroicon-o-arrow-down-tray')
                                    ->color('blue')
                                    ->formatStateUsing(fn($state) => $state ? json_encode($state, JSON_PRETTY_PRINT) : 'N/A')
                                    ->copyable()
                                    ->columnSpanFull(),

                                TextEntry::make('click_pay_callback')
                                    ->label('ClickPay Callback')
                                    ->icon('heroicon-o-arrow-uturn-right')
                                    ->color('indigo')
                                    ->formatStateUsing(fn($state) => $state ? json_encode($state, JSON_PRETTY_PRINT) : 'N/A')
                                    ->copyable()
                                    ->columnSpanFull(),
                            ]),
                    ])
                    ->icon('heroicon-o-information-circle')
                    ->iconColor('gray')
                    ->collapsed()
                    ->collapsible(),

                // Timestamps
                Section::make('Timestamps')
                    ->schema([
                        Grid::make(3)
                            ->schema([
                                TextEntry::make('created_at')
                                    ->label('Created At')
                                    ->dateTime('M d, Y h:i A')
                                    ->icon('heroicon-o-plus-circle')
                                    ->color('success'),

                                TextEntry::make('updated_at')
                                    ->label('Updated At')
                                    ->dateTime('M d, Y h:i A')
                                    ->icon('heroicon-o-pencil-square')
                                    ->color('warning'),

                                TextEntry::make('deleted_at')
                                    ->label('Deleted At')
                                    ->dateTime('M d, Y h:i A')
                                    ->icon('heroicon-o-trash')
                                    ->color('danger')
                                    ->placeholder('Not deleted'),
                            ]),
                    ])
                    ->icon('heroicon-o-clock')
                    ->iconColor('gray')
                    ->collapsed()
                    ->collapsible(),
            ]);
    }
}
