<?php

namespace App\Filament\Resources\Subscriptions\Pages;

use App\Filament\Resources\Subscriptions\SubscriptionsResource;
use App\Filament\Resources\Subscriptions\Widgets\SubscriptionPackagesWidget;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListSubscriptions extends ListRecords
{
    protected static string $resource = SubscriptionsResource::class;

//    protected function getHeaderWidgets(): array
//    {
//        return [
//           SubscriptionPackagesWidget::class,
//        ];
//    }
    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make()
                ->label('Create Package Bundle')
                ->icon('heroicon-o-plus-circle'),
        ];
    }

    public function getTitle(): string
    {
        return 'Subscriptions Packages';
    }


}
