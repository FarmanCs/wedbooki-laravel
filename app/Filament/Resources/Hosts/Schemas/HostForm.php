<?php

namespace App\Filament\Resources\Hosts\Schemas;

use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Schema;
use Illuminate\Support\Facades\Storage;

class HostForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                FileUpload::make('profile_image')
                    ->label('Profile Image')
                    ->image()
                    ->previewable(true)
                    ->imageEditor()
                    ->visibility('public')
                    ->circleCropper()
                    ->afterStateHydrated(function (FileUpload $component, $state) {
                        if (blank($state)) return;

                        // Build the base URL from your existing filesystem configuration
                        $bucket = config('filesystems.disks.s3.bucket');
                        $region = config('filesystems.disks.s3.region');

                        // Pattern: bucket.s3.region.amazonaws.com
                        $baseUrl = "https://{$bucket}.s3.{$region}.amazonaws.com/";

                        // Escape special characters for the regex pattern
                        $escapedBaseUrl = preg_quote($baseUrl, '/');

                        $path = preg_replace('/' . $escapedBaseUrl . '/', '', $state);

                        $component->state($path);
                    })
                    ->saveUploadedFileUsing(function ($file, callable $set) {
                        $path = $file->storePublicly('');
                        $url = Storage::url($path);
                        return $url;
                    })
                    ->maxSize(2048),

                TextInput::make('full_name')
                    ->label('Full Name')
                    ->required()
                    ->maxLength(255),

                TextInput::make('email')
                    ->label('Email')
                    ->email()
                    ->required()
                    ->unique(ignoreRecord: true),

                Select::make('status')
                    ->label('Status')
                    ->options([
                        'pending' => 'Pending',
                        'approved' => 'Approved',
                        'blocked' => 'Blocked',
                    ])
                    ->required(),

//                Toggle::make('is_active')
//                    ->label('Active')
//                    ->default(true),

            ]);
    }
}
