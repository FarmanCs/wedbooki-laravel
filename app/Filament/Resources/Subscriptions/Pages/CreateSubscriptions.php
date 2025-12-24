<?php

namespace App\Filament\Resources\Subscriptions\Pages;

use App\Filament\Resources\Subscriptions\SubscriptionsResource;
use Filament\Resources\Pages\CreateRecord;

class CreateSubscriptions extends CreateRecord
{
    protected static string $resource = SubscriptionsResource::class;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        // Validate required fields for all tiers
        $tiers = ['silver', 'gold', 'platinum'];

        foreach ($tiers as $tier) {
            if (empty($data["{$tier}_description"])) {
                throw new \Exception(ucfirst($tier) . " description is required");
            }
            if (empty($data["{$tier}_monthly_price"])) {
                throw new \Exception(ucfirst($tier) . " monthly price is required");
            }
        }

        // Set published_at to now if active
        if (!isset($data['published_at']) && ($data['is_active'] ?? true)) {
            $data['published_at'] = now();
        }

        return $data;
    }

    protected function getCreatedNotificationTitle(): ?string
    {
        return 'Package with all 3 tiers created successfully';
    }

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}
