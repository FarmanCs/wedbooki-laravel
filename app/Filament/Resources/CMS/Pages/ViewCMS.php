<?php

namespace App\Filament\Resources\CMS\Pages;

use App\Filament\Resources\CMS\CMSResource;
use Filament\Actions\EditAction;
use Filament\Resources\Pages\ViewRecord;

class ViewCMS extends ViewRecord
{
    protected static string $resource = CMSResource::class;

    protected function getHeaderActions(): array
    {
        return [
            EditAction::make(),
        ];
    }
}
