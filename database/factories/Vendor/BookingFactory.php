<?php

namespace Database\Factories\Vendor;

use App\Models\Host\Host;
use App\Models\Vendor\Booking;
use App\Models\Vendor\Business;
use App\Models\Vendor\Vendor;
use Illuminate\Database\Eloquent\Factories\Factory;

class BookingFactory extends Factory
{
    protected $model = Booking::class;

    public function definition(): array
    {
        $amount = fake()->randomFloat(2, 500.00, 10000.00);
        $advancePercentage = fake()->randomElement([10, 20, 30, 40, 50]);
        $advanceAmount = ($amount * $advancePercentage) / 100;
        $finalAmount = $amount - $advanceAmount;

        // Generate event date up to 11 months in future (leave room for final_due_date)
        $eventDate = fake()->dateTimeBetween('now', '+11 months');

        // Clone the datetime objects to avoid mutation issues
        $eventDateClone = clone $eventDate;

        return [
            'host_id' => Host::factory(),
            'business_id' => Business::factory(),
            // 'vendor_id' => Vendor::factory(), // Removed - column doesn't exist in bookings table
            'package_id' => null,
            'amount' => $amount,
            'advance_percentage' => $advancePercentage,
            'advance_amount' => $advanceAmount,
            'final_amount' => $finalAmount,
            'advance_due_date' => fake()->dateTimeBetween('now', $eventDate),
            // Use relative modification instead of dateTimeBetween for dates after event
            'final_due_date' => (clone $eventDate)->modify('+' . rand(1, 30) . ' days'),
            'event_date' => $eventDate,
            'timezone' => fake()->timezone(),
            'time_slot' => fake()->randomElement(['morning', 'afternoon', 'evening']),
            'guests' => fake()->numberBetween(50, 500),
            'custom_booking_id' => 'BK-' . fake()->unique()->numerify('######'),
            'status' => fake()->randomElement(['pending', 'accepted', 'rejected', 'cancelled', 'confirmed', 'completed']),
            // Use relative modification for start/end times
            'start_time' => (clone $eventDateClone)->modify('+' . rand(1, 8) . ' hours'),
            'end_time' => (clone $eventDateClone)->modify('+' . rand(9, 12) . ' hours'),
            'approved_at' => fake()->boolean(60) ? fake()->dateTimeBetween('-1 month', 'now') : null,
            'payment_completed_at' => fake()->boolean(30) ? fake()->dateTimeBetween('-1 month', 'now') : null,
            'payment_status' => fake()->randomElement(['unpaid', 'advancePaid', 'refunded', 'fullyPaid']),
            'advance_paid' => fake()->boolean(60),
            'final_paid' => fake()->boolean(30),
            'is_synced_with_calendar' => fake()->boolean(50),
        ];
    }

    /**
     * Indicate that the booking is confirmed.
     */
    public function confirmed(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'confirmed',
            'approved_at' => fake()->dateTimeBetween('-1 month', 'now'),
        ]);
    }

    /**
     * Indicate that the booking is completed.
     */
    public function completed(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'completed',
            'payment_status' => 'fullyPaid',
            'advance_paid' => true,
            'final_paid' => true,
            'approved_at' => fake()->dateTimeBetween('-6 months', '-1 month'),
            'payment_completed_at' => fake()->dateTimeBetween('-1 month', 'now'),
        ]);
    }

    /**
     * Indicate that the booking is pending.
     */
    public function pending(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'pending',
            'payment_status' => 'unpaid',
            'advance_paid' => false,
            'final_paid' => false,
            'approved_at' => null,
            'payment_completed_at' => null,
        ]);
    }

    /**
     * Indicate that advance payment is made.
     */
    public function advancePaid(): static
    {
        return $this->state(fn (array $attributes) => [
            'advance_paid' => true,
            'final_paid' => false,
            'payment_status' => 'advancePaid',
        ]);
    }

    /**
     * Indicate that the booking is cancelled.
     */
    public function cancelled(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'cancelled',
        ]);
    }
}
