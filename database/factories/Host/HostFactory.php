<?php

namespace Database\Factories\Host;

use App\Models\Host\Host;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\Hash;

class HostFactory extends Factory
{
    protected $model = Host::class;

    public function definition(): array
    {
        return [
            'full_name' => fake()->name(),
            'partner_full_name' => fake()->name(),
            'partner_email' => fake()->unique()->safeEmail(),
            'country' => fake()->country(),
            'email' => fake()->unique()->safeEmail(),
            'linked_email' => fake()->boolean(30) ? fake()->safeEmail() : null,
            'country_code' => fake()->randomElement(['+1', '+44', '+91', '+92', '+61']),
            'phone_no' => fake()->numerify('##########'),
            'profile_image' => fake()->imageUrl(400, 400, 'people', true),
            'about' => fake()->paragraph(3),
            'wedding_date' => fake()->dateTimeBetween('now', '+2 years'),
            'password' => Hash::make('password'),
            'google_id' => fake()->boolean(20) ? fake()->uuid() : null,
            'apple_id' => fake()->boolean(10) ? fake()->uuid() : null,
            'signup_method' => fake()->randomElement(['email', 'google', 'apple']),
            'status' => fake()->randomElement(['approved', 'pending', 'rejected', 'blocked', 'Banned', 'Pending']),
            'role' => 'host',
            'account_deactivated' => fake()->boolean(5),
            'account_soft_deleted' => fake()->boolean(5),
            'account_soft_deleted_at' => null,
            'otp' => null,
            'is_verified' => fake()->boolean(80),
            'pending_email' => null,
            'category' => fake()->randomElement(['Wedding', 'Engagement', 'Reception', 'Anniversary']),
            'event_type' => fake()->randomElement(['Wedding', 'Reception', 'Engagement Party', 'Bridal Shower']),
            'estimated_guests' => fake()->numberBetween(50, 500),
            'event_budget' => fake()->randomFloat(2, 5000.00, 100000.00),
            'join_date' => fake()->dateTime(),
        ];
    }

    /**
     * Indicate that the host is verified.
     */
    public function verified(): static
    {
        return $this->state(fn (array $attributes) => [
            'is_verified' => true,
            'otp' => null,
        ]);
    }

    /**
     * Indicate that the host is unverified.
     */
    public function unverified(): static
    {
        return $this->state(fn (array $attributes) => [
            'is_verified' => false,
            'otp' => fake()->numerify('######'),
        ]);
    }

    /**
     * Indicate that the host account is deactivated.
     */
    public function deactivated(): static
    {
        return $this->state(fn (array $attributes) => [
            'account_deactivated' => true,
            'status' => 'blocked',
        ]);
    }

    /**
     * Indicate that the host signed up with Google.
     */
    public function googleSignup(): static
    {
        return $this->state(fn (array $attributes) => [
            'signup_method' => 'google',
            'google_id' => fake()->uuid(),
            'is_verified' => true,
        ]);
    }

    /**
     * Indicate that the host signed up with Apple.
     */
    public function appleSignup(): static
    {
        return $this->state(fn (array $attributes) => [
            'signup_method' => 'apple',
            'apple_id' => fake()->uuid(),
            'is_verified' => true,
        ]);
    }
}
