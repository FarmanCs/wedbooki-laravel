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
                // 1 Business dropdown
                Select::make('business_id')
                    ->label('Business')
                    ->relationship('business', 'company_name')
                    ->searchable()
                    ->preload()
                    ->required(),

                // 2 Package name
                TextInput::make('name')
                    ->label('Package Name')
                    ->required()
                    ->maxLength(255),

                // 3️⃣ Price
                TextInput::make('price')
                    ->label('Price')
                    ->numeric()
                    ->required(),

                // 4️⃣ Discount
                TextInput::make('discount')
                    ->label('Discount')
                    ->numeric()
                    ->default(0),

                // 5️⃣ Discount percentage
                TextInput::make('discount_percentage')
                    ->label('Discount Percentage')
                    ->numeric()
                    ->default(0),

                // 6️⃣ Description
                Textarea::make('description')
                    ->label('Description')
                    ->rows(3),

                // 7️⃣ Features (array)
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

                // 8️⃣ Is popular toggle
                Toggle::make('is_popular')
                    ->label('Is Popular')
                    ->default(false),
            ]);
    }
}
