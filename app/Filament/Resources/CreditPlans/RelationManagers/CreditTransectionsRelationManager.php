<?php

namespace App\Filament\Resources\CreditPlans\RelationManagers;

use App\Models\Admin\CreditTransaction;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class CreditTransectionsRelationManager extends RelationManager
{
    /**
     * Fake relationship name (required by Filament)
     */
    protected static string $relationship = 'transactions';


    protected static ?string $title = 'Credits Transactions';

    /**
     * Custom query (THIS is the key)
     */
    protected function getTableQuery(): Builder
    {
        return CreditTransaction::query()
            ->with('business')
            ->latest();
    }

    public function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('id')->sortable(),

                TextColumn::make('business.name')
                    ->label('Business')
                    ->searchable(),

                TextColumn::make('tran_type')
                    ->badge()
                    ->colors([
                        'success' => 'credit',
                        'danger' => 'debit',
                    ]),

                TextColumn::make('no_of_credits')
                    ->label('Credits')
                    ->sortable(),

                TextColumn::make('amount')
                    ->money('USD')
                    ->sortable(),

                TextColumn::make('created_at')
                    ->label('Date')
                    ->dateTime(),
            ])
            ->filters([
                SelectFilter::make('tran_type')
                    ->options([
                        'credit' => 'Credit',
                        'debit' => 'Debit',
                    ]),
            ])
            ->defaultSort('created_at', 'desc');
    }
}
