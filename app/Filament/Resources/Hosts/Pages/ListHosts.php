<?php

namespace App\Filament\Resources\Hosts\Pages;

use App\Filament\Resources\Hosts\HostResource;
use App\Filament\Resources\Hosts\Widgets\HostStats;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListHosts extends ListRecords
{
    protected static string $resource = HostResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }

    protected function getHeaderWidgets(): array
    {
        return [
          HostStats::class,
        ];
    }
}
