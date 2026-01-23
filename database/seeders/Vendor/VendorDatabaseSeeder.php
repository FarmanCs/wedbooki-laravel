<?php

namespace Database\Seeders\Vendor;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class VendorDatabaseSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $this->call([
            VendorSeeder::class,
            BusinessSeeder::class,
            BookingSeeder::class,
        ]);
    }
}
