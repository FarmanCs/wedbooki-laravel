<?php

namespace App\Filament\Resources\Subscriptions\Widgets;

use App\Models\Admin\AdminPackage;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget;
use Filament\Tables\Columns\Layout\Stack;
use Filament\Tables\Columns\Layout\Split;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\IconColumn;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;

class SubscriptionPackagesWidget extends TableWidget
{
    protected static ?string $heading = 'Vendor_Packages';

    protected int|string|array $columnSpan = 'full';

    public function table(Table $table): Table
    {
        return $table
            ->query(AdminPackage::query())
            ->contentGrid([
                'md' => 3,
                'xl' => 3,
            ])
            ->columns([
                Stack::make([

                    // HEADER
                    TextColumn::make('name')
                        ->size('lg')
                        ->weight('bold')
                        ->alignCenter(),

                    TextColumn::make('category.type')
                        ->badge()
                        ->alignCenter(),

                    // DESCRIPTION
                    TextColumn::make('description')
                        ->color('gray')
                        ->wrap(),

                    // PRICES
                    Split::make([
                        TextColumn::make('silver_monthly_price')
                            ->badge()
                            ->color('gray')
                            ->formatStateUsing(fn ($state) => 'Silver £' . number_format($state)),

                        TextColumn::make('gold_monthly_price')
                            ->badge()
                            ->color('warning')
                            ->formatStateUsing(fn ($state) => 'Gold £' . number_format($state)),

                        TextColumn::make('platinum_monthly_price')
                            ->badge()
                            ->color('success')
                            ->formatStateUsing(fn ($state) => 'Platinum £' . number_format($state)),
                    ]),

                    // STATUS
                    IconColumn::make('is_active')
                        ->boolean()
                        ->alignCenter(),

                ])->extraAttributes([
                    'class' => 'rounded-xl shadow-md border p-6 bg-gray-900',
                ]),
            ])
            ->recordActions([
                ViewAction::make(),
                EditAction::make(),
            ]);
    }
}
