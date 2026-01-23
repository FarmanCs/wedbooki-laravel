<?php

namespace Database\Seeders\Admin;

use Illuminate\Database\Seeder;
use App\Models\Admin\Notification;

class NotificationSeeder extends Seeder
{
    public function run(): void
    {
        // Create 20 random notifications
        Notification::factory()->count(20)->create();

        // Create 5 drafts
        Notification::factory()->draft()->count(5)->create();

        // Create 5 scheduled notifications
        Notification::factory()->state([
            'send_mode' => 'Schedule',
            'scheduled_at' => now()->addDays(3),
        ])->count(5)->create();

        $this->command->info('Notifications seeded successfully!');
    }
}
