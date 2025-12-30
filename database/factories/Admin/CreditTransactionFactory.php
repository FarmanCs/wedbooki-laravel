<?php

namespace Database\Factories\Admin;

use App\Models\Admin\CreditTransaction;
use App\Models\Vendor\Business;
use Illuminate\Database\Eloquent\Factories\Factory;

class CreditTransactionFactory extends Factory
{
    protected $model = CreditTransaction::class;

    public function definition(): array
    {
        $tranType = fake()->randomElement([CreditTransaction::TYPE_CREDIT, CreditTransaction::TYPE_DEBIT]);
        $credits = fake()->numberBetween(10, 500);

        return [
            'business_id' => Business::factory(),
            'no_of_credits' => $credits,
            'amount' => fake()->randomFloat(2, 10.00, 500.00),
            'from' => $tranType === CreditTransaction::TYPE_DEBIT ? 'business' : 'system',
            'to' => $tranType === CreditTransaction::TYPE_DEBIT ? 'system' : 'business',
            'tran_type' => $tranType,
        ];
    }

    /**
     * Indicate that this is a credit transaction.
     */
    public function credit(): static
    {
        return $this->state(fn (array $attributes) => [
            'tran_type' => CreditTransaction::TYPE_CREDIT,
            'from' => 'system',
            'to' => 'business',
        ]);
    }

    /**
     * Indicate that this is a debit transaction.
     */
    public function debit(): static
    {
        return $this->state(fn (array $attributes) => [
            'tran_type' => CreditTransaction::TYPE_DEBIT,
            'from' => 'business',
            'to' => 'system',
        ]);
    }
}
