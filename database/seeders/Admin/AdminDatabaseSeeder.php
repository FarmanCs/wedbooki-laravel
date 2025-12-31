<?php

namespace Database\Seeders\Admin;

use Illuminate\Database\Seeder;

class AdminDatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database for Admin models.
     */
    public function run(): void
    {
        $this->call([
            AdminSeeder::class,
            FeatureSeeder::class,
            AdminPackageSeeder::class,
            PlanSeeder::class,
            CreditPlanSeeder::class,
            CreditTransactionSeeder::class,
            SupportQuerySeeder::class,
            TransactionSeeder::class,
            InvoiceSeeder::class,
        ]);
    }
}
