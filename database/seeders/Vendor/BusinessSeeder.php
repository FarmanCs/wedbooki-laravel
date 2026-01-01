<?php

namespace Database\Seeders\Vendor;

use App\Models\Vendor\Business;
use App\Models\Admin\Category;
use App\Models\Vendor\Vendor;
use Illuminate\Database\Seeder;

class BusinessSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Ensure we have categories and vendors
        $categories = Category::all();
        $vendors = Vendor::all();

        if ($categories->isEmpty()) {
            $this->call(CategorySeeder::class);
            $categories = Category::all();
        }

        if ($vendors->isEmpty()) {
            $this->call(VendorSeeder::class);
            $vendors = Vendor::all();
        }

        // Create featured businesses
        Business::factory()
            ->featured()
            ->verified()
            ->highRated()
            ->count(50)
            ->create();

        // Create verified businesses
        Business::factory()
            ->verified()
            ->count(20)
            ->create();

        // Create venue businesses
        Business::factory()
            ->venue()
            ->verified()
            ->count(30)
            ->create();

        // Create high-rated businesses
        Business::factory()
            ->highRated()
            ->verified()
            ->count(30)
            ->create();

        // Create businesses for each vendor
        foreach ($vendors->take(10) as $vendor) {
            Business::factory()
                ->verified()
                ->count(rand(1, 3))
                ->create([
                    'vendor_id' => $vendor->id,
                    'category_id' => $vendor->category_id,
                ]);
        }

        // Create businesses for each category
        foreach ($categories as $category) {
            Business::factory()
                ->verified()
                ->count(3)
                ->create(['category_id' => $category->id]);
        }

        // Create additional random businesses
        Business::factory()->count(30)->create();

        $this->command->info('Businesses seeded successfully!');
        $this->command->info('Total businesses: ' . Business::count());
        $this->command->info('Featured businesses: ' . Business::where('is_featured', true)->count());
        $this->command->info('Verified businesses: ' . Business::where('profile_verification', 'verified')->count());
    }
}
