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
        // Create or get existing bookings, hosts, vendors
        $bookings = Booking::limit(10)->get();
        $hosts = Host::limit(5)->get();
        $vendors = Vendor::limit(5)->get();

        if ($bookings->isEmpty()) {
            $bookings = Booking::factory()->count(10)->create();
        }
        if ($hosts->isEmpty()) {
            $hosts = Host::factory()->count(5)->create();
        }
        if ($vendors->isEmpty()) {
            $vendors = Vendor::factory()->count(5)->create();
        }

        // Helper function to get random IDs
        $getRandomIds = function() use ($bookings, $hosts, $vendors) {
            return [
                'booking_id' => $bookings->random()->id,
                'host_id' => $hosts->random()->id,
                'vendor_id' => $vendors->random()->id,
            ];
        };

        // Completed transactions
        foreach (range(1, 50) as $i) {
            Transaction::factory()
                ->completed()
                ->create($getRandomIds());
        }

        // Pending transactions
        foreach (range(1, 15) as $i) {
            Transaction::factory()
                ->pending()
                ->create($getRandomIds());
        }

        // Failed transactions
        foreach (range(1, 10) as $i) {
            Transaction::factory()
                ->failed()
                ->create($getRandomIds());
        }

        // Transactions for specific bookings
        foreach ($bookings->take(5) as $booking) {
            Transaction::factory()
                ->completed()
                ->create([
                    'booking_id' => $booking->id,
                    'host_id' => $hosts->random()->id,
                    'vendor_id' => $vendors->random()->id,
                ]);
        }

        // Additional random transactions
        foreach (range(1, 25) as $i) {
            Transaction::factory()
                ->create($getRandomIds());
        }
    }
}
