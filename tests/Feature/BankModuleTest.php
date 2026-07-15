<?php

declare(strict_types=1);

use App\Filament\Clusters\Config\Pages\Settings;
use App\Filament\Resources\BankAccounts\BankAccountResource;
use App\Filament\Resources\BankAccounts\Pages\CreateBankAccount;
use App\Filament\Resources\BankAccounts\Pages\EditBankAccount;
use App\Filament\Resources\BankTransactions\BankTransactionResource;
use App\Http\Controllers\Cron\Jobs\UpdateBankTransaction;
use App\Models\AppSetting;
use App\Models\BankAccount;
use App\Services\Bank\Connector\FioBank;
use App\Services\Bank\Connector\MonetaBank;
use Illuminate\Support\Arr;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\DB;
use Livewire\Livewire;

beforeEach(function (): void {
    Cache::flush();
});

it('saves bank module toggle from settings page', function (): void {
    actingAsSuperAdmin();

    Livewire::test(Settings::class)
        ->set('bank_enabled', true)
        ->call('submit')
        ->assertHasNoErrors();

    expect(AppSetting::isBankModuleEnabled())->toBeTrue();
});

it('hides bank pages when module is disabled', function (): void {
    actingAsSuperAdmin();
    AppSetting::set(AppSetting::BANK_MODULE_ENABLED, false);

    $this->get(BankTransactionResource::getUrl('index'))->assertForbidden();
    $this->get(BankAccountResource::getUrl('index'))->assertForbidden();
});

it('shows bank pages when module is enabled', function (): void {
    actingAsSuperAdmin();
    AppSetting::set(AppSetting::BANK_MODULE_ENABLED, true);

    $this->get(BankTransactionResource::getUrl('index'))->assertOk();
    $this->get(BankAccountResource::getUrl('index'))->assertOk();
});

it('stores account credentials encrypted at rest', function (): void {
    $account = BankAccount::factory()->create([
        'account_credentials' => ['token' => 'plain-secret-token'],
    ]);

    $raw = (string) $account->refresh()->getRawOriginal('account_credentials');

    expect($raw)->not->toContain('plain-secret-token')
        ->and(Crypt::decryptString($raw))->toContain('plain-secret-token')
        ->and($account->account_credentials)->toBe(['token' => 'plain-secret-token']);
});

it('skips bank sync when module is disabled', function (): void {
    AppSetting::set(AppSetting::BANK_MODULE_ENABLED, false);
    $lastSynced = Carbon::parse('2026-07-01 10:00:00');
    $account = BankAccount::factory()->create([
        'code' => BankAccount::FIO_BANK,
        'active' => true,
        'last_synced' => $lastSynced,
    ]);

    $this->mock(FioBank::class)->shouldNotReceive('getTransactions');
    $this->mock(MonetaBank::class)->shouldNotReceive('getTransactions');

    (new UpdateBankTransaction())->run();

    expect($account->refresh()->last_synced?->equalTo($lastSynced))->toBeTrue();
});

it('skips inactive accounts during bank sync', function (): void {
    AppSetting::set(AppSetting::BANK_MODULE_ENABLED, true);
    BankAccount::query()->update(['active' => false]);
    BankAccount::factory()->create([
        'code' => BankAccount::FIO_BANK,
        'active' => false,
    ]);

    $this->mock(FioBank::class)->shouldNotReceive('getTransactions');
    $this->mock(MonetaBank::class)->shouldNotReceive('getTransactions');

    (new UpdateBankTransaction())->run();
});

it('syncs active accounts when module is enabled', function (): void {
    AppSetting::set(AppSetting::BANK_MODULE_ENABLED, true);
    BankAccount::query()->update(['active' => false]);
    $account = BankAccount::factory()->create([
        'code' => BankAccount::FIO_BANK,
        'active' => true,
        'last_synced' => Carbon::parse('2026-07-01 10:00:00'),
    ]);

    $this->mock(FioBank::class)
        ->shouldReceive('getTransactions')
        ->once()
        ->andReturn([]);

    (new UpdateBankTransaction())->run();

    expect($account->refresh()->last_synced?->greaterThan('2026-07-01 10:00:00'))->toBeTrue();
});

