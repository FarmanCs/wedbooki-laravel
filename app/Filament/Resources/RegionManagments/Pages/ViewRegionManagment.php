<?php

namespace App\Filament\Resources\RegionManagments\Pages;

use App\Filament\Resources\RegionManagments\RegionManagmentResource;
use Filament\Actions\EditAction;
use Filament\Resources\Pages\ViewRecord;

class ViewRegionManagment extends ViewRecord
{
    protected static string $resource = RegionManagmentResource::class;

    protected function getHeaderActions(): array
    {
        return [
            EditAction::make(),
        ];
    }
}
