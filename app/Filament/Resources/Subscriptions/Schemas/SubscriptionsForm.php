<?php

namespace App\Filament\Resources\Subscriptions\Schemas;

use App\Models\Admin\Feature;
use App\Models\Vendor\Category;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class SubscriptionsForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema->components([
            Section::make('Package Information')
                ->description('Select a category to automatically create Silver, Gold, and Platinum packages')
                ->schema([
                    Select::make('category_id')
                        ->label('Category')
                        ->relationship('category', 'type') // Changed from 'name' to 'type'
                        ->required()
                        ->searchable()
                        ->preload()
                        ->live()
                        ->afterStateUpdated(function ($state, callable $set) {
                            // Reset all tier fields when category changes
                            $tiers = ['silver', 'gold', 'platinum'];
                            foreach ($tiers as $tier) {
                                $set("{$tier}_description", null);
                                $set("{$tier}_badge", null);
                                $set("{$tier}_monthly_price", null);
                                $set("{$tier}_quarterly_price", null);
                                $set("{$tier}_yearly_price", null);
                            }
                        })
                        ->helperText('All three packages (Silver, Gold, Platinum) will be created for this category'),
                ])
                ->columns(1),

            Section::make('Silver Tier')
                ->description('Configure the Silver package details')
                ->schema([
                    Textarea::make('silver_description')
                        ->label('Description')
                        ->required()
                        ->rows(3)
                        ->maxLength(500),

                    TextInput::make('silver_badge')
                        ->label('Badge')
                        ->maxLength(50)
                        ->placeholder('e.g., Most Popular, Best Value'),

                    TextInput::make('silver_monthly_price')
                        ->label('Monthly Price')
                        ->required()
                        ->numeric()
                        ->prefix('$')
                        ->minValue(0)
                        ->step(0.01),

                    TextInput::make('silver_quarterly_price')
                        ->label('Quarterly Price')
                        ->numeric()
                        ->prefix('$')
                        ->minValue(0)
                        ->step(0.01),

                    TextInput::make('silver_yearly_price')
                        ->label('Yearly Price')
                        ->numeric()
                        ->prefix('$')
                        ->minValue(0)
                        ->step(0.01),
                ])
                ->columns(2)
                ->collapsible(),

            Section::make('Gold Tier')
                ->description('Configure the Gold package details')
                ->schema([
                    Textarea::make('gold_description')
                        ->label('Description')
                        ->required()
                        ->rows(3)
                        ->maxLength(500),

                    TextInput::make('gold_badge')
                        ->label('Badge')
                        ->maxLength(50)
                        ->placeholder('e.g., Most Popular, Best Value'),

                    TextInput::make('gold_monthly_price')
                        ->label('Monthly Price')
                        ->required()
                        ->numeric()
                        ->prefix('$')
                        ->minValue(0)
                        ->step(0.01),

                    TextInput::make('gold_quarterly_price')
                        ->label('Quarterly Price')
                        ->numeric()
                        ->prefix('$')
                        ->minValue(0)
                        ->step(0.01),

                    TextInput::make('gold_yearly_price')
                        ->label('Yearly Price')
                        ->numeric()
                        ->prefix('$')
                        ->minValue(0)
                        ->step(0.01),
                ])
                ->columns(2)
                ->collapsible(),

            Section::make('Platinum Tier')
                ->description('Configure the Platinum package details')
                ->schema([
                    Textarea::make('platinum_description')
                        ->label('Description')
                        ->required()
                        ->rows(3)
                        ->maxLength(500),

                    TextInput::make('platinum_badge')
                        ->label('Badge')
                        ->maxLength(50)
                        ->placeholder('e.g., Most Popular, Best Value'),

                    TextInput::make('platinum_monthly_price')
                        ->label('Monthly Price')
                        ->required()
                        ->numeric()
                        ->prefix('$')
                        ->minValue(0)
                        ->step(0.01),

                    TextInput::make('platinum_quarterly_price')
                        ->label('Quarterly Price')
                        ->numeric()
                        ->prefix('$')
                        ->minValue(0)
                        ->step(0.01),

                    TextInput::make('platinum_yearly_price')
                        ->label('Yearly Price')
                        ->numeric()
                        ->prefix('$')
                        ->minValue(0)
                        ->step(0.01),
                ])
                ->columns(2)
                ->collapsible(),

            Section::make('Package Settings')
                ->description('Configure additional package settings')
                ->schema([
                    Toggle::make('is_active')
                        ->label('Active')
                        ->default(true)
                        ->helperText('Only active packages will be visible to users'),

                    DateTimePicker::make('published_at')
                        ->label('Publish Date')
                        ->default(now())
                        ->helperText('Set when this package should be published'),
                ])
                ->columns(2)
                ->collapsible(),
        ]);
    }
}
