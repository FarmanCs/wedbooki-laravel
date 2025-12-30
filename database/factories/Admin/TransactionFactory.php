<?php

namespace Database\Factories\Admin;

use App\Models\Admin\Transaction;
use App\Models\Host\Host;
use App\Models\Vendor\Booking;
use App\Models\Vendor\Vendor;
use Illuminate\Database\Eloquent\Factories\Factory;

class TransactionFactory extends Factory
{
    protected $model = Transaction::class;

    public function definition(): array
    {
        // Use migration's enum values: 'initiated', 'successful', 'failed', 'refunded'
        $status = fake()->randomElement(['initiated', 'successful', 'failed', 'refunded']);

        return [
            'booking_id'           => Booking::factory(),
            'host_id'              => Host::factory(),
            'vendor_id'            => Vendor::factory(),
            'amount'               => fake()->randomFloat(2, 10, 5000),
            'status'               => $status,
            'payment_method'       => fake()->randomElement(['credit_card', 'debit_card', 'paypal', 'bank_transfer', 'wallet']),
            'payment_reference'    => fake()->uuid(),
            'sender_id'            => fake()->randomNumber(5),
            'receiver_id'          => fake()->randomNumber(5),
            'sender_type'          => fake()->randomElement(['App\Models\Vendor\Vendor', 'App\Models\Host\Host']),
            'receiver_type'        => fake()->randomElement(['App\Models\Vendor\Vendor', 'App\Models\Host\Host']),
            'sender_name'          => fake()->name(),
            'receiver_name'        => fake()->name(),
            'redirect_url'         => fake()->url(),
            'acquirer_ref'         => fake()->uuid(),
            'profile_id'           => fake()->randomNumber(6),
            'tran_type'            => fake()->randomElement(['sale', 'refund', 'auth', 'capture']),
            'tran_class'           => fake()->randomElement(['ecom', 'moto', 'recurring']),
            'cart_id'              => fake()->uuid(),
            'cart_currency'        => fake()->randomElement(['USD', 'EUR', 'GBP', 'PKR']),
            'comments'             => fake()->sentence(),
            'request_body'         => [
                'amount'      => fake()->randomFloat(2, 10, 5000),
                'currency'    => 'USD',
                'description' => fake()->sentence(),
            ],
            'click_pay_response'   => null,
            'click_pay_callback'   => null,
            'paid_at'              => null,
        ];
    }

    /** Completed transaction */
    public function completed(): static
    {
        return $this->state(function (array $attributes) {
            // Pick a moment up to 30 days ago
            $paidAt = fake()->dateTimeBetween('-30 days', 'now');

            return [
                'status'              => 'successful', // Changed from 'completed' to match migration
                'paid_at'             => $paidAt,
                'click_pay_response'  => [
                    'transaction_id' => fake()->uuid(),
                    'status'         => 'success',
                    'message'        => 'Payment successful',
                ],
                // Callback always happens after payment (within same time range)
                'click_pay_callback'  => [
                    'callback_time' => $paidAt->format('Y-m-d H:i:s'),
                    'status'        => 'success',
                ],
            ];
        });
    }

    /** Pending transaction */
    public function pending(): static
    {
        return $this->state(fn(array $attributes) => [
            'status'  => 'initiated', // Changed from 'pending' to match migration
            'paid_at' => null,
        ]);
    }

    /** Failed transaction */
    public function failed(): static
    {
        return $this->state(fn(array $attributes) => [
            'status'             => 'failed',
            'paid_at'            => null,
            'click_pay_response' => [
                'transaction_id' => fake()->uuid(),
                'status'         => 'failed',
                'message'        => 'Payment failed',
            ],
        ]);
    }

    /** Refunded transaction */
    public function refunded(): static
    {
        return $this->state(function (array $attributes) {
            $paidAt = fake()->dateTimeBetween('-30 days', '-1 day');

            return [
                'status'             => 'refunded',
                'paid_at'            => $paidAt,
                'click_pay_response' => [
                    'transaction_id' => fake()->uuid(),
                    'status'         => 'refunded',
                    'message'        => 'Payment refunded',
                ],
            ];
        });
    }
}
