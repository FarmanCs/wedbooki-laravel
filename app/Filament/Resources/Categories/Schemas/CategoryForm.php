<?php

namespace App\Filament\Resources\Categories\Schemas;

use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class CategoryForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Image')
                    ->icon('heroicon-o-rectangle-stack')
                    ->iconColor('primary')
                    ->schema([
                        FileUpload::make('image')
                            ->disk('s3')
                            ->directory('categories')
                            ->visibility('public')
                            ->image()
                            ->avatar()
                            ->imageEditor()
                            ->maxSize(2048)
                            ->preserveFilenames()
                            ->helperText('Upload a new image to replace the existing one.')

                    ]),
                Section::make('Categories Information')
                    ->icon('heroicon-o-book-open')
                    ->iconColor('primary')
                    ->schema([
                        TextInput::make('type')
                            ->label('Category Name')
                            ->required()
                            ->maxLength(255)
                            ->unique(ignoreRecord: true)
                            ->placeholder('Photography, Catering'),

                        Textarea::make('description')
                            ->label('Description')
                            ->rows(4)
                            ->placeholder('Brief description of the category'),

                    ])
            ]);
    }
}
