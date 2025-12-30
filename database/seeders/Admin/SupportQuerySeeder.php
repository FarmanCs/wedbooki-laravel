<?php

namespace Database\Seeders\Admin;

use App\Models\Admin\SupportQuery;
use Illuminate\Database\Seeder;

class SupportQuerySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Create pending support queries
        SupportQuery::factory()
            ->pending()
            ->count(10)
            ->create();

        // Create resolved support queries
        SupportQuery::factory()
            ->resolved()
            ->count(20)
            ->create();

        // Create high priority queries
        SupportQuery::factory()
            ->highPriority()
            ->pending()
            ->count(5)
            ->create();

        // Create queries with attachments
        SupportQuery::factory()
            ->withAttachments()
            ->count(8)
            ->create();

        // Create additional random queries
        SupportQuery::factory()->count(25)->create();
    }
}
