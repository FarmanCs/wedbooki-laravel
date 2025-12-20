<?php

namespace App\Filament\Resources\Hosts\Tables;

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

class HostsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                ImageColumn::make('profile_image')->circular()->imageSize(40),
                TextColumn::make('full_name')->label('Name')
                ->searchable()
                ->sortable(),
                TextColumn::make('email')->label('Email')
                ->searchable()
                ->sortable(),
                TextColumn::make('event_type')->label('Event Type'),
                TextColumn::make('wedding_date')->label('Event Date')
                ->date(),
                TextColumn::make('event_budget')->label('Event Budget'),
                TextColumn::make('estimated_guests')->label('Estimated Guests'),
                TextColumn::make('status')
                    ->badge()
                    ->label('Status')
                    ->color(function ($state): string {
                        return match (strtolower($state)) {
                            'pending' => 'info',
                            'approved' => 'success',
                            'blocked' => 'error',
                            default => 'warning',
                        };
                    }),
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
