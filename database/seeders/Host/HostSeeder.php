<?php

namespace Database\Seeders\Host;

use App\Models\Host\Host;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class HostSeeder extends Seeder
{
    public function run(): void
    {
        // Create a default verified host
        Host::factory()->state([
            'full_name' => 'John Doe',
            'partner_full_name' => 'Jane Smith',
            'partner_email' => 'jane@example.com',
            'email' => 'host@example.com',
            'country' => 'United States',
            'country_code' => '+1',
            'phone_no' => '1234567890',
            'password' => Hash::make('password'),
            'signup_method' => 'email',
            'status' => 'approved',
            'role' => 'host',
            'is_verified' => true,
            'wedding_date' => now()->addMonths(6),
            'category' => 'Wedding',
            'event_type' => 'Wedding',
            'estimated_guests' => 150,
            'event_budget' => 25000.00,
            'join_date' => now(),
        ])->create();

        // Generate random hosts
        Host::factory()->verified()->count(20)->create();
        Host::factory()->unverified()->count(5)->create();
        Host::factory()->googleSignup()->count(10)->create();
        Host::factory()->appleSignup()->count(5)->create();
        Host::factory()->deactivated()->count(3)->create();
        Host::factory()->count(25)->create();

        $this->command->info('Hosts seeded successfully!');
        $this->command->info('Total hosts: ' . Host::count());
        $this->command->info('Verified hosts: ' . Host::where('is_verified', true)->count());
    }
}
