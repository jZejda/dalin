<?php

declare(strict_types=1);

namespace Database\Seeders\Demo;

use App\Models\BankAccount;
use Illuminate\Database\Seeder;
use Illuminate\Support\Carbon;

class DemoBankAccountSeeder extends Seeder
{
    public function run(): void
    {
        BankAccount::create([
            'name'                => 'Demo FIO účet',
            'code'                => BankAccount::FIO_BANK,
            'currency'            => 'CZK',
            'account_credentials' => ['token' => 'demo-fio-token-placeholder', 'account_id' => 'demo-fio-account-id'],
            'active'              => true,
            'last_synced'         => Carbon::now()->subHour()->toDateTimeString(),
        ]);
    }
}
