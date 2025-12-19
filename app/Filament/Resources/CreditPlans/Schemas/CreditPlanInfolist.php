<?php

namespace App\Filament\Resources\CreditPlans\Schemas;

use Filament\Infolists\Components\ImageEntry;
use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class CreditPlanInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Image')
                    ->schema([
                        ImageEntry::make('image')
                            ->label('Plan Image')
                            ->disk('s3') // ✅ Laravel filesystem abstraction
                            ->visibility('public')
                            ->height(200)
                            ->columnSpanFull(),
                    ]),

                Section::make('Credit Plan Details')->schema([
                    TextEntry::make('name')
                        ->label('Plan Name')
                        ->weight('bold'),

                    TextEntry::make('description')
                        ->label('Description')
                        ->columnSpanFull(),

                    TextEntry::make('price')
                        ->label('Price')
                        ->money('PKR'),

                    TextEntry::make('discounted_percentage')
                        ->label('Discount')
                        ->suffix('%'),

                    TextEntry::make('no_of_credits')
                        ->label('Credits'),

                    TextEntry::make('created_at')
                        ->label('Created At')
                        ->dateTime(),

                    TextEntry::make('deleted_at')
                        ->label('Deleted At')
                        ->dateTime()
                        ->visible(fn($record) => $record?->deleted_at !== null),
                ])
            ]);
    }
}
