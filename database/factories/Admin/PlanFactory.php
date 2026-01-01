<?php

namespace Database\Factories\Admin;

use App\Models\Admin\Plan;
use App\Models\Admin\Category;
use Illuminate\Database\Eloquent\Factories\Factory;

class PlanFactory extends Factory
{
    protected $model = Plan::class;

    public function definition(): array
    {
        return [
            'name' => fake()->words(2, true) . ' Plan',
            'description' => fake()->paragraph(),
            'badge' => fake()->randomElement([
                'Starter',
                'Popular',
                'Best Value',
                'Premium',
            ]),
            'monthly_price' => fake()->randomFloat(2, 10, 100),
            'quarterly_price' => fake()->randomFloat(2, 30, 300),
            'yearly_price' => fake()->randomFloat(2, 100, 1000),
            'category_id' => Category::factory(), // fallback only
            'is_active' => true,
            'published_at' => now(),
        ];
    }

    public function active(): static
    {
        return $this->state([
            'is_active' => true,
        ]);
    }

    public function inactive(): static
    {
        return $this->state([
            'is_active' => false,
        ]);
    }

    public function published(): static
    {
        return $this->state([
            'published_at' => now(),
        ]);
    }
}
