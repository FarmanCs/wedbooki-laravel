<?php

namespace App\Filament\Resources\Vendors\Schemas;

use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;

class VendorForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([

                // Personal Info
                TextInput::make('full_name')->label('Full Name')->required()->maxLength(255),
                TextInput::make('email')->label('Email')->email()->required()->unique('vendors', 'email'),
                TextInput::make('phone_no')->label('Phone Number')->required()->tel(),
                TextInput::make('country_code')->label('Country Code')->required()->maxLength(5),
                TextInput::make('country')->label('Country')->required(),
                TextInput::make('city')->label('City')->required(),
                TextInput::make('password')->label('Password')->password()->required()->minLength(6),

                // Vendor Category
                Select::make('category_id')
                    ->label('Category')
                    ->relationship('category', 'type')
                    ->searchable()
                    ->preload()
                    ->required(),


                // Business Info
                TextInput::make('company_name')->label('Company Name')->required(),
                TextInput::make('business_registration')->label('Business Registration')->nullable(),
                TextInput::make('business_license_number')->label('Business License Number')->nullable(),

                // Profile Image
                FileUpload::make('profile_image')
                    ->disk('s3')
                    ->directory('Vendors_images')
                    ->visibility('public')
                    ->image()
                    ->maxSize(1024),
            ]);
    }
}
