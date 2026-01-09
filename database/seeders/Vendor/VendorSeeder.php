<?php

namespace Database\Seeders\Vendor;

use App\Models\Vendor\Vendor;
use App\Models\Admin\Category;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class VendorSeeder extends Seeder
{
    public function run(): void
    {
        DB::table('vendors')->truncate();

        $categories = Category::pluck('id')->toArray();

        if (empty($categories)) {
            $this->command->error('Categories missing. Seed categories first.');
            return;
        }

        Vendor::create([
            'full_name' => 'John Photography Studio',
            'email' => 'vendor1@example.com',
            'phone_no' => '1234567890',
            'country_code' => '+1',
            'password' => Hash::make('password'),
            'years_of_experience' => 10,
            'languages' => ['English', 'Spanish'],
            'team_members' => 5,
            'specialties' => ['Weddings', 'Corporate Events'],
            'about' => 'Professional photography studio with 10 years of experience.',
            'country' => 'United States',
            'city' => 'New York',
            'role' => 'vendor',
            'category_id' => fake()->randomElement($categories),
            'postal_code' => '10001',
            'profile_verification' => 'verified',
            'email_verified' => true,
            'custom_vendor_id' => 'VEN-000001',
            'signup_method' => 'email',
            'last_login' => now(),
        ]);

        Vendor::factory()->verified()->withStripe()->count(15)
            ->create(['category_id' => fake()->randomElement($categories)]);

        Vendor::factory()->verified()->count(10)
            ->create(['category_id' => fake()->randomElement($categories)]);

        Vendor::factory()->unverified()->count(5)
            ->create(['category_id' => fake()->randomElement($categories)]);

        Vendor::factory()->deactivated()->count(3)
            ->create(['category_id' => fake()->randomElement($categories)]);

        Vendor::factory()->count(22)
            ->create(['category_id' => fake()->randomElement($categories)]);

        $this->command->info('Vendors seeded safely (56 total)');
    }
}