it('skips accounts with unknown connector code', function (): void {
    AppSetting::set(AppSetting::BANK_MODULE_ENABLED, true);
    BankAccount::query()->update(['active' => false]);

    // Legacy row with a code outside the enum — must be skipped, not crash the sync.
    DB::table('bank_accounts')->insert([
        'name' => 'Neznámá banka',
        'code' => 'neznamaBanka',
        'currency' => 'CZK',
        'account_credentials' => null,
        'active' => true,
        'last_synced' => null,
        'created_at' => now(),
        'updated_at' => now(),
    ]);

    $this->mock(FioBank::class)->shouldNotReceive('getTransactions');
    $this->mock(MonetaBank::class)->shouldNotReceive('getTransactions');

    (new UpdateBankTransaction())->run();

    expect(DB::table('bank_accounts')->where('code', 'neznamaBanka')->value('last_synced'))->toBeNull();
});

it('creates bank connection with write-only credentials', function (): void {
    actingAsSuperAdmin();
    AppSetting::set(AppSetting::BANK_MODULE_ENABLED, true);

    Livewire::test(CreateBankAccount::class)
        ->fillForm([
            'name' => 'Fio běžný účet',
            'code' => BankAccount::FIO_BANK,
            'currency' => 'CZK',
            'active' => true,
            'credentials.token' => 'tajny-fio-token',
        ])
        ->call('create')
        ->assertHasNoFormErrors();

    $account = BankAccount::query()->latest('id')->firstOrFail();

    expect($account->account_credentials)->toBe(['token' => 'tajny-fio-token'])
        ->and((string) $account->getRawOriginal('account_credentials'))->not->toContain('tajny-fio-token');
});

it('does not expose stored credentials in the edit form', function (): void {
    actingAsSuperAdmin();
    AppSetting::set(AppSetting::BANK_MODULE_ENABLED, true);
    $account = BankAccount::factory()->create([
        'code' => BankAccount::FIO_BANK,
        'account_credentials' => ['token' => 'puvodni-token'],
    ]);

    Livewire::test(EditBankAccount::class, ['record' => $account->getRouteKey()])
        ->assertFormSet(function (array $state): void {
            expect(Arr::get($state, 'credentials.token'))->toBeNull();
        });
});

it('keeps stored credentials when fields are left empty on edit', function (): void {
    actingAsSuperAdmin();
    AppSetting::set(AppSetting::BANK_MODULE_ENABLED, true);
    $account = BankAccount::factory()->create([
        'code' => BankAccount::FIO_BANK,
        'account_credentials' => ['token' => 'puvodni-token'],
    ]);

    Livewire::test(EditBankAccount::class, ['record' => $account->getRouteKey()])
        ->fillForm(['name' => 'Nový název'])
        ->call('save')
        ->assertHasNoFormErrors();

    expect($account->refresh()->account_credentials)->toBe(['token' => 'puvodni-token'])
        ->and($account->name)->toBe('Nový název');
});

it('overwrites stored credentials when a new value is entered on edit', function (): void {
    actingAsSuperAdmin();
    AppSetting::set(AppSetting::BANK_MODULE_ENABLED, true);
    $account = BankAccount::factory()->create([
        'code' => BankAccount::FIO_BANK,
        'account_credentials' => ['token' => 'puvodni-token'],
    ]);

    Livewire::test(EditBankAccount::class, ['record' => $account->getRouteKey()])
        ->fillForm(['credentials.token' => 'novy-token'])
        ->call('save')
        ->assertHasNoFormErrors();

    expect($account->refresh()->account_credentials)->toBe(['token' => 'novy-token']);
});
