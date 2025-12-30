<?php

namespace Database\Factories\Vendor;

use App\Models\Vendor\Category;
use Illuminate\Database\Eloquent\Factories\Factory;

class CategoryFactory extends Factory
{
    protected $model = Category::class;

    public function definition(): array
    {
        return [
            'type' => fake()->randomElement([
                'Photography',
                'Videography',
                'Catering',
                'Venue',
                'Decoration',
                'Music & Entertainment',
                'Makeup & Hair',
                'Wedding Planner',
                'Florist',
                'Transportation'
            ]),
            'description' => fake()->paragraph(2),
            'image' => fake()->imageUrl(640, 480, 'business', true),
        ];
    }

    /**
     * Photography category.
     */
    public function photography(): static
    {
        return $this->state(fn (array $attributes) => [
            'type' => 'Photography',
            'description' => 'Professional wedding photography services to capture your special moments.',
        ]);
    }

    /**
     * Catering category.
     */
    public function catering(): static
    {
        return $this->state(fn (array $attributes) => [
            'type' => 'Catering',
            'description' => 'Delicious catering services for your wedding and events.',
        ]);
    }

    /**
     * Venue category.
     */
    public function venue(): static
    {
        return $this->state(fn (array $attributes) => [
            'type' => 'Venue',
            'description' => 'Beautiful venues for your special day.',
        ]);
    }
}

