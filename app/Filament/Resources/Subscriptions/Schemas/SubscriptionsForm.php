<?php

namespace App\Filament\Resources\Subscriptions\Schemas;

use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class SubscriptionsForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema->components([
            Section::make('Package Information')
                ->description('Create a subscription package with three pricing tiers')
                ->schema([
                    TextInput::make('name')
                        ->label('Package Name')
                        ->required()
                        ->maxLength(255)
                        ->placeholder('e.g., Venue, Photography, Catering')
                        ->helperText('The name of this package (e.g., Venue Packages, Photography Packages)'),

                    Select::make('category_id')
                        ->label('Category')
                        ->relationship('category', 'type')
                        ->required()
                        ->searchable()
                        ->preload()
                        ->helperText('Select the category for this package'),
                ])
                ->columns(2)
                ->columnSpanFull(),

            self::packageSection('silver', 'Silver Package', 'STARTER', 'Basic tier package'),
            self::packageSection('gold', 'Gold Package', 'POPULAR', 'Premium tier package'),
            self::packageSection('platinum', 'Platinum Package', 'PREMIUM', 'Elite tier package'),
        ]);
    }

    protected static function packageSection(
        string $key,
        string $title,
        string $defaultBadge = null,
        string $description = null
    ): Section {
        return Section::make($title)
            ->description($description)
            ->schema([
                Textarea::make("{$key}_description")
                    ->label('Description')
                    ->required()
                    ->rows(3)
                    ->placeholder('Describe the features and benefits of this tier')
                    ->helperText('What does this tier include?')
                    ->columnSpanFull(),

                TextInput::make("{$key}_badge")
                    ->label('Badge')
                    ->maxLength(255)
                    ->default($defaultBadge)
                    ->placeholder('e.g., Most Popular, Best Value')
                    ->columnSpanFull(),

               Grid::make(3)
                    ->schema([
                        TextInput::make("{$key}_monthly_price")
                            ->label('Monthly Price')
                            ->numeric()
                            ->required()
                            ->prefix('£')
                            ->inputMode('decimal')
                            ->minValue(0)
                            ->step(0.01)
                            ->placeholder('0.00'),

                        TextInput::make("{$key}_quarterly_price")
                            ->label('Quarterly Price')
                            ->numeric()
                            ->prefix('£')
                            ->inputMode('decimal')
                            ->minValue(0)
                            ->step(0.01)
                            ->placeholder('0.00'),

                        TextInput::make("{$key}_yearly_price")
                            ->label('Yearly Price')
                            ->numeric()
                            ->prefix('£')
                            ->inputMode('decimal')
                            ->minValue(0)
                            ->step(0.01)
                            ->placeholder('0.00'),
                    ]),
            ])
            ->columns(1)
            ->collapsible();
    }
}
