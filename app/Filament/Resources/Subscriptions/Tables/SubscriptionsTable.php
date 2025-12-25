<?php

namespace App\Filament\Resources\Subscriptions\Tables;

use Filament\Actions\DeleteAction;
use Filament\Actions\EditAction;
use Filament\Actions\RestoreAction;
use Filament\Actions\ViewAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\BadgeColumn;
use Filament\Tables\Columns\IconColumn;
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
                    ->color(fn (string $state): string => match ($state) {
                        'Silver' => 'gray',
                        'Gold' => 'warning',
                        'Platinum' => 'info',
                        default => 'gray',
                    }),

                TextColumn::make('category.type') // Changed from 'category.name' to 'category.type'
                ->label('Category')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('badge')
                    ->label('Badge')
                    ->badge()
                    ->color('success')
                    ->default('—'),

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

                TextColumn::make('features_count')
                    ->counts('features')
                    ->label('Features')
                    ->badge()
                    ->color('primary'),

                IconColumn::make('is_active')
                    ->label('Status')
                    ->boolean()
                    ->sortable(),

                TextColumn::make('published_at')
                    ->label('Published')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(),

                TextColumn::make('created_at')
                    ->label('Created')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                SelectFilter::make('category')
                    ->relationship('category', 'type') // Changed from 'name' to 'type'
                    ->searchable()
                    ->preload(),

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

            ])
            ->defaultSort('created_at', 'desc');
    }
}
