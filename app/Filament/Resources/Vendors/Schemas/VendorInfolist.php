<?php

namespace App\Filament\Resources\Vendors\Schemas;

use Filament\Infolists\Components\ImageEntry;
use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class VendorInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Profile')
                    ->columns(2)
                    ->schema([
                        ImageEntry::make('profile_image')
                            ->label('Profile Image')
                            ->circular()
                            ->disk('s3')
                            ->size(200),

                        TextEntry::make('full_name')
                            ->label('Full Name')
                            ->size(100),
                    ]),

                Section::make('Business Information')
                    ->schema([
                        TextEntry::make('category.type')
                            ->label('Category')
                            ->placeholder('—'),

                        TextEntry::make('businesses')
                            ->label('Businesses')
                            ->state(fn($record) => $record->businesses->pluck('company_name')->join(', '))
                            ->placeholder('—'),

                        TextEntry::make('country')
                            ->label('Location'),

                        TextEntry::make('phone_no')
                            ->label('Phone Number')
                            ->state(fn($record) => $record->country_code . ' ' . $record->phone_no),

                        TextEntry::make('profile_verification')
                            ->label('Verified Status')
                            ->badge()
                            ->color(fn(string $state) => match ($state) {
                                'approved' => 'success',
                                'under_review' => 'info',
                                'rejected' => 'warning',
                                'banned' => 'danger',
                                default => 'gray',
                            })
                            ->icon(fn(string $state) => match ($state) {
                                'approved' => 'heroicon-o-check-circle',
                                'under_review' => 'heroicon-o-clock',
                                'rejected' => 'heroicon-o-x-circle',
                                'banned' => 'heroicon-o-no-symbol',
                                default => 'heroicon-o-question-mark-circle',
                            }),

                        TextEntry::make('created_at')
                            ->label('Joining Date')
                            ->date(),
                    ]),
            ]);
    }
}
