<?php

namespace Database\Seeders\Vendor;

use App\Models\Vendor\Vendor;
use Illuminate\Database\Seeder;

class VendorSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Vendor::factory()->count(15)->create();
    }
}
