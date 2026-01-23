<?php

namespace Database\Factories\Admin;

use App\Models\Admin\Invoice;
use App\Models\Host\Host;
use App\Models\Vendor\Booking;
use App\Models\Vendor\Business;
use App\Models\Vendor\Vendor;
use Illuminate\Database\Eloquent\Factories\Factory;

class InvoiceFactory extends Factory
{
    protected $model = Invoice::class;

    public function definition(): array
    {
        $totalAmount = fake()->randomFloat(2, 100.00, 5000.00);
        $commissionRate = fake()->randomFloat(2, 5.00, 20.00);
        $advancePercentage = fake()->randomElement([10, 20, 30, 50]);
        $fullPaymentOnly = fake()->boolean(30);

        // Calculate amounts
        $platformShare = ($totalAmount * $commissionRate) / 100;
        $vendorShare = $totalAmount - $platformShare;
        $platformFeeFromUser = fake()->randomFloat(2, 5.00, 50.00);
        $totalUserPaid = $totalAmount + $platformFeeFromUser;

        $advanceAmount = $fullPaymentOnly ? 0 : ($totalAmount * $advancePercentage) / 100;
        $remainingAmount = $totalAmount - $advanceAmount;

        $isAdvancePaid = $fullPaymentOnly ? false : fake()->boolean(70);
        $isFinalPaid = $isAdvancePaid ? fake()->boolean(50) : fake()->boolean(20);

        $paymentType = $fullPaymentOnly
            ? Invoice::PAYMENT_TYPE_FULL
            : ($isFinalPaid ? Invoice::PAYMENT_TYPE_FINAL : Invoice::PAYMENT_TYPE_ADVANCE);

        return [
            'booking_id' => Booking::factory(),
            'host_id' => Host::factory(),
            'business_id' => Business::factory(),
            'vendor_id' => Vendor::factory(),
            'sender_name' => fake()->name(),
            'receiver_name' => fake()->name(),
            'invoice_number' => $this->generateInvoiceNumber(),
            'payment_type' => $paymentType,
            'total_amount' => $totalAmount,
            'advance_amount' => $advanceAmount,
            'remaining_amount' => $remainingAmount,
            'base_amount_paid' => $isFinalPaid ? $totalAmount : ($isAdvancePaid ? $advanceAmount : 0),
            'platform_fee_from_user' => $platformFeeFromUser,
            'total_user_paid' => $isFinalPaid ? $totalUserPaid : ($isAdvancePaid ? $advanceAmount + ($platformFeeFromUser / 2) : 0),
            'vendor_share' => $vendorShare,
            'platform_share' => $platformShare,
            'commission_rate' => $commissionRate,
            'vendor_plan_name' => fake()->randomElement(['Basic Plan', 'Premium Plan', 'Enterprise Plan', null]),
            'advance_paid_date' => $isAdvancePaid ? fake()->dateTimeBetween('-2 months', '-1 month') : null,
            'final_paid_date' => $isFinalPaid ? fake()->dateTimeBetween('-1 month', 'now') : null,
            'is_advance_paid' => $isAdvancePaid,
            'is_final_paid' => $isFinalPaid,
            'advance_due_date' => fake()->dateTimeBetween('now', '+1 week'),
            'final_due_date' => fake()->dateTimeBetween('+1 week', '+1 month'),
            'advance_percentage' => $advancePercentage,
            'full_payment_only' => $fullPaymentOnly,
        ];
    }

    /**
     * Generate a unique invoice number.
     */
    private function generateInvoiceNumber(): string
    {
        $prefix = 'INV';
        $date = fake()->dateTimeBetween('-6 months', 'now')->format('Ymd');
        $sequence = fake()->unique()->numberBetween(1, 9999);

        return sprintf('%s-%s-%04d', $prefix, $date, $sequence);
    }

    /**
     * Indicate that the invoice is fully paid.
     */
    public function paid(): static
    {
        return $this->state(fn (array $attributes) => [
            'is_advance_paid' => true,
            'is_final_paid' => true,
            'payment_type' => Invoice::PAYMENT_TYPE_FINAL,
            'advance_paid_date' => fake()->dateTimeBetween('-2 months', '-1 month'),
            'final_paid_date' => fake()->dateTimeBetween('-1 month', 'now'),
            'base_amount_paid' => $attributes['total_amount'],
            'total_user_paid' => $attributes['total_amount'] + $attributes['platform_fee_from_user'],
        ]);
    }

    /**
     * Indicate that the invoice is unpaid.
     */
    public function unpaid(): static
    {
        return $this->state(fn (array $attributes) => [
            'is_advance_paid' => false,
            'is_final_paid' => false,
            'payment_type' => Invoice::PAYMENT_TYPE_ADVANCE,
            'advance_paid_date' => null,
            'final_paid_date' => null,
            'base_amount_paid' => 0,
            'total_user_paid' => 0,
        ]);
    }

    /**
     * Indicate that only advance is paid.
     */
    public function advancePaid(): static
    {
        return $this->state(fn (array $attributes) => [
            'is_advance_paid' => true,
            'is_final_paid' => false,
            'payment_type' => Invoice::PAYMENT_TYPE_ADVANCE,
            'advance_paid_date' => fake()->dateTimeBetween('-1 month', 'now'),
            'final_paid_date' => null,
            'base_amount_paid' => $attributes['advance_amount'],
            'total_user_paid' => $attributes['advance_amount'] + ($attributes['platform_fee_from_user'] / 2),
        ]);
    }

    /**
     * Indicate that the invoice is overdue.
     */
    public function overdue(): static
    {
        return $this->state(fn (array $attributes) => [
            'is_advance_paid' => false,
            'is_final_paid' => false,
            'advance_due_date' => fake()->dateTimeBetween('-2 weeks', '-1 day'),
            'final_due_date' => fake()->dateTimeBetween('-1 week', '-1 day'),
        ]);
    }

    /**
     * Indicate that the invoice requires full payment only.
     */
    public function fullPaymentOnly(): static
    {
        return $this->state(fn (array $attributes) => [
            'full_payment_only' => true,
            'payment_type' => Invoice::PAYMENT_TYPE_FULL,
            'advance_amount' => 0,
            'remaining_amount' => $attributes['total_amount'],
            'advance_percentage' => 0,
        ]);
    }

    /**
     * Indicate that the invoice allows partial payment.
     */
    public function allowPartialPayment(): static
    {
        return $this->state(fn (array $attributes) => [
            'full_payment_only' => false,
        ]);
    }
}
