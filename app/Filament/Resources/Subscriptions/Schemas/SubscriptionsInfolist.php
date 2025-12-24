<?php

namespace App\Filament\Resources\Subscriptions\Schemas;

use Filament\Infolists\Components\IconEntry;
use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class SubscriptionsInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema->components([
            Section::make('Package Information')
                ->schema([
                    TextEntry::make('name')
                        ->label('Package Name')
                        ->icon('heroicon-o-cube')
                        ->size('lg')
                        ->weight('bold'),

                    TextEntry::make('category.type')
                        ->label('Category')
                        ->badge()
                        ->icon('heroicon-o-tag'),

                    IconEntry::make('is_active')
                        ->label('Status')
                        ->boolean()
                        ->trueIcon('heroicon-o-check-circle')
                        ->falseIcon('heroicon-o-x-circle')
                        ->trueColor('success')
                        ->falseColor('danger'),

                    TextEntry::make('published_at')
                        ->label('Published At')
                        ->dateTime(),
                ])
                ->columns(4),

            // Silver Tier
            Section::make('Silver Tier')
                ->icon('heroicon-o-star')
                ->schema([
                    TextEntry::make('silver_badge')
                        ->label('Badge')
                        ->badge()
                        ->color('gray')
                        ->placeholder('No badge'),

                    TextEntry::make('silver_description')
                        ->label('Description')
                        ->columnSpanFull(),

                    Grid::make(3)
                        ->schema([
                            TextEntry::make('silver_monthly_price')
                                ->label('Monthly')
                                ->money('GBP')
                                ->icon('heroicon-o-calendar'),

                            TextEntry::make('silver_quarterly_price')
                                ->label('Quarterly')
                                ->money('GBP')
                                ->icon('heroicon-o-calendar-days')
                                ->placeholder('Not set'),

                            TextEntry::make('silver_yearly_price')
                                ->label('Yearly')
                                ->money('GBP')
                                ->icon('heroicon-o-calendar')
                                ->placeholder('Not set'),
                        ]),
                ])
                ->collapsible(),

            // Gold Tier
            Section::make('Gold Tier')
                ->icon('heroicon-o-sparkles')
                ->schema([
                    TextEntry::make('gold_badge')
                        ->label('Badge')
                        ->badge()
                        ->color('warning')
                        ->placeholder('No badge'),

                    TextEntry::make('gold_description')
                        ->label('Description')
                        ->columnSpanFull(),

                    Grid::make(3)
                        ->schema([
                            TextEntry::make('gold_monthly_price')
                                ->label('Monthly')
                                ->money('GBP')
                                ->icon('heroicon-o-calendar'),

                            TextEntry::make('gold_quarterly_price')
                                ->label('Quarterly')
                                ->money('GBP')
                                ->icon('heroicon-o-calendar-days')
                                ->placeholder('Not set'),

                            TextEntry::make('gold_yearly_price')
                                ->label('Yearly')
                                ->money('GBP')
                                ->icon('heroicon-o-calendar')
                                ->placeholder('Not set'),
                        ]),
                ])
                ->collapsible(),

            // Platinum Tier
            Section::make('Platinum Tier')
                ->icon('heroicon-o-fire')
                ->schema([
                    TextEntry::make('platinum_badge')
                        ->label('Badge')
                        ->badge()
                        ->color('success')
                        ->placeholder('No badge'),

                    TextEntry::make('platinum_description')
                        ->label('Description')
                        ->columnSpanFull(),

                    Grid::make(3)
                        ->schema([
                            TextEntry::make('platinum_monthly_price')
                                ->label('Monthly')
                                ->money('GBP')
                                ->icon('heroicon-o-calendar'),

                            TextEntry::make('platinum_quarterly_price')
                                ->label('Quarterly')
                                ->money('GBP')
                                ->icon('heroicon-o-calendar-days')
                                ->placeholder('Not set'),

                            TextEntry::make('platinum_yearly_price')
                                ->label('Yearly')
                                ->money('GBP')
                                ->icon('heroicon-o-calendar')
                                ->placeholder('Not set'),
                        ]),
                ])
                ->collapsible(),
        ]);
    }
}
