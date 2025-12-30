<?php

namespace Database\Factories\Vendor;

use App\Models\Vendor\Category;
use App\Models\Vendor\Vendor;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\Hash;

class VendorFactory extends Factory
{
    protected $model = Vendor::class;

    public function definition(): array
    {
        return [
            'full_name' => fake()->name(),
            'email' => fake()->unique()->safeEmail(),
            'phone_no' => fake()->numerify('##########'),
            'pending_email' => null,
            'country_code' => fake()->randomElement(['+1', '+44', '+91', '+92', '+61']),
            'profile_image' => fake()->imageUrl(400, 400, 'people', true),
            'years_of_experience' => fake()->numberBetween(1, 20),
            'languages' => fake()->randomElements(['English', 'Spanish', 'French', 'German', 'Urdu', 'Arabic'], rand(1, 3)),
            'team_members' => fake()->numberBetween(1, 50),
            'specialties' => fake()->randomElements(['Weddings', 'Corporate Events', 'Parties', 'Ceremonies'], rand(1, 3)),
            'about' => fake()->paragraph(4),
            'country' => fake()->country(),
            'city' => fake()->city(),
            'role' => 'vendor',
            'password' => Hash::make('password'),
            'category_id' => Category::factory(),
            'postal_code' => fake()->postcode(),
            'otp' => null,
            'profile_verification' => fake()->randomElement(['pending', 'verified', 'approved', 'under_review', 'rejected', 'banned']),
            'email_verified' => fake()->boolean(80),
            'stripe_account_id' => fake()->boolean(50) ? 'acct_' . fake()->uuid() : null,
            'bank_last4' => fake()->boolean(50) ? fake()->numerify('####') : null,
            'bank_name' => fake()->boolean(50) ? fake()->randomElement(['Chase', 'Bank of America', 'Wells Fargo', 'Citibank']) : null,
            'account_holder_name' => fake()->boolean(50) ? fake()->name() : null,
            'payout_currency' => fake()->randomElement(['USD', 'EUR', 'GBP', 'PKR']),
            'custom_vendor_id' => 'VEN-' . fake()->unique()->numerify('######'),
            'google_id' => fake()->boolean(20) ? fake()->uuid() : null,
            'signup_method' => fake()->randomElement(['email', 'google']),
            'cover_image' => fake()->imageUrl(1200, 400, 'business', true),
            'last_login' => fake()->dateTimeBetween('-1 month', 'now'),
            'account_deactivated' => fake()->boolean(5),
            'account_soft_deleted' => fake()->boolean(3),
            'account_soft_deleted_at' => null,
            'auto_hard_delete_after_days' => 30,
        ];
    }

    /**
     * Indicate that the vendor is verified.
     */
    public function verified(): static
    {
        return $this->state(fn (array $attributes) => [
            'email_verified' => true,
            'profile_verification' => 'verified',
        ]);
    }

    /**
     * Indicate that the vendor is unverified.
     */
    public function unverified(): static
    {
        return $this->state(fn (array $attributes) => [
            'email_verified' => false,
            'profile_verification' => 'pending',
            'otp' => fake()->numerify('######'),
        ]);
    }

    /**
     * Indicate that the vendor has Stripe connected.
     */
    public function withStripe(): static
    {
        return $this->state(fn (array $attributes) => [
            'stripe_account_id' => 'acct_' . fake()->uuid(),
            'bank_last4' => fake()->numerify('####'),
            'bank_name' => fake()->randomElement(['Chase', 'Bank of America', 'Wells Fargo', 'Citibank']),
            'account_holder_name' => fake()->name(),
        ]);
    }

    /**
     * Indicate that the vendor is deactivated.
     */
    public function deactivated(): static
    {
        return $this->state(fn (array $attributes) => [
            'account_deactivated' => true,
        ]);
    }
}
