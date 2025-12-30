<?php

namespace Database\Seeders\Admin;

use App\Models\Admin\Invoice;
use App\Models\Host\Host;
use App\Models\Vendor\Booking;
use App\Models\Vendor\Business;
use App\Models\Vendor\Vendor;
use Illuminate\Database\Seeder;

class InvoiceSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Ensure we have related records
        $bookings = Booking::limit(10)->get();
        $hosts = Host::limit(5)->get();
        $businesses = Business::limit(5)->get();
        $vendors = Vendor::limit(5)->get();

        if ($bookings->isEmpty()) {
            $bookings = Booking::factory()->count(10)->create();
        }
        if ($hosts->isEmpty()) {
            $hosts = Host::factory()->count(5)->create();
        }
        if ($businesses->isEmpty()) {
            $businesses = Business::factory()->count(5)->create();
        }
        if ($vendors->isEmpty()) {
            $vendors = Vendor::factory()->count(5)->create();
        }

        // Create fully paid invoices
        Invoice::factory()
            ->paid()
            ->count(20)
            ->create();

        // Create unpaid invoices
        Invoice::factory()
            ->unpaid()
            ->count(15)
            ->create();

        // Create invoices with only advance paid
        Invoice::factory()
            ->advancePaid()
            ->count(10)
            ->create();

        // Create overdue invoices
        Invoice::factory()
            ->overdue()
            ->count(8)
            ->create();

        // Create full payment only invoices
        Invoice::factory()
            ->fullPaymentOnly()
            ->paid()
            ->count(5)
            ->create();

        // Create invoices with partial payment allowed
        Invoice::factory()
            ->allowPartialPayment()
            ->count(10)
            ->create();

        // Create invoices for specific bookings with various states
        foreach ($bookings->take(5) as $booking) {
            // Fully paid invoice
            Invoice::factory()
                ->paid()
                ->create([
                    'booking_id' => $booking->id,
                    'host_id' => $hosts->random()->id,
                    'business_id' => $businesses->random()->id,
                    'vendor_id' => $vendors->random()->id,
                ]);

            // Advance paid invoice
            Invoice::factory()
                ->advancePaid()
                ->create([
                    'booking_id' => $booking->id,
                    'host_id' => $hosts->random()->id,
                    'business_id' => $businesses->random()->id,
                    'vendor_id' => $vendors->random()->id,
                ]);
        }

        // Create additional random invoices with different characteristics
        Invoice::factory()->count(30)->create();

        $this->command->info('Invoices seeded successfully!');
        $this->command->info('Total invoices: ' . Invoice::count());
        $this->command->info('Paid invoices: ' . Invoice::paid()->count());
        $this->command->info('Unpaid invoices: ' . Invoice::unpaid()->count());
        $this->command->info('Overdue invoices: ' . Invoice::overdue()->count());
    }
}
