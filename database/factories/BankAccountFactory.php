<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Models\BankAccount;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<BankAccount>
 */
class BankAccountFactory extends Factory
{
    public function definition(): array
    {
        return [
            'name'                => $this->faker->company() . ' účet',
            'code'                => $this->faker->randomElement([BankAccount::FIO_BANK, BankAccount::MONETA_MONEY_BANK]),
            'currency'            => 'CZK',
            'account_credentials' => ['token' => $this->faker->sha256()],
            'active'              => true,
            'last_synced'         => $this->faker->dateTimeThisMonth(),
        ];
    }
}
