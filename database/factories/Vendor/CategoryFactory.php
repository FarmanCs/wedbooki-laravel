<?php

namespace Database\Factories\Vendor;

use App\Models\Vendor\Category;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Vendor\Category>
 */
class CategoryFactory extends Factory
{
    protected $model = Category::class;

    public function definition(): array
    {
        return [
            // Dynamically generate any category type
            'type' => $this->faker->words($this->faker->numberBetween(1, 3), true),

            // Random description
            'description' => $this->faker->sentence($this->faker->numberBetween(5, 12)),

            // Optionally leave image null for now
            'image' => null,
        ];
    }
}
