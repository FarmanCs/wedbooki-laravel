<?php

namespace Database\Factories\Admin;

use App\Models\Admin\SupportQuery;
use Illuminate\Database\Eloquent\Factories\Factory;

class SupportQueryFactory extends Factory
{
    protected $model = SupportQuery::class;

    public function definition(): array
    {
        return [
            'full_name' => fake()->name(),
            'email' => fake()->safeEmail(),
            'phone_number' => fake()->phoneNumber(),
            'subject' => fake()->sentence(6),
            'priority' => fake()->randomElement(['low', 'medium', 'high', 'urgent']),
            'message' => fake()->paragraph(5),
            'attachments' => fake()->boolean(30) ? [
                fake()->imageUrl(640, 480, 'business', true),
                fake()->imageUrl(640, 480, 'business', true),
            ] : null,
            'status' => fake()->randomElement(['pending', 'in_progress', 'resolved', 'closed']),
        ];
    }

    /**
     * Indicate that the query is pending.
     */
    public function pending(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'pending',
        ]);
    }

    /**
     * Indicate that the query is resolved.
     */
    public function resolved(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'resolved',
        ]);
    }

    /**
     * Indicate that the query is high priority.
     */
    public function highPriority(): static
    {
        return $this->state(fn (array $attributes) => [
            'priority' => 'high',
        ]);
    }

    /**
     * Indicate that the query has attachments.
     */
    public function withAttachments(): static
    {
        return $this->state(fn (array $attributes) => [
            'attachments' => [
                fake()->imageUrl(640, 480, 'business', true),
                fake()->imageUrl(640, 480, 'business', true),
                fake()->url(),
            ],
        ]);
    }
}
