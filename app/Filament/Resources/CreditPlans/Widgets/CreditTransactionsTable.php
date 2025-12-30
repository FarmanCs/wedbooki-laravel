<?php

namespace App\Filament\Resources\CreditPlans\Widgets;

use App\Models\Admin\CreditTransaction;
use Filament\Actions\ViewAction;
use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Components\Section;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget;
use Illuminate\Database\Eloquent\Builder;

class CreditTransactionsTable extends TableWidget
{
    protected int|string|array $columnSpan = 'full';

    protected function getTableQuery(): Builder
    {
        return CreditTransaction::query()
            ->with([
                'business:id,company_name,business_email,business_phone,business_type,profile_verification,rating,street_address,city,country,postal_code,payment_days_advance,payment_days_final,advance_percentage,view_count,social_count,last_login',
                'business.vendor:id,full_name,email,phone_no,country,city,years_of_experience',
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->query($this->getTableQuery())
            ->columns([
                TextColumn::make('id')
                    ->label('ID')
                    ->sortable()
                    ->toggleable(),

                TextColumn::make('business.company_name')
                    ->label('Business')
                    ->searchable()
                    ->limit(30)
                    ->icon('heroicon-m-building-office')
                    ->iconColor('primary'),

                TextColumn::make('tran_type')
                    ->label('Type')
                    ->badge()
                    ->icon(fn (string $state) => $state === 'credit'
                        ? 'heroicon-m-arrow-down-circle'
                        : 'heroicon-m-arrow-up-circle'
                    )
                    ->color(fn (string $state) => match ($state) {
                        'credit' => 'success',
                        'debit'  => 'danger',
                        default  => 'gray',
                    }),

                TextColumn::make('amount')
                    ->label('Amount')
                    ->money('USD')
                    ->sortable()
                    ->icon('heroicon-m-banknotes'),

                TextColumn::make('no_of_credits')
                    ->label('Credits')
                    ->numeric()
                    ->icon('heroicon-m-ticket'),

                TextColumn::make('created_at')
                    ->label('Created')
                    ->dateTime('d M Y, h:i A')
                    ->sortable(),
            ])
            ->filters([
                SelectFilter::make('tran_type')
                    ->label('Transaction Type')
                    ->options([
                        'credit' => 'Credit',
                        'debit'  => 'Debit',
                    ])
                    ->native(false),
            ])
            ->recordActions([
                ViewAction::make()
                    ->icon('heroicon-m-eye')
                    ->modalHeading('Credit Transaction Details')
                    ->modalWidth('3xl')
                    ->infolist($this->getViewInfolist()),
            ])
            ->defaultSort('created_at', 'desc');
    }

    protected function getViewInfolist(): array
    {
        return [
            /* ================= Transaction ================= */
            Section::make('Transaction')
                ->icon('heroicon-m-credit-card')
                ->iconColor('primary')
                ->columns(4)
                ->schema([
                    TextEntry::make('id')->label('Transaction ID'),
                    TextEntry::make('tran_type')
                        ->label('Type')
                        ->badge()
                        ->color(fn ($state) => $state === 'credit' ? 'success' : 'danger'),
                    TextEntry::make('amount')->money('USD'),
                    TextEntry::make('no_of_credits')->label('Credits'),

                    TextEntry::make('from')->placeholder('N/A'),
                    TextEntry::make('to')->placeholder('N/A'),
                    TextEntry::make('created_at')->dateTime(),
                    TextEntry::make('updated_at')->dateTime(),
                ]),

            /* ================= Business ================= */
            Section::make('Business')
                ->icon('heroicon-m-building-office-2')
                ->iconColor('primary')
                ->columns(3)
                ->schema([
                    TextEntry::make('business.company_name')
                        ->label('Company')
                        ->columnSpan(2)
                        ->icon('heroicon-m-building-storefront'),

                    TextEntry::make('business.business_email')
                        ->label('Email')
                        ->icon('heroicon-m-envelope'),

                    TextEntry::make('business.business_type'),
                    TextEntry::make('business.profile_verification')
                        ->badge()
                        ->color('success'),
                    TextEntry::make('business.rating')
                        ->icon('heroicon-m-star'),
                ]),

            /* ================= Vendor ================= */
            Section::make('Vendor')
                ->icon('heroicon-m-user-circle')
                ->iconColor('primary')
                ->collapsible()
                ->columns(3)
                ->schema([
                    TextEntry::make('business.vendor.full_name')
                        ->icon('heroicon-m-user')
                    ->color('success'),
                    TextEntry::make('business.vendor.email')
                        ->icon('heroicon-m-envelope')
                        ->color('success'),
                    TextEntry::make('business.vendor.phone_no')
                        ->icon('heroicon-m-phone')
                    ->color('success'),

                    TextEntry::make('business.vendor.country')
                    ->icon('heroicon-m-phone'),
                    TextEntry::make('business.vendor.city'),
                    TextEntry::make('business.vendor.years_of_experience')
                        ->suffix(' years'),
                ]),

            /* ================= Contact ================= */
            Section::make('Contact')
                ->icon('heroicon-m-phone-arrow-up-right')
                ->iconColor('primary')
                ->collapsible()
                ->collapsed()
                ->columns(2)
                ->schema([
                    TextEntry::make('business.business_email'),
                    TextEntry::make('business.business_phone'),
                ]),

            /* ================= Location ================= */
            Section::make('Location')
                ->icon('heroicon-m-map-pin')
                ->iconColor('primary')
                ->collapsible()
                ->collapsed()
                ->columns(3)
                ->schema([
                    TextEntry::make('business.street_address')->columnSpanFull(),
//                    TextEntry::make('business.city'),
//                    TextEntry::make('business.country'),
                    TextEntry::make('business.postal_code'),
                ]),

            /* ================= Payments ================= */
            Section::make('Payment Settings')
                ->icon('heroicon-m-banknotes')
                ->iconColor('primary')
                ->collapsible()
                ->collapsed()
                ->columns(3)
                ->schema([
                    TextEntry::make('business.payment_days_advance')->suffix(' days'),
                    TextEntry::make('business.payment_days_final')->suffix(' days'),
                    TextEntry::make('business.advance_percentage')->suffix('%'),
                ]),

            /* ================= Stats ================= */
            Section::make('Statistics')
                ->icon('heroicon-m-chart-bar')
                ->iconColor('primary')
                ->collapsible()
                ->collapsed()
                ->columns(3)
                ->schema([
                    TextEntry::make('business.view_count'),
                    TextEntry::make('business.social_count'),
                    TextEntry::make('business.last_login')
                        ->placeholder('Never'),
                ]),
        ];
    }
}
