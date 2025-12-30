<?php

namespace Database\Seeders\Admin;

use App\Models\Admin\Admin;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class AdminSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Create a default super admin
        Admin::create([
            'first_name' => 'Super',
            'email' => 'admin@example.com',
            'password' => Hash::make('password'),
            'role' => 'super_admin',
        ]);

        // Create additional admins
        Admin::factory()->count(5)->create();

        // Create some admins with specific roles
        Admin::factory()->superAdmin()->count(2)->create();
    }
}
