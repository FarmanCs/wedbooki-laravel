<?php

namespace App\Filament\Resources\Packages\Schemas;

use Filament\Schemas\Schema;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Repeater;

class PackagesForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([

                Select::make('business_id')
                    ->label('Business')
                    ->relationship('business', 'company_name')
                    ->searchable()
                    ->preload()
                    ->required(),

                TextInput::make('name')
                    ->label('Package Name')
                    ->required()
                    ->maxLength(255),

                TextInput::make('price')
                    ->label('Price')
                    ->numeric()
                    ->required(),

                TextInput::make('discount')
                    ->label('Discount')
                    ->numeric()
                    ->default(0),

                TextInput::make('discount_percentage')
                    ->label('Discount Percentage')
                    ->numeric()
                    ->default(0),


                Textarea::make('description')
                    ->label('Description')
                    ->rows(3),


                Repeater::make('features')
                    ->label('Features')
                    ->schema([
                        TextInput::make('feature')
                            ->label('Feature')
                            ->required(),
                    ])
                    ->default([])
                    ->columns(1)
                    ->collapsible(),


                Toggle::make('is_popular')
                    ->label('Is Popular')
                    ->default(false),
            ]);
    }
}
