<?php

namespace Database\Seeders\Admin;

use App\Models\Admin\AdminPackage;
use App\Models\Admin\Feature;
use App\Models\Vendor\Category;
use Illuminate\Database\Seeder;

class AdminPackageSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Get or create a default category
        $category = Category::first() ?? Category::factory()->create();

        // Create predefined packages
        $packages = [
            [
                'name' => 'Starter Package',
                'description' => 'Perfect for small businesses just getting started',
                'badge' => 'Popular',
                'monthly_price' => 29.99,
                'quarterly_price' => 79.99,
                'yearly_price' => 299.99,
                'category_id' => $category->id,
                'is_active' => true,
                'published_at' => now(),
                'features_count' => 3,
            ],
            [
                'name' => 'Professional Package',
                'description' => 'Ideal for growing businesses with advanced needs',
                'badge' => 'Best Value',
                'monthly_price' => 79.99,
                'quarterly_price' => 219.99,
                'yearly_price' => 799.99,
                'category_id' => $category->id,
                'is_active' => true,
                'published_at' => now(),
                'features_count' => 6,
            ],
            [
                'name' => 'Enterprise Package',
                'description' => 'Complete solution for large organizations',
                'badge' => 'Premium',
                'monthly_price' => 199.99,
                'quarterly_price' => 549.99,
                'yearly_price' => 1999.99,
                'category_id' => $category->id,
                'is_active' => true,
                'published_at' => now(),
                'features_count' => 10,
            ],
        ];

        foreach ($packages as $packageData) {
            $featuresCount = $packageData['features_count'];
            unset($packageData['features_count']);

            $package = AdminPackage::create($packageData);

            // Attach random features to the package
            $features = Feature::active()->inRandomOrder()->limit($featuresCount)->get();
            $package->features()->attach($features->pluck('id'));
        }

        // Create additional random packages
        AdminPackage::factory()
            ->count(5)
            ->create()
            ->each(function ($package) {
                // Attach 3-8 random features to each package
                $features = Feature::active()->inRandomOrder()->limit(rand(3, 8))->get();
                $package->features()->attach($features->pluck('id'));
            });
    }
}
