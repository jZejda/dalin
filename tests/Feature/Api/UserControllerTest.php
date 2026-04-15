<?php

declare(strict_types=1);

use App\Enums\EntryStatus;
use App\Enums\UserCreditStatus;
use App\Enums\UserCreditType;
use App\Enums\UserParamType;
use App\Models\SportEvent;
use App\Models\User;
use App\Models\UserCredit;
use App\Models\UserEntry;
use App\Models\UserRaceProfile;
use Illuminate\Support\Facades\DB;

beforeEach(function (): void {
    $this->user = User::factory()->create(['active' => true]);

    $this->raceProfile = UserRaceProfile::create([
        'user_id'    => $this->user->id,
        'first_name' => 'Jan',
        'last_name'  => 'Novák',
        'reg_number' => 'ABB0001',
        'gender'     => 'M',
        'active'     => true,
    ]);
});

// ---------------------------------------------------------------------------
// GET /api/user/race-profiles
// ---------------------------------------------------------------------------

test('race-profiles: unauthenticated request is rejected with 401', function (): void {
    $this->getJson('/api/user/race-profiles')
        ->assertUnauthorized();
});

test('race-profiles: authenticated user gets HTTP 200', function (): void {
    $this->actingAs($this->user)
        ->getJson('/api/user/race-profiles')
        ->assertOk();
});

test('race-profiles: response contains expected keys for each profile', function (): void {
    $response = $this->actingAs($this->user)
        ->getJson('/api/user/race-profiles')
        ->assertOk();

    $profile = $response->json('data.0');

    expect($profile)
        ->toHaveKeys([
            'id',
            'reg_number',
            'first_name',
            'last_name',
            'full_name',
            'email',
            'phone',
            'gender',
            'active',
            'active_until',
            'created_at',
            'updated_at',
        ]);
});

test('race-profiles: returns only active profiles by default', function (): void {
    UserRaceProfile::create([
        'user_id'    => $this->user->id,
        'first_name' => 'Neaktivní',
        'last_name'  => 'Profil',
        'reg_number' => 'ABB0002',
        'gender'     => 'F',
        'active'     => false,
    ]);

    $response = $this->actingAs($this->user)
        ->getJson('/api/user/race-profiles')
        ->assertOk();

    $profiles = $response->json('data');

    expect($profiles)->toHaveCount(1)
        ->and($profiles[0]['active'])->toBeTrue();
});

test('race-profiles: ?all=true returns both active and inactive profiles', function (): void {
    UserRaceProfile::create([
        'user_id'    => $this->user->id,
        'first_name' => 'Neaktivní',
        'last_name'  => 'Profil',
        'reg_number' => 'ABB0002',
        'gender'     => 'F',
        'active'     => false,
    ]);

    $response = $this->actingAs($this->user)
        ->getJson('/api/user/race-profiles?all=true')
        ->assertOk();

    expect($response->json('data'))->toHaveCount(2);
});

test('race-profiles: ?all=false behaves the same as default (active only)', function (): void {
    UserRaceProfile::create([
        'user_id'    => $this->user->id,
        'first_name' => 'Neaktivní',
        'last_name'  => 'Profil',
        'reg_number' => 'ABB0003',
        'gender'     => 'F',
        'active'     => false,
    ]);

    $response = $this->actingAs($this->user)
        ->getJson('/api/user/race-profiles?all=false')
        ->assertOk();

    expect($response->json('data'))->toHaveCount(1);
});

test('race-profiles: user sees only their own profiles, not other users profiles', function (): void {
    $otherUser = User::factory()->create();
    UserRaceProfile::create([
        'user_id'    => $otherUser->id,
        'first_name' => 'Cizí',
        'last_name'  => 'Uživatel',
        'reg_number' => 'XYZ9999',
        'gender'     => 'M',
        'active'     => true,
    ]);

    $response = $this->actingAs($this->user)
        ->getJson('/api/user/race-profiles')
        ->assertOk();

    expect($response->json('data'))->toHaveCount(1)
        ->and($response->json('data.0.reg_number'))->toBe('ABB0001');
});

test('race-profiles: user with no profiles gets empty data array', function (): void {
    $emptyUser = User::factory()->create();

    $response = $this->actingAs($emptyUser)
        ->getJson('/api/user/race-profiles')
        ->assertOk();

    expect($response->json('data'))->toBeEmpty();
});

