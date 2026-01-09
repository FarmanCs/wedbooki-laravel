<?php

namespace Database\Seeders\Vendor;

use App\Models\Host\Host;
use App\Models\Vendor\Booking;
use App\Models\Vendor\Business;
use App\Models\Vendor\Vendor;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class BookingSeeder extends Seeder
{
    public function run(): void
    {
        DB::table('bookings')->truncate();

        $hosts = Host::count() ? Host::all() : Host::factory()->count(20)->create();
        $businesses = Business::count() ? Business::all() : Business::factory()->count(20)->create();
        $vendors = Vendor::count() ? Vendor::all() : Vendor::factory()->count(20)->create();

        Booking::factory()->confirmed()->count(20)
            ->create([
                'host_id' => $hosts->random()->id,
                'business_id' => $businesses->random()->id,
                'vendor_id' => $vendors->random()->id,
            ]);

        Booking::factory()->completed()->count(15)
            ->create([
                'host_id' => $hosts->random()->id,
                'business_id' => $businesses->random()->id,
                'vendor_id' => $vendors->random()->id,
            ]);

        Booking::factory()->pending()->count(10)
            ->create([
                'host_id' => $hosts->random()->id,
                'business_id' => $businesses->random()->id,
                'vendor_id' => $vendors->random()->id,
            ]);

        Booking::factory()->advancePaid()->count(12)
            ->create([
                'host_id' => $hosts->random()->id,
                'business_id' => $businesses->random()->id,
                'vendor_id' => $vendors->random()->id,
            ]);

        Booking::factory()->cancelled()->count(5)
            ->create([
                'host_id' => $hosts->random()->id,
                'business_id' => $businesses->random()->id,
                'vendor_id' => $vendors->random()->id,
            ]);

        Booking::factory()->count(20)
            ->create([
                'host_id' => $hosts->random()->id,
                'business_id' => $businesses->random()->id,
                'vendor_id' => $vendors->random()->id,
            ]);

        $this->command->info('Bookings seeded successfully!');
        $this->command->info('Total bookings: ' . Booking::count());
        $this->command->info('Confirmed bookings: ' . Booking::where('status', 'confirmed')->count());
        $this->command->info('Completed bookings: ' . Booking::where('status', 'completed')->count());
        $this->command->info('Pending bookings: ' . Booking::where('status', 'pending')->count());
    }
}
