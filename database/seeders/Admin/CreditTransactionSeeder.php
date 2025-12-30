<?php

namespace Database\Seeders\Admin;

use App\Models\Admin\CreditTransaction;
use App\Models\Vendor\Business;
use Illuminate\Database\Seeder;

class CreditTransactionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Get existing businesses or create some
        $businesses = Business::limit(10)->get();

        if ($businesses->isEmpty()) {
            $businesses = Business::factory()->count(10)->create();
        }

        // Create credit transactions for each business
        foreach ($businesses as $business) {
            // Create some credit transactions (adding credits)
            CreditTransaction::factory()
                ->credit()
                ->count(rand(3, 7))
                ->create(['business_id' => $business->id]);

            // Create some debit transactions (using credits)
            CreditTransaction::factory()
                ->debit()
                ->count(rand(2, 5))
                ->create(['business_id' => $business->id]);
        }

        // Create additional random transactions
        CreditTransaction::factory()->count(50)->create();
    }
}
