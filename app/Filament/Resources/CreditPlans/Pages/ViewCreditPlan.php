<?php

namespace App\Filament\Resources\CreditPlans\Pages;

use App\Filament\Resources\CreditPlans\CreditPlanResource;
use Filament\Actions\EditAction;
use Filament\Resources\Pages\ViewRecord;

class ViewCreditPlan extends ViewRecord
{
    protected static string $resource = CreditPlanResource::class;

    protected function getHeaderActions(): array
    {
        return [
            EditAction::make(),
        ];
    }
}
