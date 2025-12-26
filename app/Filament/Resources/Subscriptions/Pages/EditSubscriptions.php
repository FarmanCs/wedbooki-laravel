<?php

namespace App\Filament\Resources\Subscriptions\Pages;

use App\Filament\Resources\Subscriptions\Schemas\SubscriptionsEditForm;
use App\Filament\Resources\Subscriptions\SubscriptionsResource;
use Filament\Actions\DeleteAction;
use Filament\Actions\ForceDeleteAction;
use Filament\Actions\RestoreAction;
use Filament\Actions\ViewAction;
use Filament\Resources\Pages\EditRecord;
use Filament\Schemas\Schema;

class EditSubscriptions extends EditRecord
{
    protected static string $resource = SubscriptionsResource::class;

    protected function getHeaderActions(): array
    {
        return [
            ViewAction::make(),
            DeleteAction::make(),
            ForceDeleteAction::make(),
            RestoreAction::make(),
        ];
    }

    protected function getSavedNotificationTitle(): ?string
    {
        return 'Package updated successfully';
    }

    public function form(Schema $schema): Schema
    {
        return $schema->schema(SubscriptionsEditForm::getSchema());
    }

    public function getTitle(): string
    {
        return 'Edit Subscription Package';
    }
}
