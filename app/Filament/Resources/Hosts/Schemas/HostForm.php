<?php

namespace App\Filament\Resources\Hosts\Schemas;

use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Illuminate\Support\Facades\Storage;

class HostForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([

                Section::make('Profile')
                    ->icon('heroicon-o-user-circle')
                    ->iconColor('primary')
                    ->schema([
                        FileUpload::make('profile_image')
                            ->label('Profile Image')
                            ->image()
                            ->avatar()
                            ->directory('hosts/profile-images')
                            ->visibility('public')
                            ->imageEditor()
                            ->maxSize(2048),
                    ]),

                Section::make('Basic Information')
                    ->icon('heroicon-o-identification')
                    ->iconColor('primary')
                    ->columns(2)
                    ->schema([

                        TextInput::make('full_name')
                            ->label('Full Name')
                            ->required()
                            ->maxLength(255),

                        TextInput::make('email')
                            ->label('Email')
                            ->email()
                            ->required()
                            ->unique(ignoreRecord: true),
                    ]),

                Section::make('Event Information')
                    ->icon('heroicon-o-calendar')
                    ->iconColor('primary')
                    ->columns(2)
                    ->schema([

                        TextInput::make('event_type')
                            ->label('Event Type')
                            ->placeholder('Wedding, Engagement, Birthday')
                            ->required(),

                        DatePicker::make('wedding_date')
                            ->label('Event Date')
                            ->required(),

                        TextInput::make('event_budget')
                            ->label('Event Budget')
                            ->numeric()
                            ->prefix('PKR'),

                        TextInput::make('estimated_guests')
                            ->label('Estimated Guests')
                            ->numeric(),
                    ]),

                Section::make('Account Status')
                    ->icon('heroicon-o-shield-check')
                    ->iconColor('primary')
                    ->schema([
                        Select::make('status')
                            ->label('Status')
                            ->options([
                                'pending' => 'Pending',
                                'approved' => 'Approved',
                                'blocked' => 'Blocked',
                            ])
                            ->required()
                            ->native(false),
                    ]),
            ]);
    }
}
