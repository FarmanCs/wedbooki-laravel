<?php

namespace Database\Seeders\Admin;

use App\Models\Admin\Plan;
use App\Models\Admin\Feature;
use App\Models\Admin\Category;
use Illuminate\Database\Seeder;

class PlanSeeder extends Seeder
{
    public function run(): void
    {
        // Ensure at least one category exists
        $category = Category::query()->first()
            ?? Category::factory()->create();

        // Create plans using ONLY factory values
        Plan::factory()
            ->count(10)
            ->active()
            ->published()
            ->create([
                // Relation only — NOT value columns
                'category_id' => $category->id,
            ])
            ->each(function (Plan $plan) {
                // Attach random active features
                $features = Feature::active()
                    ->inRandomOrder()
                    ->limit(rand(2, 8))
                    ->pluck('id');

                $plan->features()->sync($features);
            });
    }
}
