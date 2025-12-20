<?php

namespace App\Filament\Resources\Categories\Schemas;

use Filament\Infolists\Components\ImageEntry;
use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class CategoryInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Image')
                    ->icon('heroicon-o-photo')
                    ->iconColor('primary')
                    ->schema([
                        ImageEntry::make('image')
                            ->label('')
                            ->disk('s3')
                            ->height(150)
                            ->width(420)
                    ]),
                Section::make('Info')
                    ->icon('heroicon-o-rectangle-stack')
                    ->iconColor('primary')
                    ->schema([
                        TextEntry::make('type')
                            ->label('Category Name')
                            ->weight('bold'),

                        TextEntry::make('description')
                            ->label('Description')
                            ->prose()
                            ->color('gray'),

                        TextEntry::make('created_at')
                            ->label('Created At')
                            ->dateTime()
                            ->since(),
                    ])
            ]);
    }
}
