<?php

namespace Database\Seeders\Admin;

use App\Models\Admin\AdminPackage;
use Illuminate\Database\Seeder;

class AdminPackageSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Create 10 random packages using the factory
        AdminPackage::factory()
            ->count(10)
            ->create()
            ->each(function ($package) {
                // Attach 3-8 random active features to each package
                $features = $package->features()->inRandomOrder()->limit(rand(3, 8))->get();
                $package->features()->attach($features->pluck('id'));
            });

        $this->command->info('Admin packages seeded successfully!');
    }
}