test('race-profiles: full_name is composed from reg_number first_name and last_name', function (): void {
    $response = $this->actingAs($this->user)
        ->getJson('/api/user/race-profiles')
        ->assertOk();

    expect($response->json('data.0.full_name'))->toBe('ABB0001 - Jan Novák');
});

// ---------------------------------------------------------------------------
// GET /api/user/entry
// ---------------------------------------------------------------------------

test('entry: unauthenticated request is rejected with 401', function (): void {
    $this->getJson('/api/user/entry')
        ->assertUnauthorized();
});

test('entry: authenticated user gets HTTP 200', function (): void {
    $this->actingAs($this->user)
        ->getJson('/api/user/entry')
        ->assertOk();
});

test('entry: response is paginated using simplePaginate', function (): void {
    $this->actingAs($this->user)
        ->getJson('/api/user/entry')
        ->assertOk()
        ->assertJsonStructure([
            'data',
            'links' => ['first', 'next', 'prev'],
        ]);
});

test('entry: user with no entries gets empty data array', function (): void {
    $response = $this->actingAs($this->user)
        ->getJson('/api/user/entry')
        ->assertOk();

    expect($response->json('data'))->toBeEmpty();
});

test('entry: returns entries linked to user race profiles', function (): void {
    DB::statement('SET FOREIGN_KEY_CHECKS=0');

    $sportEvent = SportEvent::factory()->create([
        'date' => now()->addDays(10)->toDateString(),
        'discipline_id' => null,
    ]);

    $entry = UserEntry::create([
        'sport_event_id'      => $sportEvent->id,
        'class_definition_id' => 999,
        'user_race_profile_id' => $this->raceProfile->id,
        'class_name'          => 'H21',
        'entry_status'        => EntryStatus::Create->value,
        'rent_si'             => false,
        'entry_created'       => now(),
    ]);

    DB::statement('SET FOREIGN_KEY_CHECKS=1');

    $response = $this->actingAs($this->user)
        ->getJson('/api/user/entry')
        ->assertOk();

    expect($response->json('data'))->toHaveCount(1)
        ->and($response->json('data.0.id'))->toBe($entry->id)
        ->and($response->json('data.0.class_name'))->toBe('H21')
        ->and($response->json('data.0.entry_status'))->toBe(EntryStatus::Create->value);
});

test('entry: response item contains expected keys', function (): void {
    DB::statement('SET FOREIGN_KEY_CHECKS=0');

    $sportEvent = SportEvent::factory()->create([
        'date' => now()->addDays(5)->toDateString(),
        'discipline_id' => null,
    ]);

    UserEntry::create([
        'sport_event_id'       => $sportEvent->id,
        'class_definition_id'  => 999,
        'user_race_profile_id' => $this->raceProfile->id,
        'class_name'           => 'D21',
        'entry_status'         => EntryStatus::Create->value,
        'rent_si'              => false,
        'entry_created'        => now(),
    ]);

    DB::statement('SET FOREIGN_KEY_CHECKS=1');

    $item = $this->actingAs($this->user)
        ->getJson('/api/user/entry')
        ->assertOk()
        ->json('data.0');

    expect($item)
        ->toHaveKeys([
            'id',
            'sport_event',
            'race_profile',
            'class_name',
            'requested_start',
            'rent_si',
            'entry_stages',
            'entry_status',
            'entry_created',
            'created_at',
            'updated_at',
        ]);
});

test('entry: ?from filter excludes events before given date', function (): void {
    DB::statement('SET FOREIGN_KEY_CHECKS=0');

    $pastEvent  = SportEvent::factory()->create(['date' => now()->subDays(5)->toDateString(), 'discipline_id' => null]);
    $futureEvent = SportEvent::factory()->create(['date' => now()->addDays(10)->toDateString(), 'discipline_id' => null]);

    UserEntry::create([
        'sport_event_id'       => $pastEvent->id,
        'class_definition_id'  => 999,
        'user_race_profile_id' => $this->raceProfile->id,
        'entry_status'         => EntryStatus::Create->value,
        'rent_si'              => false,
        'entry_created'        => now(),
    ]);

    UserEntry::create([
        'sport_event_id'       => $futureEvent->id,
        'class_definition_id'  => 999,
        'user_race_profile_id' => $this->raceProfile->id,
        'entry_status'         => EntryStatus::Create->value,
        'rent_si'              => false,
        'entry_created'        => now(),
    ]);

    DB::statement('SET FOREIGN_KEY_CHECKS=1');

    $from = now()->toDateString();

    $response = $this->actingAs($this->user)
        ->getJson("/api/user/entry?from={$from}")
        ->assertOk();

    expect($response->json('data'))->toHaveCount(1)
        ->and($response->json('data.0.sport_event.id'))->toBe($futureEvent->id);
});

