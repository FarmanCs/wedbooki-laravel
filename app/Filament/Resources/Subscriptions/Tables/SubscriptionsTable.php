<?php

namespace App\Filament\Resources\Subscriptions\Tables;


use Filament\Actions\DeleteAction;
use Filament\Actions\EditAction;
use Filament\Actions\RestoreAction;
use Filament\Actions\ViewAction;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\ToggleColumn;
use Filament\Tables\Filters\SelectFilter;
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
                    ->badge()
                    ->color(fn(string $state): string => match ($state) {
                        'Silver' => 'gray',
                        'Gold' => 'warning',
                        'Platinum' => 'info',
                        default => 'gray',
                    }),

                TextColumn::make('category.type')
                    ->label('Category')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('badge')
                    ->label('Badge')
                    ->badge()
                    ->color('success')
                    ->default('—'),

                TextColumn::make('features_count')
                    ->label('Features')
                    ->counts('features')
                    ->badge()
                    ->color('primary')
                    ->sortable(),

                TextColumn::make('features.name')
                    ->label('Feature List')
                    ->badge()
                    ->separator(',')
                    ->limit(3)
                    ->wrap()
                    ->toggleable(isToggledHiddenByDefault: true),

                TextColumn::make('monthly_price')
                    ->label('Monthly Price')
                    ->money('usd')
                    ->sortable(),

                TextColumn::make('quarterly_price')
                    ->label('Quarterly Price')
                    ->money('usd')
                    ->sortable()
                    ->toggleable(),

                TextColumn::make('yearly_price')
                    ->label('Yearly Price')
                    ->money('usd')
                    ->sortable()
                    ->toggleable(),

                ToggleColumn::make('is_active')
                    ->label('Status')
                    ->onIcon(Heroicon::BookOpen),

                TextColumn::make('published_at')
                    ->label('Published')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),

                TextColumn::make('created_at')
                    ->label('Created At')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                SelectFilter::make('category')
                    ->relationship('category', 'type')
                    ->searchable()
                    ->preload()
                    ->label('Category'),

                SelectFilter::make('name')
                    ->label('Tier')
                    ->options([
                        'Silver' => 'Silver',
                        'Gold' => 'Gold',
                        'Platinum' => 'Platinum',
                    ]),

                SelectFilter::make('is_active')
                    ->label('Status')
                    ->options([
                        '1' => 'Active',
                        '0' => 'Inactive',
                    ]),

                TrashedFilter::make(),
            ])
            ->recordActions([
                ViewAction::make(),
                EditAction::make(),
                DeleteAction::make(),
                RestoreAction::make(),
            ])
            ->toolbarActions([
//                DeleteAction::make(),
            ])
            ->defaultSort('created_at', 'desc');
    }
}
