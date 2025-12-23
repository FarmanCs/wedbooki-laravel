<?php

namespace App\Filament\Resources\Categories\Schemas;

use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Illuminate\Support\Facades\Storage;

class CategoryForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                FileUpload::make('image')
                    ->label('Image')
                    ->image()
                    ->disk('s3')
                    ->directory('categories')
                    ->visibility('public')
                    ->imageEditor()
                    ->saveUploadedFileUsing(function ($file) {
                        $path = $file->storePublicly('categories', 's3');
                        return Storage::disk('s3')->url($path);
                    }),
                TextInput::make('type')
                    ->label('Category Name')
                    ->required()
                    ->maxLength(255)
                    ->unique(ignoreRecord: true)
                    ->placeholder('Photography, Catering'),

                TextInput::make('image_url'),

                Textarea::make('description')
                    ->label('Description')
                    ->rows(2)
                    ->placeholder('Brief description of the category'),

            ]);
    }
}
