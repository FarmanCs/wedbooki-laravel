<?php

namespace Database\Seeders\Admin;

use App\Models\Admin\CreditTransaction;
use App\Models\Vendor\Business;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class CreditTransactionSeeder extends Seeder
{
    public function run(): void
    {
        //  Reset table
//        DB::table('credit_transactions')->truncate();

        // Get existing businesses
        $businesses = Business::count() ? Business::all() : Business::factory()->count(10)->create();

        // 🔹 Create credit & debit transactions per business in a controlled way
        foreach ($businesses as $business) {
            // Fixed number of credits
            CreditTransaction::factory()->credit()->count(3)
                ->create(['business_id' => $business->id]);

            // Fixed number of debits
            CreditTransaction::factory()->debit()->count(2)
                ->create(['business_id' => $business->id]);
        }

        // 🔹 Additional random transactions
        CreditTransaction::factory()->count(20)
            ->create([
                'business_id' => $businesses->random()->id,
            ]);

        $this->command->info('✅ Credit transactions seeded safely.');
        $this->command->info('Total credit transactions: ' . CreditTransaction::count());
    }
}
