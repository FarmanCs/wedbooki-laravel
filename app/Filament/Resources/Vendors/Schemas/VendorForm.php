<?php

namespace App\Filament\Resources\Vendors\Schemas;

use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class VendorForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Profile Image')
                    ->columns(2)
                    ->schema([
                        FileUpload::make('profile_image')
                            ->label('Profile Image')
                            ->disk('s3')
                            ->directory('Vendors_images')
                            ->visibility('public')
                            ->image()
                            ->imageEditor()
                            ->circleCropper()
                            ->maxSize(2000)
                            ->preserveFilenames(),
                        TextInput::make('full_name')
                            ->label('Full Name')
                            ->required()
                            ->maxLength(255),

                        TextInput::make('email')
                            ->label('Email')
                            ->email()
                            ->required()
                            ->unique(ignoreRecord: true),

                        TextInput::make('phone_no')
                            ->label('Phone Number')
                            ->tel()
                            ->required(),

                        TextInput::make('country_code')
                            ->label('Country Code')
                            ->required()
                            ->maxLength(5),

                        TextInput::make('country')
                            ->label('Country')
                            ->required(),

                        TextInput::make('city')
                            ->label('City')
                            ->required(),
                    ]),

                Section::make('Business Information')
                    ->schema([
                        Select::make('category_id')
                            ->label('Category')
                            ->relationship('category', 'type')
                            ->searchable()
                            ->preload()
                            ->required(),

                        Select::make('Business_id')
                            ->label('Business')
                            ->multiple()
                            ->relationship('businesses', 'company_name')

                    ]),

            ]);
    }


}