test('entry: ?to filter excludes events after given date', function (): void {
    DB::statement('SET FOREIGN_KEY_CHECKS=0');

    $nearEvent = SportEvent::factory()->create(['date' => now()->addDays(3)->toDateString(), 'discipline_id' => null]);
    $farEvent  = SportEvent::factory()->create(['date' => now()->addDays(60)->toDateString(), 'discipline_id' => null]);

    UserEntry::create([
        'sport_event_id'       => $nearEvent->id,
        'class_definition_id'  => 999,
        'user_race_profile_id' => $this->raceProfile->id,
        'entry_status'         => EntryStatus::Create->value,
        'rent_si'              => false,
        'entry_created'        => now(),
    ]);

    UserEntry::create([
        'sport_event_id'       => $farEvent->id,
        'class_definition_id'  => 999,
        'user_race_profile_id' => $this->raceProfile->id,
        'entry_status'         => EntryStatus::Create->value,
        'rent_si'              => false,
        'entry_created'        => now(),
    ]);

    DB::statement('SET FOREIGN_KEY_CHECKS=1');

    $to = now()->addDays(10)->toDateString();

    $response = $this->actingAs($this->user)
        ->getJson("/api/user/entry?from=2000-01-01&to={$to}")
        ->assertOk();

    expect($response->json('data'))->toHaveCount(1)
        ->and($response->json('data.0.sport_event.id'))->toBe($nearEvent->id);
});

test('entry: ?per_page parameter controls page size', function (): void {
    DB::statement('SET FOREIGN_KEY_CHECKS=0');

    for ($i = 0; $i < 5; $i++) {
        $event = SportEvent::factory()->create(['date' => now()->addDays($i + 1)->toDateString(), 'discipline_id' => null]);
        UserEntry::create([
            'sport_event_id'       => $event->id,
            'class_definition_id'  => 999,
            'user_race_profile_id' => $this->raceProfile->id,
            'entry_status'         => EntryStatus::Create->value,
            'rent_si'              => false,
            'entry_created'        => now()->subMinutes($i),
        ]);
    }

    DB::statement('SET FOREIGN_KEY_CHECKS=1');

    $response = $this->actingAs($this->user)
        ->getJson('/api/user/entry?per_page=2')
        ->assertOk();

    expect($response->json('data'))->toHaveCount(2);
});

test('entry: does not return entries belonging to other user', function (): void {
    DB::statement('SET FOREIGN_KEY_CHECKS=0');

    $otherUser = User::factory()->create();
    $otherProfile = UserRaceProfile::create([
        'user_id'    => $otherUser->id,
        'first_name' => 'Cizí',
        'last_name'  => 'Závodník',
        'reg_number' => 'CCC0001',
        'gender'     => 'M',
        'active'     => true,
    ]);

    $event = SportEvent::factory()->create(['date' => now()->addDays(5)->toDateString(), 'discipline_id' => null]);

    UserEntry::create([
        'sport_event_id'       => $event->id,
        'class_definition_id'  => 999,
        'user_race_profile_id' => $otherProfile->id,
        'entry_status'         => EntryStatus::Create->value,
        'rent_si'              => false,
        'entry_created'        => now(),
    ]);

    DB::statement('SET FOREIGN_KEY_CHECKS=1');

    $response = $this->actingAs($this->user)
        ->getJson('/api/user/entry')
        ->assertOk();

    expect($response->json('data'))->toBeEmpty();
});

