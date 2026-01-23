<?php

namespace Database\Seeders\Vendor;

use App\Models\Vendor\Business;
use App\Models\Admin\Category;
use App\Models\Vendor\Vendor;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class BusinessSeeder extends Seeder
{
    public function run(): void
    {

        $categories = Category::pluck('id')->toArray();
        $vendors = Vendor::pluck('id')->toArray();

        // Featured
        Business::factory()->featured()->verified()->count(15)
            ->create($this->random($vendors, $categories));

        // Verified
        Business::factory()->verified()->count(10)
            ->create($this->random($vendors, $categories));

        // Venue
        Business::factory()->venue()->verified()->count(12)
            ->create($this->random($vendors, $categories));

        // High Rated
        Business::factory()->highRated()->verified()->count(14)
            ->create($this->random($vendors, $categories));

        // Extra
        Business::factory()->count(24)
            ->create($this->random($vendors, $categories));

        $this->command->info('✅ Businesses seeded safely (75 total)');
    }

    private function random(array $vendors, array $categories): array
    {
        return [
            'vendor_id' => fake()->randomElement($vendors),
            'category_id' => fake()->randomElement($categories),
        ];
    }
}
