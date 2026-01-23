<?php

namespace Database\Seeders\Admin;

use App\Models\Admin\Transaction;
use App\Models\Host\Host;
use App\Models\Vendor\Booking;
use App\Models\Vendor\Vendor;
use Illuminate\Database\Seeder;

class TransactionSeeder extends Seeder
{
    public function run(): void
    {
        // Ensure we have related records
        $hosts = Host::count() ? Host::all() : Host::factory()->count(5)->create();
        $vendors = Vendor::count() ? Vendor::all() : Vendor::factory()->count(5)->create();
        $bookings = Booking::count() ? Booking::all() : Booking::factory()->count(10)->create();

        // Helper for random related IDs
        $getRandomIds = function() use ($bookings, $hosts, $vendors) {
            return [
                'booking_id' => $bookings->random()->id,
                'host_id'    => $hosts->random()->id,
                'vendor_id'  => $vendors->random()->id,
            ];
        };

        //  Seed transactions with factory states
        Transaction::factory()->completed()->count(50)->create($getRandomIds());
        Transaction::factory()->pending()->count(15)->create($getRandomIds());
        Transaction::factory()->failed()->count(10)->create($getRandomIds());
        Transaction::factory()->completed()->count(5)->create($getRandomIds());
        Transaction::factory()->count(25)->create($getRandomIds()); // random statuses

        $this->command->info('Transactions seeded safely');
        $this->command->info('Total transactions: ' . Transaction::count());

        // Count by status using model query
        $this->command->info('Successful: ' . Transaction::where('status', 'successful')->count());
        $this->command->info('Pending: ' . Transaction::where('status', 'initiated')->count());
        $this->command->info('Failed: ' . Transaction::where('status', 'failed')->count());
        $this->command->info('Refunded: ' . Transaction::where('status', 'refunded')->count());
    }
}
