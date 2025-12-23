<?php

namespace App\Filament\Resources\CreditPlans\Schemas;

use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Schemas\Schema;

class CreditPlanForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                FileUpload::make('image')
                    ->label('Plan Image')
                    ->image()
                    ->disk('s3')
                    ->directory('credit-plans')
                    ->visibility('public')
                    ->imageEditor()
                    ->maxSize(2048)
                    ->columnSpanFull(),

                TextInput::make('name')
                    ->label('Plan Name')
                    ->required()
                    ->maxLength(255),

                Textarea::make('description')
                    ->label('Description')
                    ->rows(4)
                    ->maxLength(1000)
                    ->columnSpanFull(),

                TextInput::make('price')
                    ->label('Price')
                    ->numeric()
                    ->minValue(0)
                    ->required(),

                TextInput::make('discounted_percentage')
                    ->label('Discount (%)')
                    ->numeric()
                    ->minValue(0)
                    ->maxValue(100)
                    ->default(0)
                    ->suffix('%'),

                TextInput::make('no_of_credits')
                    ->label('Number of Credits')
                    ->numeric()
                    ->minValue(0)
                    ->required(),
            ])
            ->columns(2);
    }
}
