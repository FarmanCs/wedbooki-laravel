<?php

namespace Database\Seeders\Admin;

use App\Models\Admin\CreditPlan;
use Illuminate\Database\Seeder;

class CreditPlanSeeder extends Seeder
{
    public function run(): void
    {
        // Plans without discount
        CreditPlan::factory()
            ->count(5)
            ->noDiscount()
            ->create();

        // Plans with discount
        CreditPlan::factory()
            ->count(5)
            ->withDiscount()
            ->create();
    }
}
