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
                Section::make('Profile_image')
                    ->columns(2)
                    ->schema([
                        ImageEntry::make('profile_image')
                            ->label('Profile Image')
                            ->circular()
                            ->disk('s3')
                            ->size(200),
                        TextEntry::make('full_name')
                            ->label('Name')
                            ->size(100),
                    ]),
                Section::make('Business_Information')
                    ->schema([
                        TextEntry::make('category.type')
                            ->label('Category')
                            ->placeholder('—'),
                        TextEntry::make('country')
                            ->label('Location'),
                        TextEntry::make('created_at')
                            ->label('Joining Date')
                            ->date(),
                        TextEntry::make('phone_no')
                            ->label('Phone Number')
                            ->state(function ($record) {
                                return $record->country_code . ' ' . $record->phone_no;
                            }),
                        TextEntry::make('profile_verification')
                            ->label('Verified')
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
                            })
                    ])


            ]);
    }
}
