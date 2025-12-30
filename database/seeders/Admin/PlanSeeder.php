<?php

namespace Database\Seeders\Admin;

use App\Models\Admin\Feature;
use App\Models\Admin\Plan;
use App\Models\Vendor\Category;
use Illuminate\Database\Seeder;

class PlanSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Get or create a default category
        $category = Category::first() ?? Category::factory()->create();

        // Create predefined plans
        $plans = [
            [
                'name' => 'Basic Plan',
                'description' => 'Essential features for individual users',
                'badge' => 'Starter',
                'monthly_price' => 19.99,
                'quarterly_price' => 54.99,
                'yearly_price' => 199.99,
                'category_id' => $category->id,
                'is_active' => true,
                'published_at' => now(),
                'features_count' => 3,
            ],
            [
                'name' => 'Standard Plan',
                'description' => 'Advanced features for small teams',
                'badge' => 'Popular',
                'monthly_price' => 49.99,
                'quarterly_price' => 139.99,
                'yearly_price' => 499.99,
                'category_id' => $category->id,
                'is_active' => true,
                'published_at' => now(),
                'features_count' => 6,
            ],
            [
                'name' => 'Premium Plan',
                'description' => 'All features for large teams and enterprises',
                'badge' => 'Best Value',
                'monthly_price' => 99.99,
                'quarterly_price' => 279.99,
                'yearly_price' => 999.99,
                'category_id' => $category->id,
                'is_active' => true,
                'published_at' => now(),
                'features_count' => 12,
            ],
        ];

        foreach ($plans as $planData) {
            $featuresCount = $planData['features_count'];
            unset($planData['features_count']);

            $plan = Plan::create($planData);

            // Attach random features to the plan
            $features = Feature::active()->inRandomOrder()->limit($featuresCount)->get();
            $plan->features()->attach($features->pluck('id'));
        }

        // Create additional random plans
        Plan::factory()
            ->count(3)
            ->create()
            ->each(function ($plan) {
                // Attach 2-8 random features to each plan
                $features = Feature::active()->inRandomOrder()->limit(rand(2, 8))->get();
                $plan->features()->attach($features->pluck('id'));
            });
    }
}
