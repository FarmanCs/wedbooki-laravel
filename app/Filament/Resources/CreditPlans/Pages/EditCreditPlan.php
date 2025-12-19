<?php

namespace App\Filament\Resources\CreditPlans\Pages;

use App\Filament\Resources\CreditPlans\CreditPlanResource;
use Filament\Actions\DeleteAction;
use Filament\Actions\ForceDeleteAction;
use Filament\Actions\RestoreAction;
use Filament\Actions\ViewAction;
use Filament\Resources\Pages\EditRecord;

class EditCreditPlan extends EditRecord
{
    protected static string $resource = CreditPlanResource::class;

    protected function getHeaderActions(): array
    {
        return [
            ViewAction::make(),
            DeleteAction::make(),
            ForceDeleteAction::make(),
            RestoreAction::make(),
        ];
    }
}
