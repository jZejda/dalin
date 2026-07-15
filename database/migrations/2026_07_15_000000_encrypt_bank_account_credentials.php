<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class () extends Migration {
    /**
     * Credentials move from plain JSON to an encrypted string (encrypted:array
     * cast), so the column type changes to TEXT and existing rows are
     * re-encrypted. Rows already encrypted (e.g. on a re-run) are skipped.
     */
    public function up(): void
    {
        Schema::table('bank_accounts', function (Blueprint $table): void {
            $table->text('account_credentials')->nullable()->change();
        });

        DB::table('bank_accounts')
            ->whereNotNull('account_credentials')
            ->orderBy('id')
            ->each(function (object $account): void {
                if ($this->isEncrypted((string) $account->account_credentials)) {
                    return;
                }

                DB::table('bank_accounts')
                    ->where('id', $account->id)
                    ->update([
                        'account_credentials' => Crypt::encryptString((string) $account->account_credentials),
                    ]);
            });
    }

    public function down(): void
    {
        DB::table('bank_accounts')
            ->whereNotNull('account_credentials')
            ->orderBy('id')
            ->each(function (object $account): void {
                if (!$this->isEncrypted((string) $account->account_credentials)) {
                    return;
                }

                DB::table('bank_accounts')
                    ->where('id', $account->id)
                    ->update([
                        'account_credentials' => Crypt::decryptString((string) $account->account_credentials),
                    ]);
            });

        Schema::table('bank_accounts', function (Blueprint $table): void {
            $table->json('account_credentials')->nullable()->change();
        });
    }

    private function isEncrypted(string $value): bool
    {
        try {
            Crypt::decryptString($value);

            return true;
        } catch (Throwable) {
            return false;
        }
    }
};
