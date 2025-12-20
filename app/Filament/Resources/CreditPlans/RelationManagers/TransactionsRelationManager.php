<?php

namespace App\Filament\Resources\CreditPlans\RelationManagers {

    use App\Models\Admin\Transaction;
    use Filament\Resources\RelationManagers\RelationManager;
    use Filament\Tables\Columns\TextColumn;
    use Filament\Tables\Columns\BadgeColumn;
    use Filament\Tables\Table;

    class TransactionsRelationManager extends RelationManager
    {
        protected static string $relationship = 'transactions';
        protected static ?string $recordTitleAttribute = 'id';
        public function table(Table $table): Table
        {
            return $table
                ->columns([
                    TextColumn::make('id')->label('ID')->sortable(),
                    TextColumn::make('booking.id')->label('Booking ID')->sortable(),
                    TextColumn::make('vendor.full_name')->label('Vendor'),
                    TextColumn::make('host.full_name')->label('Host'),
                    TextColumn::make('sender_name')->label('Sender'),
                    TextColumn::make('receiver_name')->label('Receiver'),
                    TextColumn::make('amount')->label('Amount')->prefix('$')->sortable(),
                    TextColumn::make('status')
                        ->label('Status')
                        ->badge()
                        ->colors([
                            'success' => 'paid',
                            'warning' => 'pending',
                            'danger' => 'failed',
                        ]),
                    TextColumn::make('payment_method')->label('Payment Method'),
                    TextColumn::make('paid_at')->label('Paid At')->dateTime(),
                ])
                ->filters([])
                ->headerActions([])
                ->recordActions([]);
        }
    }
}
