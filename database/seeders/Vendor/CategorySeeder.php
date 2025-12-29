<?php

namespace Database\Seeders\Vendor;

use App\Models\Vendor\Category;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class CategorySeeder extends Seeder
{
    public function run(): void
    {
        Category::factory()->count(15)->create();
    }
}
