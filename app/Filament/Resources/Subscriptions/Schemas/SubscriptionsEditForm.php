<?php

namespace App\Filament\Resources\Subscriptions\Schemas;

use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Section;
use Filament\Support\Icons\Heroicon;

class SubscriptionsEditForm
{
    public static function getSchema(): array
    {
        return [
            Section::make('Package Information')
                ->schema([
                    TextInput::make('name')
                        ->label('Package Name')
                        ->disabled()
                        ->helperText('Package name cannot be changed'),
                ])
                ->columns(1),

            Section::make('Package Details')
                ->schema([
                    Textarea::make('description')
                        ->label('Description')
                        ->required()
                        ->rows(4),

                    TextInput::make('badge')
                        ->label('Badge')
                        ->maxLength(50),
                ])
                ->columns(2),

            Section::make('Pricing')
                ->schema([
                    TextInput::make('monthly_price')->label('Monthly Price')->numeric()->prefix('$')->minValue(0)->step(0.01),
                    TextInput::make('quarterly_price')->label('Quarterly Price')->numeric()->prefix('$')->minValue(0)->step(0.01),
                    TextInput::make('yearly_price')->label('Yearly Price')->numeric()->prefix('$')->minValue(0)->step(0.01),
                ])
                ->columns(3),

            Section::make('Package Features')
                ->schema([
                    Select::make('features')
                        ->label('Features')
                        ->multiple()
                        ->relationship('features', 'name') // only existing features
                        ->preload()
                        ->required()
                        ->minItems(1)
                        ->helperText('Select from existing features only')
                        ->searchable(), // no createOptionForm here!
                ])
                ->columns(1),

            Section::make('Package Settings')
                ->schema([
                    Toggle::make('is_active')
                        ->label('Active')
                        ->onIcon(Heroicon::Bolt)
                        ->helperText('Only active packages will be visible to users'),
                ])
                ->columns(1),
        ];
    }
}
