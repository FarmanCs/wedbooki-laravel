<?php

namespace Database\Seeders\Vendor;

use App\Models\Host\Host;
use App\Models\Vendor\Booking;
use App\Models\Vendor\Business;
use App\Models\Vendor\Vendor;
use Illuminate\Database\Seeder;

class BookingSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Ensure we have hosts, businesses, and vendors
        $hosts = Host::limit(20)->get();
        $businesses = Business::limit(20)->get();
        $vendors = Vendor::limit(20)->get();

        if ($hosts->isEmpty()) {
            $hosts = Host::factory()->count(20)->create();
        }
        if ($businesses->isEmpty()) {
            $businesses = Business::factory()->count(20)->create();
        }
        if ($vendors->isEmpty()) {
            $vendors = Vendor::factory()->count(20)->create();
        }

        // Create confirmed bookings
        Booking::factory()
            ->confirmed()
            ->count(20)
            ->create();

        // Create completed bookings
        Booking::factory()
            ->completed()
            ->count(15)
            ->create();

        // Create pending bookings
        Booking::factory()
            ->pending()
            ->count(10)
            ->create();

        // Create bookings with advance paid
        Booking::factory()
            ->advancePaid()
            ->count(12)
            ->create();

        // Create cancelled bookings
        Booking::factory()
            ->cancelled()
            ->count(5)
            ->create();

        // Create bookings for specific hosts
        foreach ($hosts->take(10) as $host) {
            Booking::factory()
                ->confirmed()
                ->count(rand(1, 3))
                ->create([
                    'host_id' => $host->id,
                    'business_id' => $businesses->random()->id,
                    'vendor_id' => $vendors->random()->id,
                ]);
        }

        // Create bookings for specific businesses
        foreach ($businesses->take(10) as $business) {
            Booking::factory()
                ->count(rand(2, 5))
                ->create([
                    'business_id' => $business->id,
                    'vendor_id' => $business->vendor_id,
                ]);
        }

        // Create additional random bookings
        Booking::factory()->count(30)->create();

        $this->command->info('Bookings seeded successfully!');
        $this->command->info('Total bookings: ' . Booking::count());
        $this->command->info('Confirmed bookings: ' . Booking::where('status', 'confirmed')->count());
        $this->command->info('Completed bookings: ' . Booking::where('status', 'completed')->count());
        $this->command->info('Pending bookings: ' . Booking::where('status', 'pending')->count());
    }
}
