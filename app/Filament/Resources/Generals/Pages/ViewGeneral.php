<?php

namespace App\Filament\Resources\Generals\Pages;

use App\Filament\Resources\Generals\GeneralResource;
use Filament\Actions\EditAction;
use Filament\Resources\Pages\ViewRecord;

class ViewGeneral extends ViewRecord
{
    protected static string $resource = GeneralResource::class;

    protected function getHeaderActions(): array
    {
        return [
            EditAction::make(),
        ];
    }
}
