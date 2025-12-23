<?php

namespace App\Filament\Resources\Bookings\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ForceDeleteBulkAction;
use Filament\Actions\RestoreBulkAction;
use Filament\Actions\ViewAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\TrashedFilter;
use Filament\Tables\Table;

class BookingsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('id')->label('ID'),

                TextColumn::make('amount')->label('Amount'),

                TextColumn::make('host.full_name')
                    ->label('Host Name')
                    ->searchable()
                ,

                TextColumn::make('host.email')
                    ->label('Host Email')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('payment_status')
                    ->badge()
                    ->label('Payment Status')
                    ->icon('heroicon-s-currency-dollar')
                    ->color(function ($state): string {
                        return match ($state) {
                            'unpaid' => 'danger',
                            'advancePaid' => 'success',
                            'refunded' => 'warning',
                            'fullyPaid' => 'success',
                        };

                    }),

                TextColumn::make('created_at')
                    ->label('Date')
                    ->date(),

                TextColumn::make('time_slot')
                    ->label('Time Slot'),
            ])
            ->filters([
                TrashedFilter::make(),
            ])
            ->recordActions([
                ViewAction::make(),
//                EditAction::make(),
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
