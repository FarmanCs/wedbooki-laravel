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
            'partner_full_name' => $this->faker->optional()->name(),
            'partner_email' => $this->faker->optional()->safeEmail(),

            'country' => $this->faker->country(),
            'email' => $this->faker->unique()->safeEmail(),
            'linked_email' => $this->faker->optional()->safeEmail(),

            'country_code' => '+44',
            'phone_no' => $this->faker->numerify('##########'),

            'profile_image' => null,
            'about' => $this->faker->paragraph(),

            'wedding_date' => $this->faker->optional()->date(),

            'password' => Hash::make('password'),

            // OAuth
            'google_id' => null,
            'apple_id' => null,

            // ✅ FIXED NAMES
            'signup_method' => 'email',

            // ⚠ enum must match migration EXACTLY
            'status' => 'Pending',

            'role' => 'host',

            'otp' => $this->faker->numberBetween(100000, 999999),

            // ✅ FIXED
            'is_verified' => true,

            'pending_email' => null,

            'category' => json_encode([$this->faker->word()]),
            'event_type' => $this->faker->word(),
            'estimated_guests' => $this->faker->numberBetween(50, 500),
            'event_budget' => $this->faker->randomFloat(2, 1000, 50000),

            // ✅ FIXED
            'join_date' => now(),

            'account_deactivated' => false,
            'account_soft_deleted' => false,
            'account_soft_deleted_at' => null,
        ];
    }
}
