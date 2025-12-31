<?php

namespace App\Filament\Resources\Communications\Schemas;

use Filament\Infolists\Components\TextEntry;

class CommunicationsInfolist
{
    protected int|string|array $columnSpan = 'full';

    public static function schema(): array
    {
        return [
            TextEntry::make('title')
                ->label('Title')
                ->weight('bold')
                ->size('lg')
                ->columnSpan(4), // full width for title

            TextEntry::make('message')
                ->label('Message')
                ->wrap()
                ->markdown()
                ->columnSpanFull(),

            TextEntry::make('type')
                ->label('Type')
                ->badge()
                ->color(fn (string $state): string => match (strtolower($state)) {
                    'info' => 'info',
                    'warning' => 'warning',
                    'success' => 'success',
                    'danger' => 'danger',
                    default => 'gray',
                })
                ->columnSpan(1),

            TextEntry::make('recipients')
                ->label('Recipients')
                ->badge()
                ->separator(',')
                ->columnSpan(1),

            TextEntry::make('delivery_method')
                ->label('Delivery Method')
                ->badge()
                ->color('primary')
                ->columnSpan(1),

            TextEntry::make('send_mode')
                ->label('Send Mode')
                ->badge()
                ->columnSpan(1),

            TextEntry::make('scheduled_at')
                ->label('Scheduled At')
                ->dateTime('d M Y H:i')
                ->placeholder('Not scheduled')
                ->columnSpan(2), // can take half-width

            TextEntry::make('status')
                ->label('Status')
                ->badge()
                ->color(fn (string $state): string => match (strtolower($state)) {
                    'sent' => 'success',
                    'pending' => 'warning',
                    'failed' => 'danger',
                    'scheduled' => 'info',
                    'draft' => 'gray',
                    default => 'gray',
                })
                ->columnSpan(2),

            TextEntry::make('sent_count')
                ->label('Total Sent')
                ->default(0)
                ->numeric()
                ->columnSpan(1),

            TextEntry::make('failed_count')
                ->label('Failed')
                ->default(0)
                ->numeric()
                ->color('danger')
                ->columnSpan(1),

            TextEntry::make('created_by.name')
                ->label('Created By')
                ->placeholder('System')
                ->columnSpan(1),

            TextEntry::make('updated_by.name')
                ->label('Last Updated By')
                ->placeholder('N/A')
                ->columnSpan(1),

            TextEntry::make('created_at')
                ->label('Created At')
                ->dateTime('d M Y H:i:s')
                ->columnSpan(2),

            TextEntry::make('updated_at')
                ->label('Updated At')
                ->dateTime('d M Y H:i:s')
                ->since()
                ->columnSpan(2),
        ];
    }
}
