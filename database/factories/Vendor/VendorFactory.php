<?php

namespace Database\Factories\Vendor;

use App\Models\Vendor\Vendor;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class VendorFactory extends Factory
{
    protected $model = Vendor::class;

    public function definition(): array
    {
        return [
            'full_name' => $this->faker->name(),
            'email' => $this->faker->unique()->safeEmail(),
            'phone_no' => $this->faker->numerify('3#########'),
            'pending_email' => null,
            'country_code' => '+92',
            'profile_image' => null,
            'cover_image' => null,

            'years_of_experience' => $this->faker->numberBetween(1, 15),
            'languages' => ['English', 'Urdu'],
            'team_members' => $this->faker->numberBetween(1, 10),
            'specialties' => ['Consulting', 'Installation'],
            'about' => $this->faker->paragraph(),

            'country' => 'Pakistan',
            'city' => $this->faker->city(),
            'postal_code' => $this->faker->postcode(),

            'role' => 'vendor',
            'password' => Hash::make('password'),

            /**
             * 🔑 RELATIONAL KEYS
             * Keep these FIXED so other factories can link later
             */
            'category_id' => 1,   // Vendor\Category
            'otp' => null,
            'profile_verification' => true,
            'email_verified' => true,

            'stripe_account_id' => 'acct_' . Str::random(16),
            'bank_last4' => '1234',
            'bank_name' => 'HBL',
            'account_holder_name' => $this->faker->name(),
            'payout_currency' => 'PKR',

            'custom_vendor_id' => 'VND-' . strtoupper(Str::random(8)),
            'google_id' => null,
            'signup_method' => 'email',

            'last_login' => now(),
            'account_deactivated' => false,
            'account_soft_deleted' => false,
            'account_soft_deleted_at' => null,
            'auto_hard_delete_after_days' => 30,
        ];
    }
}
