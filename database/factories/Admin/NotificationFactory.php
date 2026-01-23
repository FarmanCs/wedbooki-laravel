<?php

namespace Database\Factories\Admin;

use App\Models\Admin\Notification;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Arr;
use Carbon\Carbon;

class NotificationFactory extends Factory
{
    protected $model = Notification::class;

    public function definition(): array
    {
        $type = Arr::random(Notification::TYPES);
        $recipients = Arr::random(Notification::RECIPIENTS);
        $deliveryMethod = Arr::random(Notification::DELIVERY_METHODS);
        $sendMode = Arr::random(Notification::SEND_MODES);
        $status = Arr::random(Notification::STATUS);

        $scheduledAt = null;
        if ($sendMode === 'Schedule') {
            $scheduledAt = $this->faker->dateTimeBetween('now', '+1 month');
        }

        return [
            'title' => $this->faker->sentence(5),
            'message' => $this->faker->paragraph(2),
            'type' => $type,
            'recipients' => $recipients,
            'delivery_method' => $deliveryMethod,
            'send_mode' => $sendMode,
            'scheduled_at' => $scheduledAt,
            'status' => $status,
        ];
    }

    /**
     * Only drafts
     */
    public function draft(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'draft',
        ]);
    }

    /**
     * Only published
     */
    public function published(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'published',
        ]);
    }
}
