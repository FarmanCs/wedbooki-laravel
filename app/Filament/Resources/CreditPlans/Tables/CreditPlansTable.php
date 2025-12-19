<?php

namespace App\Filament\Resources\CreditPlans\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ForceDeleteBulkAction;
use Filament\Actions\RestoreBulkAction;
use Filament\Actions\ViewAction;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\TrashedFilter;
use Filament\Tables\Table;

class CreditPlansTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                ImageColumn::make('image')
                    ->label('Image')
                    ->disk('s3')
                    ->visibility('public')
                    ->height(50)
                    ->circular(),

                TextColumn::make('name')
                    ->label('Plan Name')
                    ->searchable()
                    ->sortable()
                    ->weight('bold'),
                TextColumn::make('price')
                    ->label('Price')
                    ->icon('heroicon-o-currency-dollar')
                    ->iconColor('primary')
                    ->sortable(),
                TextColumn::make('discounted_percentage')
                    ->label('Discount')
                    ->suffix('%')
                    ->sortable(),
                TextColumn::make('no_of_credits')
                    ->label('Credits'),
                TextColumn::make('created_at')
                    ->label('Created')
                    ->dateTime('d M Y')
            ])
            ->filters([
                TrashedFilter::make(),
            ])
            ->recordActions([
                ViewAction::make(),
                EditAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                    ForceDeleteBulkAction::make(),
                    RestoreBulkAction::make(),
                ]),
            ]);
    }
}
