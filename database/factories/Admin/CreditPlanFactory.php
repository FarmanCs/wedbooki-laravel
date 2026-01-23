<?php

namespace Database\Factories\Admin;

use App\Models\Admin\CreditPlan;
use Illuminate\Database\Eloquent\Factories\Factory;

class CreditPlanFactory extends Factory
{
    protected $model = CreditPlan::class;

    public function definition(): array
    {
        $credits = fake()->randomElement([100, 250, 500, 1000, 2500, 5000]);
        $basePrice = $credits * 0.10;

        return [
            'image' => fake()->imageUrl(400, 400, 'business', true),
            'name' => $credits . ' Credits Plan',
            'description' => fake()->sentence(10),
            'price' => $basePrice,
            'discounted_percentage' => fake()->randomElement([0, 5, 10, 15, 20, 25]),
            'no_of_credits' => $credits,
        ];
    }

    /**
     * Indicate that the plan has a discount.
     */
    public function withDiscount(): static
    {
        return $this->state(fn (array $attributes) => [
            'discounted_percentage' => fake()->numberBetween(10, 30),
        ]);
    }

    /**
     * Indicate that the plan has no discount.
     */
    public function noDiscount(): static
    {
        return $this->state(fn (array $attributes) => [
            'discounted_percentage' => 0,
        ]);
    }
}
