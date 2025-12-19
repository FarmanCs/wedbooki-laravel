<?php

namespace App\Filament\Resources\Generals\Pages;

use App\Filament\Resources\Generals\GeneralResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListGenerals extends ListRecords
{
    protected static string $resource = GeneralResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