test('entry: default filter shows only entries from today onwards', function (): void {
    DB::statement('SET FOREIGN_KEY_CHECKS=0');

    $pastEvent   = SportEvent::factory()->create(['date' => now()->subDays(10)->toDateString(), 'discipline_id' => null]);
    $futureEvent = SportEvent::factory()->create(['date' => now()->addDays(10)->toDateString(), 'discipline_id' => null]);

    UserEntry::create([
        'sport_event_id'       => $pastEvent->id,
        'class_definition_id'  => 999,
        'user_race_profile_id' => $this->raceProfile->id,
        'entry_status'         => EntryStatus::Create->value,
        'rent_si'              => false,
        'entry_created'        => now(),
    ]);

    UserEntry::create([
        'sport_event_id'       => $futureEvent->id,
        'class_definition_id'  => 999,
        'user_race_profile_id' => $this->raceProfile->id,
        'entry_status'         => EntryStatus::Create->value,
        'rent_si'              => false,
        'entry_created'        => now(),
    ]);

    DB::statement('SET FOREIGN_KEY_CHECKS=1');

    $response = $this->actingAs($this->user)
        ->getJson('/api/user/entry')
        ->assertOk();

    // Without 'from' param, defaults to today — past event should be excluded
    expect($response->json('data'))->toHaveCount(1)
        ->and($response->json('data.0.sport_event.id'))->toBe($futureEvent->id);
});

// ---------------------------------------------------------------------------
// GET /api/user/credit-balance
// ---------------------------------------------------------------------------

test('credit-balance: unauthenticated request is rejected with 401', function (): void {
    $this->getJson('/api/user/credit-balance')
        ->assertUnauthorized();
});

test('credit-balance: authenticated user gets HTTP 200', function (): void {
    $this->actingAs($this->user)
        ->getJson('/api/user/credit-balance')
        ->assertOk();
});

test('credit-balance: response contains amount, currency and updated_at keys', function (): void {
    $response = $this->actingAs($this->user)
        ->getJson('/api/user/credit-balance')
        ->assertOk();

    expect($response->json('data'))
        ->toHaveKeys(['amount', 'currency', 'updated_at']);
});

test('credit-balance: currency is CZK', function (): void {
    $response = $this->actingAs($this->user)
        ->getJson('/api/user/credit-balance')
        ->assertOk();

    expect($response->json('data.currency'))->toBe(UserCredit::CURRENCY_CZK);
});

test('credit-balance: amount is zero when user has no credits', function (): void {
    $response = $this->actingAs($this->user)
        ->getJson('/api/user/credit-balance')
        ->assertOk();

    expect($response->json('data.amount'))->toEqual(0);
});

test('credit-balance: amount reflects sum of user credits', function (): void {
    UserCredit::create([
        'user_id'     => $this->user->id,
        'amount'      => 500.0,
        'currency'    => UserCredit::CURRENCY_CZK,
        'source'      => UserCredit::SOURCE_USER,
        'status'      => UserCreditStatus::Open,
        'credit_type' => UserCreditType::InitialDeposit,
    ]);

    UserCredit::create([
        'user_id'     => $this->user->id,
        'amount'      => -150.0,
        'currency'    => UserCredit::CURRENCY_CZK,
        'source'      => UserCredit::SOURCE_USER,
        'status'      => UserCreditStatus::Open,
        'credit_type' => UserCreditType::InitialDeposit,
    ]);

    $response = $this->actingAs($this->user)
        ->getJson('/api/user/credit-balance')
        ->assertOk();

    expect($response->json('data.amount'))->toEqual(350);
});

test('credit-balance: amount is returned as float', function (): void {
    $response = $this->actingAs($this->user)
        ->getJson('/api/user/credit-balance')
        ->assertOk();

    expect($response->json('data.amount'))->toBeNumeric();
});

test('credit-balance: cached balance in UserParam is used when available', function (): void {
    // Pre-set param cache so the DB sum branch is skipped
    $this->user->setParam(UserParamType::UserActualBalance, 1234.56);

    $response = $this->actingAs($this->user)
        ->getJson('/api/user/credit-balance')
        ->assertOk();

    expect($response->json('data.amount'))->toBe(1234.56);
});

test('credit-balance: does not show balance of another user', function (): void {
    $otherUser = User::factory()->create();

    UserCredit::create([
        'user_id'     => $otherUser->id,
        'amount'      => 9999.0,
        'currency'    => UserCredit::CURRENCY_CZK,
        'source'      => UserCredit::SOURCE_USER,
        'status'      => UserCreditStatus::Open,
        'credit_type' => UserCreditType::InitialDeposit,
    ]);

    $response = $this->actingAs($this->user)
        ->getJson('/api/user/credit-balance')
        ->assertOk();

    expect($response->json('data.amount'))->toEqual(0);
});
