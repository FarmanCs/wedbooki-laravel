<?php

namespace App\Filament\Resources\Subscriptions\Schemas;

use App\Models\Admin\AdminPackage;
use Filament\Infolists\Components\IconEntry;
use Filament\Infolists\Components\TextEntry;
use Filament\Infolists\Components\RepeatableEntry;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;


class SubscriptionsInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema->schema([
            Section::make('Package Information')
                ->schema([
                    TextEntry::make('name')
                        ->label('Package Name')
                        ->icon('heroicon-o-cube')
                        ->size('md')
                        ->weight('bold')
                        ->badge()
                        ->color(fn(string $state): string => match ($state) {
                            'Silver' => 'gray',
                            'Gold' => 'warning',
                            'Platinum' => 'info',
                            default => 'gray',
                        }),

                    TextEntry::make('category.type')
                        ->label('Category')
                        ->badge()
                        ->color('primary')
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
                        ->dateTime('M d, Y h:i A')
                        ->icon('heroicon-o-calendar'),
                ])
                ->columns(4),

            Section::make('Package Details')
                ->icon('heroicon-o-document-text')
                ->schema([
                    TextEntry::make('badge')
                        ->label('Badge')
                        ->badge()
                        ->color('success')
                        ->icon('heroicon-o-sparkles')
                        ->placeholder('No badge set')
                        ->visible(fn($record) => !empty($record->badge)),

                    TextEntry::make('description')
                        ->label('Description')
                        ->columnSpanFull()
                        ->prose(),
                ])
                ->columns(1),

            Section::make('Pricing')
                ->icon('heroicon-o-currency-dollar')
                ->schema([
                    Grid::make(3)
                        ->schema([
                            TextEntry::make('monthly_price')
                                ->label('Monthly Price')
                                ->money('usd')
                                ->size('md')
                                ->weight('bold')
                                ->icon('heroicon-o-calendar')
                                ->color('success'),

                            TextEntry::make('quarterly_price')
                                ->label('Quarterly Price')
                                ->money('usd')
                                ->size('md')
                                ->weight('bold')
                                ->icon('heroicon-o-calendar-days')
                                ->color('warning')
                                ->placeholder('Not set')
                                ->visible(fn($record) => !empty($record->quarterly_price)),

                            TextEntry::make('yearly_price')
                                ->label('Yearly Price')
                                ->money('usd')
                                ->size('md')
                                ->weight('bold')
                                ->icon('heroicon-o-calendar')
                                ->color('danger')
                                ->placeholder('Not set')
                                ->visible(fn($record) => !empty($record->yearly_price)),
                        ]),
                ]),

            Section::make('Features')
                ->icon('heroicon-o-check-badge')
                ->description('Features included in this package')
                ->schema([
                    TextEntry::make('features_count')
                        ->label('Total Features')
                        ->badge()
                        ->color('primary')
                        ->formatStateUsing(fn($record) => $record->features()->count() . ' features'),

                    RepeatableEntry::make('features')
                        ->label('')
                        ->schema([
                            TextEntry::make('name')
                                ->label('Feature Name')
                                ->icon('heroicon-o-check-circle')
                                ->color('success')
                                ->weight('bold'),

                            TextEntry::make('key')
                                ->label('Key')
                                ->badge()
                                ->color('gray')
                                ->copyable()
                                ->copyMessage('Feature key copied!')
                                ->copyMessageDuration(1500),

                            TextEntry::make('description')
                                ->label('Description')
                                ->placeholder('No description')
                                ->color('gray'),

                            IconEntry::make('is_active')
                                ->label('Status')
                                ->boolean()
                                ->trueIcon('heroicon-o-check-circle')
                                ->falseIcon('heroicon-o-x-circle')
                                ->trueColor('success')
                                ->falseColor('danger'),
                        ])
                        ->columns(4)
                        ->columnSpanFull()
                        ->visible(fn($record) => $record->features()->count() > 0),

                    TextEntry::make('no_features')
                        ->label('')
                        ->default('No features assigned to this package')
                        ->color('warning')
                        ->icon('heroicon-o-exclamation-triangle')
                        ->visible(fn($record) => $record->features()->count() === 0),
                ])
                ->collapsible()
                ->collapsed(false),
        ]);
    }
}
