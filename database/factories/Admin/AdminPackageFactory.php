<?php

namespace Database\Factories\Admin;

use App\Models\Admin\AdminPackage;
use App\Models\Admin\Category;
use Illuminate\Database\Eloquent\Factories\Factory;

class AdminPackageFactory extends Factory
{
    protected $model = AdminPackage::class;

    public function definition(): array
    {
        return [
            'name' => fake()->words(3, true) . ' Package',
            'description' => fake()->paragraph(),
            'badge' => fake()->randomElement(['Popular', 'Best Value', 'Premium', 'Starter', null]),
            'monthly_price' => fake()->randomFloat(2, 9.99, 99.99),
            'quarterly_price' => fake()->randomFloat(2, 25.99, 249.99),
            'yearly_price' => fake()->randomFloat(2, 89.99, 899.99),
            'category_id' => Category::factory(),
            'is_active' => fake()->boolean(80),
            'published_at' => fake()->boolean(70) ? fake()->dateTimeBetween('-1 year', 'now') : null,
        ];
    }

    /**
     * Indicate that the package is active.
     */
    public function active(): static
    {
        return $this->state(fn (array $attributes) => [
            'is_active' => true,
        ]);
    }

    /**
     * Indicate that the package is published.
     */
    public function published(): static
    {
        return $this->state(fn (array $attributes) => [
            'published_at' => fake()->dateTimeBetween('-6 months', 'now'),
        ]);
    }

    /**
     * Indicate that the package is inactive.
     */
    public function inactive(): static
    {
        return $this->state(fn (array $attributes) => [
            'is_active' => false,
        ]);
    }
}
