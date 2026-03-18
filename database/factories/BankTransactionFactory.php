<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Models\BankAccount;
use App\Models\BankTransaction;
use App\Services\Bank\Enums\TransactionIndicator;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends Factory<BankTransaction>
 */
class BankTransactionFactory extends Factory
{
    public function definition(): array
    {
        $indicator = $this->faker->randomElement([TransactionIndicator::Credit, TransactionIndicator::Debit]);

        return [
            'bank_account_id'         => BankAccount::factory(),
            'transaction_indicator'   => $indicator->value,
            'date'                    => $this->faker->dateTimeThisYear(),
            'amount'                  => $this->faker->randomFloat(2, 100, 5000),
            'currency'                => 'CZK',
            'external_key'            => 'TXN-' . Str::upper(Str::random(10)),
            'bank_account_identifier' => $this->faker->optional()->numerify('##########/####'),
            'variable_symbol'         => $this->faker->optional()->numerify('########'),
            'specific_symbol'         => null,
            'constant_symbol'         => $this->faker->optional()->numerify('####'),
            'description'             => $this->faker->optional()->sentence(),
            'note'                    => null,
            'status'                  => 'ok',
        ];
    }
}
