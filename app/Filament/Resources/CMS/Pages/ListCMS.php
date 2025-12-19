<?php

namespace App\Filament\Resources\CMS\Pages;

use App\Filament\Resources\CMS\CMSResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListCMS extends ListRecords
{
    protected static string $resource = CMSResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
