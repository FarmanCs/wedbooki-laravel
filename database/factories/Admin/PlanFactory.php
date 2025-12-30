<?php

namespace Database\Factories\Admin;

use App\Models\Admin\Plan;
use App\Models\Vendor\Category;
use Illuminate\Database\Eloquent\Factories\Factory;

class PlanFactory extends Factory
{
    protected $model = Plan::class;

    public function definition(): array
    {
        return [
            'name' => fake()->words(2, true) . ' Plan',
            'description' => fake()->paragraph(),
            'badge' => fake()->randomElement(['Popular', 'Best Value', 'Premium', 'Starter', null]),
            'monthly_price' => fake()->randomFloat(2, 19.99, 199.99),
            'quarterly_price' => fake()->randomFloat(2, 49.99, 499.99),
            'yearly_price' => fake()->randomFloat(2, 149.99, 1499.99),
            'category_id' => Category::factory(),
            'is_active' => fake()->boolean(80),
            'published_at' => fake()->boolean(70) ? fake()->dateTimeBetween('-1 year', 'now') : null,
        ];
    }

    /**
     * Indicate that the plan is active.
     */
    public function active(): static
    {
        return $this->state(fn (array $attributes) => [
            'is_active' => true,
        ]);
    }

    /**
     * Indicate that the plan is published.
     */
    public function published(): static
    {
        return $this->state(fn (array $attributes) => [
            'published_at' => fake()->dateTimeBetween('-6 months', 'now'),
        ]);
    }

    /**
     * Indicate that the plan is inactive.
     */
    public function inactive(): static
    {
        return $this->state(fn (array $attributes) => [
            'is_active' => false,
        ]);
    }
}
