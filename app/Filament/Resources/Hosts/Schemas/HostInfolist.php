<?php

namespace App\Filament\Resources\Hosts\Schemas;

use Filament\Schemas\Schema;
use Filament\Infolists\Components\TextEntry;
use Filament\Infolists\Components\ImageEntry;

class HostInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                ImageEntry::make('profile_image')
                    ->label('Profile Image')
                    ->circular()
                    ->size(80),

                TextEntry::make('full_name')
                    ->label('Name'),

                TextEntry::make('email')
                    ->label('Email'),

                TextEntry::make('status')
                    ->label('Status')
                    ->badge()
                    ->color(fn ($state): string => match (strtolower($state)) {
                        'pending' => 'info',
                        'approved' => 'success',
                        'blocked' => 'danger',
                        default => 'warning',
                    }),

                TextEntry::make('created_at')
                    ->label('Wedding Date')
                    ->date(),
            ]);
    }
}
