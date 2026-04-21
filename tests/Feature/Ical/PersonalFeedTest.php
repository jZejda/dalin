<?php

declare(strict_types=1);

use App\Enums\EntryStatus;
use App\Enums\SportEventType;
use App\Models\SportClassDefinition;
use App\Models\SportEvent;
use App\Models\SportList;
use App\Models\User;
use App\Models\UserEntry;
use App\Models\UserRaceProfile;
use App\Services\CalendarTokenService;

beforeEach(function () {
    $this->service = new CalendarTokenService();
    $this->user = User::factory()->create(['active' => true]);
    $this->token = $this->service->generate($this->user);
});

test('valid token returns 200 with ical content for race feed', function () {
    $response = $this->get("/api/feed/kalendar/zavody/me/{$this->token}");

    $response->assertStatus(200);
    $response->assertHeader('Content-Type', 'text/calendar; charset=utf-8');
    expect($response->getContent())->toContain('BEGIN:VCALENDAR');
});

test('invalid token returns 404 for race feed', function () {
    $response = $this->get('/api/feed/kalendar/zavody/me/invalid-token-that-does-not-exist');

    $response->assertStatus(404);
});

test('revoked token returns 404 for race feed', function () {
    $this->service->revoke($this->user);

    $response = $this->get("/api/feed/kalendar/zavody/me/{$this->token}");

    $response->assertStatus(404);
});

test('race feed has correct content-disposition header', function () {
    $response = $this->get("/api/feed/kalendar/zavody/me/{$this->token}");

    $response->assertHeader('Content-Disposition', 'attachment; filename="abm-moje-zavody.ics"');
});

test('race feed has correct cache-control header', function () {
    $response = $this->get("/api/feed/kalendar/zavody/me/{$this->token}");

    $cacheControl = $response->headers->get('Cache-Control');
    expect($cacheControl)
        ->toContain('no-cache')
        ->toContain('no-store')
        ->toContain('must-revalidate');
});

test('valid token returns 200 with ical content for training feed', function () {
    $response = $this->get("/api/feed/kalendar/treninky/me/{$this->token}");

    $response->assertStatus(200);
    $response->assertHeader('Content-Type', 'text/calendar; charset=utf-8');
    expect($response->getContent())->toContain('BEGIN:VCALENDAR');
});

test('invalid token returns 404 for training feed', function () {
    $response = $this->get('/api/feed/kalendar/treninky/me/invalid-token-that-does-not-exist');

    $response->assertStatus(404);
});

test('revoked token returns 404 for training feed', function () {
    $this->service->revoke($this->user);

    $response = $this->get("/api/feed/kalendar/treninky/me/{$this->token}");

    $response->assertStatus(404);
});

test('training feed has correct content-disposition header', function () {
    $response = $this->get("/api/feed/kalendar/treninky/me/{$this->token}");

    $response->assertHeader('Content-Disposition', 'attachment; filename="abm-moje-treninky.ics"');
});

test('training feed has correct cache-control header', function () {
    $response = $this->get("/api/feed/kalendar/treninky/me/{$this->token}");

    $cacheControl = $response->headers->get('Cache-Control');
    expect($cacheControl)
        ->toContain('no-cache')
        ->toContain('no-store')
        ->toContain('must-revalidate');
});

test('empty calendar returns valid ics with no events', function () {
    $response = $this->get("/api/feed/kalendar/zavody/me/{$this->token}");

    $response->assertStatus(200);
    $content = $response->getContent();
    expect($content)
        ->toContain('BEGIN:VCALENDAR')
        ->toContain('END:VCALENDAR')
        ->not->toContain('BEGIN:VEVENT');
});

test('personal feed only contains events for the token owner', function () {
    $sport = SportList::create(['short_name' => 'OB']);

    $sportEvent = SportEvent::factory()->create([
        'event_type' => SportEventType::Race,
        'cancelled' => false,
        'date' => now()->addMonth(),
        'sport_id' => $sport->id,
        'discipline_id' => null,
    ]);

    $classDefinition = SportClassDefinition::create([
        'sport_id' => $sportEvent->sport_id,
        'age_from' => 18,
        'age_to' => 99,
        'name' => 'H21',
    ]);

    $profileA = UserRaceProfile::create([
        'user_id' => $this->user->id,
        'first_name' => 'User',
        'last_name' => 'A',
        'reg_number' => 'ABM0001',
        'gender' => 'M',
    ]);

    UserEntry::create([
        'sport_event_id' => $sportEvent->id,
        'class_definition_id' => $classDefinition->id,
        'user_race_profile_id' => $profileA->id,
        'entry_status' => EntryStatus::Create,
    ]);

    $userB = User::factory()->create(['active' => true]);
    $tokenB = $this->service->generate($userB);

    $sportEventB = SportEvent::factory()->create([
        'event_type' => SportEventType::Race,
        'cancelled' => false,
        'date' => now()->addMonths(2),
        'name' => 'Event Only For User B',
        'sport_id' => $sport->id,
        'discipline_id' => null,
    ]);

    $profileB = UserRaceProfile::create([
        'user_id' => $userB->id,
        'first_name' => 'User',
        'last_name' => 'B',
        'reg_number' => 'ABM0002',
        'gender' => 'F',
    ]);

    UserEntry::create([
        'sport_event_id' => $sportEventB->id,
        'class_definition_id' => $classDefinition->id,
        'user_race_profile_id' => $profileB->id,
        'entry_status' => EntryStatus::Create,
    ]);

    $responseA = $this->get("/api/feed/kalendar/zavody/me/{$this->token}");
    $contentA = $responseA->getContent();

    expect($contentA)
        ->toContain($sportEvent->name)
        ->not->toContain('Event Only For User B');

    $responseB = $this->get("/api/feed/kalendar/zavody/me/{$tokenB}");
    $contentB = $responseB->getContent();

    expect($contentB)
        ->toContain('Event Only For User B')
        ->not->toContain($sportEvent->name);
});

test('race feed includes events where user has entries', function () {
    $sport = SportList::create(['short_name' => 'OB']);
    $sportEvent = SportEvent::factory()->create([
        'event_type' => SportEventType::Race,
        'cancelled' => false,
        'date' => now()->addMonth(),
        'name' => 'Test Race Event',
        'sport_id' => $sport->id,
        'discipline_id' => null,
    ]);

    $classDefinition = SportClassDefinition::create([
        'sport_id' => $sportEvent->sport_id,
        'age_from' => 18,
        'age_to' => 99,
        'name' => 'H21',
    ]);

    $profile = UserRaceProfile::create([
        'user_id' => $this->user->id,
        'first_name' => 'Test',
        'last_name' => 'Runner',
        'reg_number' => 'ABM9999',
        'gender' => 'M',
    ]);

    UserEntry::create([
        'sport_event_id' => $sportEvent->id,
        'class_definition_id' => $classDefinition->id,
        'user_race_profile_id' => $profile->id,
        'entry_status' => EntryStatus::Create,
    ]);

    $response = $this->get("/api/feed/kalendar/zavody/me/{$this->token}");

    expect($response->getContent())
        ->toContain('BEGIN:VEVENT')
        ->toContain('Test Race Event');
});
