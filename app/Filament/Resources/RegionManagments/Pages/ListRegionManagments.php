<?php

namespace App\Filament\Resources\RegionManagments\Pages;

use App\Filament\Resources\RegionManagments\RegionManagmentResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListRegionManagments extends ListRecords
{
    protected static string $resource = RegionManagmentResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
