<?php

namespace App\Filament\Resources\CreditPlans\Pages;

use App\Filament\Resources\CreditPlans\CreditPlanResource;
use App\Filament\Resources\CreditPlans\Widgets\CreditTransactionsTable;
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

    protected function getFooterWidgets(): array
    {
        return [
            CreditTransactionsTable::class,
        ];
    }
}
