<?php

namespace App\Filament\Resources\Supports\Schemas;

use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class SupportInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->schema([
                // Header Section with Key Information
                Section::make('Support Query Overview')
                    ->schema([
                        Grid::make(3)
                            ->schema([
                                TextEntry::make('id')
                                    ->label('Ticket ID')
                                    ->badge()
                                    ->color('primary')
                                    ->formatStateUsing(fn($state) => '#' . $state),

                                TextEntry::make('status')
                                    ->label('Status')
                                    ->badge()
                                    ->color(fn($state) => match ($state) {
                                        'pending' => 'warning',
                                        'resolved' => 'success',
                                        'rejected' => 'danger',
                                        default => 'gray',
                                    })
                                    ->icon(fn($state) => match ($state) {
                                        'pending' => 'heroicon-o-clock',
                                        'resolved' => 'heroicon-o-check-circle',
                                        'rejected' => 'heroicon-o-x-circle',
                                        default => 'heroicon-o-question-mark-circle',
                                    })
                                    ->formatStateUsing(fn($state) => ucfirst($state)),

                                TextEntry::make('priority')
                                    ->label('Priority Level')
                                    ->badge()
                                    ->color(fn($state) => match ($state) {
                                        'low' => 'success',
                                        'medium' => 'warning',
                                        'high' => 'danger',
                                        default => 'gray',
                                    })
                                    ->icon(fn($state) => match ($state) {
                                        'low' => 'heroicon-o-arrow-down',
                                        'medium' => 'heroicon-o-minus',
                                        'high' => 'heroicon-o-arrow-up',
                                        default => 'heroicon-o-question-mark-circle',
                                    })
                                    ->formatStateUsing(fn($state) => ucfirst($state)),
                            ]),
                    ])
                    ->collapsible(),

                // Customer Information Section
                Section::make('Customer Information')
                    ->schema([
                        Grid::make(2)
                            ->schema([
                                TextEntry::make('full_name')
                                    ->label('Full Name')
                                    ->icon('heroicon-o-user')
                                    ->weight('bold')
                                    ->size('lg'),

                                TextEntry::make('email')
                                    ->label('Email Address')
                                    ->icon('heroicon-o-envelope')
                                    ->copyable()
                                    ->copyMessage('Email copied!')
                                    ->copyMessageDuration(1500)
                                    ->url(fn($record) => "mailto:{$record->email}")
                                    ->color('primary'),

                                TextEntry::make('phone_number')
                                    ->label('Phone Number')
                                    ->icon('heroicon-o-phone')
                                    ->copyable()
                                    ->copyMessage('Phone number copied!')
                                    ->url(fn($record) => "tel:{$record->phone_number}")
                                    ->color('primary'),

                                TextEntry::make('created_at')
                                    ->label('Submitted On')
                                    ->icon('heroicon-o-calendar')
                                    ->dateTime('d M Y, h:i A')
                                    ->color('gray'),
                            ]),
                    ])
                    ->icon('heroicon-o-user-circle')
                    ->collapsible(),

                // Query Details Section
                Section::make('Query Details')
                    ->schema([
                        TextEntry::make('subject')
                            ->label('Subject')
                            ->icon('heroicon-o-document-text')
                            ->size('lg')
                            ->weight('bold')
                            ->color('primary')
                            ->columnSpanFull(),

                        TextEntry::make('message')
                            ->label('Message')
                            ->icon('heroicon-o-chat-bubble-left-right')
                            ->columnSpanFull()
                            ->prose()
                            ->formatStateUsing(fn($state) => nl2br(e($state)))
                            ->html(),
                    ])
                    ->icon('heroicon-o-information-circle')
                    ->collapsible(),
            ]);
    }
}
