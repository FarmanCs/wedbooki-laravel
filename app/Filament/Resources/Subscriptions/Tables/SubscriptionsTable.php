<?php

namespace App\Filament\Resources\Subscriptions\Tables;

use Filament\Actions\DeleteAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\Layout\Split;
use Filament\Tables\Columns\Layout\Stack;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Filters\TernaryFilter;
use Filament\Tables\Filters\TrashedFilter;
use Filament\Tables\Table;

class SubscriptionsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')
                    ->label('Package Name')
                    ->searchable()
                    ->sortable()
                    ->weight('bold')
                    ->icon('heroicon-o-cube'),

                TextColumn::make('category.type')
                    ->label('Category')
                    ->icon('heroicon-o-tag')
                    ->badge()
                    ->sortable()
                    ->searchable(),

                Split::make([
                    Stack::make([
                        TextColumn::make('silver_monthly_price')
                            ->label('Silver')
                            ->money('GBP')
                            ->badge()
                            ->color('gray')
                            ->icon('heroicon-o-star')
                            ->formatStateUsing(fn($state) => 'Silver: £' . number_format($state, 2)),
                    ]),

                    Stack::make([
                        TextColumn::make('gold_monthly_price')
                            ->label('Gold')
                            ->money('GBP')
                            ->badge()
                            ->color('warning')
                            ->icon('heroicon-o-sparkles')
                            ->formatStateUsing(fn($state) => 'Gold: £' . number_format($state, 2)),
                    ]),

                    Stack::make([
                        TextColumn::make('platinum_monthly_price')
                            ->label('Platinum')
                            ->money('GBP')
                            ->badge()
                            ->color('success')
                            ->icon('heroicon-o-fire')
                            ->formatStateUsing(fn($state) => 'Platinum: £' . number_format($state, 2)),
                    ]),
                ])->from('md'),

                IconColumn::make('is_active')
                    ->label('Active')
                    ->boolean()
                    ->trueIcon('heroicon-o-check-circle')
                    ->falseIcon('heroicon-o-x-circle')
                    ->trueColor('success')
                    ->falseColor('danger'),

                TextColumn::make('published_at')
                    ->label('Published')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(),
            ])
            ->defaultSort('created_at', 'desc')
            ->filters([
                TrashedFilter::make(),
                SelectFilter::make('category')
                    ->relationship('category', 'type'),
                TernaryFilter::make('is_active')
                    ->label('Active Status'),
            ])
            ->recordActions([
                ViewAction::make(),
                EditAction::make(),
                DeleteAction::make(),
            ]);
    }
}
