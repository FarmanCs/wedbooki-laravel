<?php

namespace App\Filament\Resources\CreditPlans\Pages;

use App\Filament\Resources\CreditPlans\CreditPlanResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListCreditPlans extends ListRecords
{
    protected static string $resource = CreditPlanResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
