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
            'full_name' => $this->faker->name(),
            'partner_full_name' => $this->faker->name(),
            'partner_email' => $this->faker->unique()->safeEmail(),
            'email' => $this->faker->unique()->safeEmail(),
            'linked_email' => $this->faker->boolean(30) ? $this->faker->safeEmail() : null,
            'country' => $this->faker->country(),
            'country_code' => $this->faker->randomElement(['+1', '+44', '+91', '+92', '+61']),
            'phone_no' => $this->faker->numerify('##########'),
            'profile_image' => $this->faker->imageUrl(400, 400, 'people', true),
            'about' => $this->faker->paragraph(3),
            'wedding_date' => $this->faker->dateTimeBetween('now', '+2 years'),
            'password' => Hash::make('password'),
            'google_id' => $this->faker->boolean(20) ? $this->faker->uuid() : null,
            'apple_id' => $this->faker->boolean(10) ? $this->faker->uuid() : null,
            'signup_method' => $this->faker->randomElement(['email', 'google', 'apple']),
            'status' => $this->faker->randomElement(['approved', 'pending', 'rejected', 'blocked']),
            'role' => 'host',
            'account_deactivated' => $this->faker->boolean(5),
            'account_soft_deleted' => $this->faker->boolean(5),
            'account_soft_deleted_at' => null,
            'otp' => null,
            'is_verified' => $this->faker->boolean(80),
            'pending_email' => null,
            'category' => $this->faker->randomElement(['Wedding', 'Engagement', 'Reception', 'Anniversary']),
            'event_type' => $this->faker->randomElement(['Wedding', 'Reception', 'Engagement Party', 'Bridal Shower']),
            'estimated_guests' => $this->faker->numberBetween(50, 500),
            'event_budget' => $this->faker->randomFloat(2, 5000, 100000),
            'join_date' => $this->faker->dateTime(),
        ];
    }

    public function verified(): static
    {
        return $this->state(fn () => ['is_verified' => true, 'otp' => null]);
    }

    public function unverified(): static
    {
        return $this->state(fn () => ['is_verified' => false, 'otp' => $this->faker->numerify('######')]);
    }

    public function deactivated(): static
    {
        return $this->state(fn () => ['account_deactivated' => true, 'status' => 'blocked']);
    }

    public function googleSignup(): static
    {
        return $this->state(fn () => ['signup_method' => 'google', 'google_id' => $this->faker->uuid(), 'is_verified' => true]);
    }

    public function appleSignup(): static
    {
        return $this->state(fn () => ['signup_method' => 'apple', 'apple_id' => $this->faker->uuid(), 'is_verified' => true]);
    }
}
