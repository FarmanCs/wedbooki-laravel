<?php

namespace App\Filament\Resources\Hosts\Schemas;

use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Group;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Infolists\Components\TextEntry;
use Filament\Infolists\Components\ImageEntry;

class HostInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([

                Section::make('Host Information')
                    ->icon('heroicon-o-user')
                    ->schema([

                        Grid::make(12)->schema([

                            ImageEntry::make('profile_image')
                                ->label('')
                                ->circular()
                                ->size(100)
                                ->columnSpan(3),

                            Group::make([
                                TextEntry::make('full_name')
                                    ->label('Name')
                                    ->weight('bold'),

                                TextEntry::make('email')
                                    ->label('Email')
                                    ->icon('heroicon-o-envelope'),

                                TextEntry::make('status')
                                    ->label('Status')
                                    ->badge()
                                    ->color(fn($state) => match (strtolower($state)) {
                                        'pending' => 'info',
                                        'approved' => 'success',
                                        'blocked' => 'danger',
                                        default => 'warning',
                                    }),
                            ])->columnSpan(9),

                        ]),
                    ]),

                Section::make('Event Details')
                    ->icon('heroicon-o-calendar-days')
                    ->columns(2)
                    ->schema([
                        TextEntry::make('event_type')
                            ->label('Event Type')
                            ->badge(),

                        TextEntry::make('wedding_date')
                            ->label('Event Date')
                            ->date(),

                        TextEntry::make('event_budget')
                            ->label('Event Budget')
                            ->money('PKR'),

                        TextEntry::make('estimated_guests')
                            ->label('Estimated Guests'),
                    ]),
            ]);
    }
}
