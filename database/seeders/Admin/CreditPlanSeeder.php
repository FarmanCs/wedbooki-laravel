<?php

namespace Database\Seeders\Admin;

use App\Models\Admin\CreditPlan;
use Illuminate\Database\Seeder;

class CreditPlanSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Create predefined credit plans
        $creditPlans = [
            [
                'image' => 'https://via.placeholder.com/400x400/3498db/ffffff?text=100+Credits',
                'name' => '100 Credits Plan',
                'description' => 'Perfect for occasional users',
                'price' => 10.00,
                'discounted_percentage' => 0,
                'no_of_credits' => 100,
            ],
            [
                'image' => 'https://via.placeholder.com/400x400/2ecc71/ffffff?text=250+Credits',
                'name' => '250 Credits Plan',
                'description' => 'Great for regular users',
                'price' => 22.50,
                'discounted_percentage' => 10,
                'no_of_credits' => 250,
            ],
            [
                'image' => 'https://via.placeholder.com/400x400/f39c12/ffffff?text=500+Credits',
                'name' => '500 Credits Plan',
                'description' => 'Best value for frequent users',
                'price' => 40.00,
                'discounted_percentage' => 20,
                'no_of_credits' => 500,
            ],
            [
                'image' => 'https://via.placeholder.com/400x400/e74c3c/ffffff?text=1000+Credits',
                'name' => '1000 Credits Plan',
                'description' => 'Ideal for power users',
                'price' => 75.00,
                'discounted_percentage' => 25,
                'no_of_credits' => 1000,
            ],
            [
                'image' => 'https://via.placeholder.com/400x400/9b59b6/ffffff?text=2500+Credits',
                'name' => '2500 Credits Plan',
                'description' => 'Enterprise level credits',
                'price' => 175.00,
                'discounted_percentage' => 30,
                'no_of_credits' => 2500,
            ],
        ];

        foreach ($creditPlans as $plan) {
            CreditPlan::create($plan);
        }

        // Create additional random credit plans
        CreditPlan::factory()->count(5)->create();
    }
}
