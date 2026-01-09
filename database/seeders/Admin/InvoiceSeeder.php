<?php

namespace Database\Seeders\Admin;

use App\Models\Admin\Invoice;
use App\Models\Host\Host;
use App\Models\Vendor\Booking;
use App\Models\Vendor\Business;
use App\Models\Vendor\Vendor;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class InvoiceSeeder extends Seeder
{
    public function run(): void
    {
        // 🔹 Reset table
        DB::table('invoices')->truncate();

        // 🔹 Ensure we have related records
        $hosts = Host::count() ? Host::all() : Host::factory()->count(5)->create();
        $vendors = Vendor::count() ? Vendor::all() : Vendor::factory()->count(5)->create();
        $businesses = Business::count() ? Business::all() : Business::factory()->count(5)->create();
        $bookings = Booking::count() ? Booking::all() : Booking::factory()->count(10)->create();

        // 🔹 Create fixed number of invoices per type
        Invoice::factory()->paid()->count(20)
            ->create([
                'host_id' => $hosts->random()->id,
                'vendor_id' => $vendors->random()->id,
                'business_id' => $businesses->random()->id,
                'booking_id' => $bookings->random()->id,
            ]);

        Invoice::factory()->unpaid()->count(15)
            ->create([
                'host_id' => $hosts->random()->id,
                'vendor_id' => $vendors->random()->id,
                'business_id' => $businesses->random()->id,
                'booking_id' => $bookings->random()->id,
            ]);

        Invoice::factory()->advancePaid()->count(10)
            ->create([
                'host_id' => $hosts->random()->id,
                'vendor_id' => $vendors->random()->id,
                'business_id' => $businesses->random()->id,
                'booking_id' => $bookings->random()->id,
            ]);

        Invoice::factory()->overdue()->count(8)
            ->create([
                'host_id' => $hosts->random()->id,
                'vendor_id' => $vendors->random()->id,
                'business_id' => $businesses->random()->id,
                'booking_id' => $bookings->random()->id,
            ]);

        Invoice::factory()->fullPaymentOnly()->paid()->count(5)
            ->create([
                'host_id' => $hosts->random()->id,
                'vendor_id' => $vendors->random()->id,
                'business_id' => $businesses->random()->id,
                'booking_id' => $bookings->random()->id,
            ]);

        Invoice::factory()->allowPartialPayment()->count(10)
            ->create([
                'host_id' => $hosts->random()->id,
                'vendor_id' => $vendors->random()->id,
                'business_id' => $businesses->random()->id,
                'booking_id' => $bookings->random()->id,
            ]);

        // 🔹 Total invoices = 68 (20+15+10+8+5+10)
        $this->command->info('✅ Invoices seeded safely.');
        $this->command->info('Total invoices: ' . Invoice::count());
        $this->command->info('Paid invoices: ' . Invoice::paid()->count());
        $this->command->info('Unpaid invoices: ' . Invoice::unpaid()->count());
        $this->command->info('Overdue invoices: ' . Invoice::overdue()->count());
    }
}
