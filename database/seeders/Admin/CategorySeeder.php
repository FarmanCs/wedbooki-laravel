<?php

namespace Database\Seeders\Admin;

use App\Models\Admin\Category;
use Illuminate\Database\Seeder;

class CategorySeeder extends Seeder
{
    public function run(): void
    {

        Category::factory()->count(15)->create();
        $this->command->info('Random categories seeded successfully!');
        $this->command->info('Total categories: ' . Category::count());
    }
}
