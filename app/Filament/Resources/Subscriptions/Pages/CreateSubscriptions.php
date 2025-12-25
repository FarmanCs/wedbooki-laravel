<?php

namespace App\Filament\Resources\Subscriptions\Pages;

use App\Filament\Resources\Subscriptions\SubscriptionsResource;
use App\Models\Admin\AdminPackage;
use App\Models\Admin\Feature;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class CreateSubscriptions extends CreateRecord
{
    protected static string $resource = SubscriptionsResource::class;

    protected function mutateFormDataBeforeCreate(array $data): array
    {

        return $data;
    }

    protected function handleRecordCreation(array $data): Model
    {
        $tiers = ['silver', 'gold', 'platinum'];
        $createdPackages = [];

        DB::beginTransaction();

        try {
            // Validate that category exists
            $categoryId = $data['category_id'];

            // Get features for this category
            $features = Feature::where('category_id', $categoryId)
                ->where('is_active', true)
                ->get();

            // Create packages for each tier
            foreach ($tiers as $tier) {
                $tierCapitalized = ucfirst($tier);

                // Validate required fields
                if (empty($data["{$tier}_description"]) || empty($data["{$tier}_monthly_price"])) {
                    throw new \Exception("{$tierCapitalized} Description and Monthly Price are required");
                }

                // Create the package
                $package = AdminPackage::create([
                    'name' => $tierCapitalized,
                    'description' => $data["{$tier}_description"],
                    'badge' => $data["{$tier}_badge"] ?? null,
                    'monthly_price' => $data["{$tier}_monthly_price"],
                    'quarterly_price' => $data["{$tier}_quarterly_price"] ?? null,
                    'yearly_price' => $data["{$tier}_yearly_price"] ?? null,
                    'category_id' => $categoryId,
                    'is_active' => $data['is_active'] ?? true,
                    'published_at' => $data['published_at'] ?? now(),
                ]);

                // Attach features to package based on tier
                // Filter features where the tier column is true/enabled
                $tierFeatures = $features->filter(function ($feature) use ($tier) {
                    return $feature->{$tier} === true || $feature->{$tier} === 1;
                });

                // Attach the features
                if ($tierFeatures->isNotEmpty()) {
                    $package->features()->attach($tierFeatures->pluck('id'));
                }

                $createdPackages[] = $package;
            }

            DB::commit();

            // Send success notification
            Notification::make()
                ->success()
                ->title('Packages Created Successfully')
                ->body('Created ' . count($createdPackages) . ' packages (Silver, Gold, Platinum)')
                ->send();

            // Return the first package for redirect purposes
            return $createdPackages[0];

        } catch (\Exception $e) {
            DB::rollBack();

            Notification::make()
                ->danger()
                ->title('Package Creation Failed')
                ->body($e->getMessage())
                ->send();

            throw $e;
        }
    }

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }

    protected function getCreatedNotification(): ?Notification
    {
        // We're handling notifications in handleRecordCreation
        return null;
    }
}
