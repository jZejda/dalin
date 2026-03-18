<?php

declare(strict_types=1);

namespace Database\Seeders\Demo;

use App\Models\BankAccount;
use App\Models\BankTransaction;
use App\Models\User;
use App\Services\Bank\Enums\TransactionIndicator;
use Illuminate\Database\Seeder;
use Illuminate\Support\Carbon;
use Illuminate\Support\Str;

class DemoBankTransactionSeeder extends Seeder
{
    public function run(): void
    {
        $faker      = \Faker\Factory::create('cs_CZ');
        $bankAccount = BankAccount::first();

        if ($bankAccount === null) {
            return;
        }

        $users = User::all();

        // ~150 transactions over the last 12 months
        for ($i = 0; $i < 150; $i++) {
            $date      = Carbon::now()->subDays($faker->numberBetween(0, 365));
            $isCredit  = $faker->boolean(75); // 75% incoming (member payments)
            $indicator = $isCredit ? TransactionIndicator::Credit : TransactionIndicator::Debit;
            $amount    = $isCredit
                ? $faker->randomElement([500, 1000, 1500, 2000, 2500, 3000, 5000])
                : $faker->randomElement([200, 350, 500, 750, 1000]);

            $user = $users->random();
            /** @var string $expenseDescription */
            $expenseDescription = $faker->words(3, asText: true);

            BankTransaction::create([
                'bank_account_id'        => $bankAccount->id,
                'transaction_indicator'  => $indicator->value,
                'date'                   => $date->toDateTimeString(),
                'amount'                 => $amount,
                'currency'               => 'CZK',
                'external_key'           => 'DEMO-' . Str::upper(Str::random(12)),
                'bank_account_identifier' => $faker->optional(0.8)->numerify('##########/####'),
                'variable_symbol'        => $user->payer_variable_symbol,
                'specific_symbol'        => null,
                'constant_symbol'        => $faker->optional(0.3)->numerify('####'),
                'description'            => $isCredit
                    ? 'Platba členský příspěvek ' . $user->name
                    : 'Výdaj ' . $expenseDescription,
                'note'                   => $faker->optional(0.2)->sentence(),
                'status'                 => 'ok',
            ]);
        }
    }
}
