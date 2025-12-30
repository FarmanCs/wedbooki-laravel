<?php
namespace App\Filament\Pages;

use App\Filament\Widgets\Charts;
use App\Filament\Widgets\States;
use Filament\Pages\Dashboard as BaseDashboard;

class Dashboard extends BaseDashboard
{
    public function getWidgets(): array
    {
        return [
            States::class,
            Charts::make()
        ];
    }

}
