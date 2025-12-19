<?php

namespace App\Filament\Resources\Vendors\Tables;

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

class VendorsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                ImageColumn::make('profile_image')
                    ->label('Image')->circular()->imageSize(40),
                TextColumn::make('full_name')->label('Name')->searchable()->sortable(),
                TextColumn::make('category.type')->label('Category'),
                TextColumn::make('country')->label('Location'),
                TextColumn::make('created_at')->label('Joining Date')->date(),
                TextColumn::make('phone_no')
                    ->label('Phone Number')
                    ->formatStateUsing(function ($record) {
                        return $record->country_code . ' ' . $record->phone_no;
                    }),
                TextColumn::make('profile_verification')
                    ->badge()
                    ->label('Verified')
                    ->color(function ($state): string {
                        return match ($state) {
                            'approved' => 'success',
                            'under_review' => 'info',
                            'rejected' => 'warning',
                            'banned' => 'danger',
                        };
                    })
                    ->icon(function ($state): string {
                        return match ($state) {
                            'approved' => 'heroicon-o-check',
                            'under_review' => 'heroicon-o-clock',
                            'rejected' => 'heroicon-o-x-circle',
                            'banned' => 'heroicon-o-x-circle',
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
