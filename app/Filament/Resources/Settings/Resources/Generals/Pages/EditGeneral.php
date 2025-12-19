<?php

namespace App\Filament\Resources\Settings\Resources\Generals\Pages;

use App\Filament\Resources\Settings\Resources\Generals\GeneralResource;
use Filament\Actions\DeleteAction;
use Filament\Actions\ForceDeleteAction;
use Filament\Actions\RestoreAction;
use Filament\Actions\ViewAction;
use Filament\Resources\Pages\EditRecord;

class EditGeneral extends EditRecord
{
    protected static string $resource = GeneralResource::class;

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
