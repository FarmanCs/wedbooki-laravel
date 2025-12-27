<?php

namespace App\Filament\Resources\Subscriptions\Pages;

use App\Filament\Resources\Subscriptions\Schemas\SubscriptionsForm;
use App\Filament\Resources\Subscriptions\SubscriptionsResource;
use App\Models\Admin\AdminPackage;
use App\Models\Admin\Feature;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\CreateRecord;
use Filament\Schemas\Schema;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class CreateSubscriptions extends CreateRecord
{
    protected static string $resource = SubscriptionsResource::class;


    protected function tiers(): array
    {
        return ['silver', 'gold', 'platinum', 'dimond'];
    }
    public function form(Schema $schema): Schema
    {
        return SubscriptionsForm::configure($schema);
    }

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        foreach ($this->tiers() as $tier) {
            if (empty($data["{$tier}_features"])) {
                throw new \Exception(ucfirst($tier) . ' must have at least one feature');
            }
        }
        return $data;
    }

    protected function handleRecordCreation(array $data): Model
    {
        return DB::transaction(function () use ($data) {

            $packages = collect($this->tiers())
                ->map(fn($tier) => $this->createPackage($tier, $data));

            Notification::make()
                ->success()
                ->title('Packages Created')
                ->send();

            return $packages->first();
        });
    }

    private function createPackage(string $tier, array $data): AdminPackage
    {
        $package = AdminPackage::create([
            'name' => ucfirst($tier),
            'description' => $data["{$tier}_description"],
            'badge' => $data["{$tier}_badge"] ?? null,
            'monthly_price' => $data["{$tier}_monthly_price"],
            'quarterly_price' => $data["{$tier}_quarterly_price"] ?? null,
            'yearly_price' => $data["{$tier}_yearly_price"] ?? null,
            'category_id' => $data['category_id'],
            'is_active' => $data['is_active'] ?? true,
            'published_at' => $data['published_at'] ?? now(),
        ]);

        $package->features()->sync($data["{$tier}_features"]);

        return $package;
    }



//    protected function handleRecordCreation(array $data): Model
//    {
//        $tiers = ['silver', 'gold', 'platinum'];
//        $createdPackages = [];
//        $featuresCreatedCount = 0;
//
//        DB::beginTransaction();
//
//        try {
//            $categoryId = $data['category_id'];
//
//            // Create packages for each tier
//            foreach ($tiers as $tier) {
//                $tierCapitalized = ucfirst($tier);
//
//                // Validate required fields
//                if (empty($data["{$tier}_description"]) || empty($data["{$tier}_monthly_price"])) {
//                    throw new \Exception("{$tierCapitalized} Description and Monthly Price are required");
//                }
//
//                // Validate features
//                if (empty($data["{$tier}_features"]) || count($data["{$tier}_features"]) === 0) {
//                    throw new \Exception("{$tierCapitalized} must have at least one feature");
//                }
//
//                // Create the package
//                $package = AdminPackage::create([
//                    'name' => $tierCapitalized,
//                    'description' => $data["{$tier}_description"],
//                    'badge' => $data["{$tier}_badge"] ?? null,
//                    'monthly_price' => $data["{$tier}_monthly_price"],
//                    'quarterly_price' => $data["{$tier}_quarterly_price"] ?? null,
//                    'yearly_price' => $data["{$tier}_yearly_price"] ?? null,
//                    'category_id' => $categoryId,
//                    'is_active' => $data['is_active'] ?? true,
//                    'published_at' => $data['published_at'] ?? now(),
//                ]);
//
//                // Attach selected features to package via pivot table
//                // Features are already created via createOptionUsing in the form
//                // We just need to attach the existing feature IDs
//                $featureIds = $data["{$tier}_features"];
//
//                // Validate that all features exist
//                $existingFeatures = Feature::whereIn('id', $featureIds)
//                    ->where('is_active', true)
//                    ->pluck('id')
//                    ->toArray();
//
//                if (count($existingFeatures) !== count($featureIds)) {
//                    throw new \Exception("Some selected features for {$tierCapitalized} are invalid or inactive");
//                }
//
//                // Attach features to package via pivot table in real-time
//                $package->features()->attach($existingFeatures);
//
//                $createdPackages[] = $package;
//            }
//
//            DB::commit();
//
//            // Count total features across all packages
//            $totalFeaturesAttached = array_sum(array_map(
//                fn($package) => $package->features()->count(),
//                $createdPackages
//            ));
//
//            // Send success notification
//            Notification::make()
//                ->success()
//                ->title('Packages Created Successfully')
//                ->body("Created 3 packages (Silver, Gold, Platinum) with {$totalFeaturesAttached} feature relationships")
//                ->send();
//
//            // Return the first package for redirect purposes
//            return $createdPackages[0];
//
//        } catch (\Exception $e) {
//            DB::rollBack();
//
//            Notification::make()
//                ->danger()
//                ->title('Package Creation Failed')
//                ->body($e->getMessage())
//                ->send();
//
//            throw $e;
//        }
//    }

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
