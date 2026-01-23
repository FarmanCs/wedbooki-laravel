<?php

namespace Database\Seeders\Admin;

use App\Models\Admin\Feature;
use Illuminate\Database\Seeder;

class FeatureSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {


        // Create additional random features
        Feature::factory()->count(10)->create();
    }
}
