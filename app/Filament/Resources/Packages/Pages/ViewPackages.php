<?php

namespace App\Filament\Resources\Packages\Pages;

use App\Filament\Resources\Packages\PackagesResource;
use Filament\Actions\EditAction;
use Filament\Resources\Pages\ViewRecord;

class ViewPackages extends ViewRecord
{
    protected static string $resource = PackagesResource::class;

    protected function getHeaderActions(): array
    {
        return [
            EditAction::make(),
        ];
    }
}
