<?php

namespace App\Filament\Resources\Vendors\Schemas;

use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;

class VendorForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('full_name')->label('Name'),
                Select::make('category_id')
                    ->label('Category')
                    ->relationship('category', 'type')
                    ->searchable()
                    ->preload()
                    ->required(),

            ]);
    }
}
