<?php

namespace Database\Factories\Admin;

use App\Models\Admin\Category;
use Illuminate\Database\Eloquent\Factories\Factory;

class CategoryFactory extends Factory
{
    protected $model = Category::class;

    public function definition(): array
    {
        return [
            'type' => fake()->unique()->words(2, true), // e.g. "Luxury Events"
            'description' => fake()->paragraph(2),
            'image' => fake()->imageUrl(640, 480, 'business', true),
        ];
    }
}
