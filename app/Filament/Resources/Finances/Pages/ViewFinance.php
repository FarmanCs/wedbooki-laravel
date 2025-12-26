<?php

namespace App\Filament\Resources\Finances\Pages;

use App\Filament\Resources\Finances\FinanceResource;
use App\Filament\Resources\Finances\Schemas\FinanceInfolist;
use Filament\Actions\EditAction;
use Filament\Resources\Pages\ViewRecord;
use Filament\Schemas\Schema;

class ViewFinance extends ViewRecord
{
    protected static string $resource = FinanceResource::class;


    public function infolist(Schema $schema): Schema
    {
        return FinanceInfolist::configure($schema);
    }
    protected function getHeaderActions(): array
    {
        return [
//            EditAction::make(),
        ];
    }
}
