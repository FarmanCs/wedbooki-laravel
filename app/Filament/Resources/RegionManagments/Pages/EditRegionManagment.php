<?php

namespace App\Filament\Resources\RegionManagments\Pages;

use App\Filament\Resources\RegionManagments\RegionManagmentResource;
use Filament\Actions\DeleteAction;
use Filament\Actions\ViewAction;
use Filament\Resources\Pages\EditRecord;

class EditRegionManagment extends EditRecord
{
    protected static string $resource = RegionManagmentResource::class;

    protected function getHeaderActions(): array
    {
        return [
            ViewAction::make(),
            DeleteAction::make(),
        ];
    }
}
