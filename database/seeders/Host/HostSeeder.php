<?php

namespace Database\Seeders\Host;

use App\Models\Host\Host;
use Illuminate\Database\Seeder;

class HostSeeder extends Seeder
{
    public function run(): void
    {
        // Create 20 hosts
        Host::factory()
            ->count(20)
            ->create()
            ->each(function ($host) {
                // Optionally attach favorite businesses if you want
                $businessIds = \App\Models\Vendor\Business::inRandomOrder()->take(rand(1, 5))->pluck('id');
                $host->favouriteBusinesses()->attach($businessIds);
            });
    }
}
