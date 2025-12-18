<?php

namespace App\Filament\Resources\Vendors\Pages;

use App\Filament\Resources\Vendors\VendorResource;
use App\Filament\Resources\Vendors\Widgets\VendorStates;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;
use Filament\Schemas\Components\Tabs\Tab;
use Filament\Widgets\StatsOverviewWidget;
use Illuminate\Contracts\Support\Htmlable;
use Illuminate\Database\Eloquent\Builder;

class ListVendors extends ListRecords
{
    protected static string $resource = VendorResource::class;

    protected function getHeaderActions(): array
    {
        return [
//            CreateAction::make(),
        ];
    }

    protected function getHeaderWidgets(): array
    {
        return [
            VendorStates::class,
        ];
    }

    public function getHeading(): string|Htmlable|null
    {
        return "Vendor Management";
    }

    public function getTabs(): array
    {
        return [
            'all' => Tab::make(),
            'active' => Tab::make()
                ->modifyQueryUsing(fn (Builder $query) => $query->where('account_deactivated', false)),
            'inactive' => Tab::make()
                ->modifyQueryUsing(fn (Builder $query) => $query->where('account_deactivated', true)),
        ];
    }


}
