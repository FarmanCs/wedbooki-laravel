<?php

namespace App\Filament\Resources\Supports\Pages;

use App\Filament\Resources\Supports\SupportResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListSupports extends ListRecords
{
    protected static string $resource = SupportResource::class;

    protected function getHeaderActions(): array
    {
        return [
            // No create action needed since this is view-only
        ];
    }

    // Add stats widgets (optional)
    protected function getHeaderWidgets(): array
    {
        return [
            // You can add stats widgets here if needed
        ];
    }
}
