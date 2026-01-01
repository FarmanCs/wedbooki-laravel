<?php

namespace Database\Seeders\Vendor;

use App\Models\Admin\Category;
use App\Models\Vendor\Vendor;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class VendorSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Ensure we have categories
        $categories = Category::all();
        if ($categories->isEmpty()) {
            $this->call(CategorySeeder::class);
            $categories = Category::all();
        }

        // Create a default verified vendor
        Vendor::create([
            'full_name' => 'John Photography Studio',
            'email' => 'vendor@example.com',
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
            'category_id' => $categories->first()->id,
            'postal_code' => '10001',
            'profile_verification' => 'verified',
            'email_verified' => true,
            'custom_vendor_id' => 'VEN-000001',
            'signup_method' => 'email',
            'last_login' => now(),
        ]);

        // Create verified vendors with Stripe
        Vendor::factory()
            ->verified()
            ->withStripe()
            ->count(15)
            ->create();

        // Create verified vendors without Stripe
        Vendor::factory()
            ->verified()
            ->count(10)
            ->create();

        // Create unverified vendors
        Vendor::factory()
            ->unverified()
            ->count(5)
            ->create();

        // Create deactivated vendors
        Vendor::factory()
            ->deactivated()
            ->count(3)
            ->create();

        // Create vendors for each category
        foreach ($categories->take(5) as $category) {
            Vendor::factory()
                ->verified()
                ->count(2)
                ->create(['category_id' => $category->id]);
        }

        // Create additional random vendors
        Vendor::factory()->count(20)->create();

        $this->command->info('Vendors seeded successfully!');
        $this->command->info('Total vendors: ' . Vendor::count());
        $this->command->info('Verified vendors: ' . Vendor::where('email_verified', true)->count());
    }
}
