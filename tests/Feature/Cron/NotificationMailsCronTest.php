<?php

declare(strict_types=1);

use App\Enums\ContentFormat;
use App\Enums\EntryStatus;
use App\Enums\PostStatus;
use App\Http\Controllers\Cron\Jobs\ReportEmailEventWeeklyEndsBySport;
use App\Http\Controllers\Cron\Jobs\ReportEmailPreRaceSummary;
use App\Jobs\SendNewPostsEmailJob;
use App\Jobs\SendSportEventEntryEndingEmailJob;
use App\Mail\EventEntryEnds;
use App\Mail\EventWeeklyEndsBySport;
use App\Mail\NewPosts;
use App\Mail\PreRaceSummaryMail;
use App\Models\Post;
use App\Models\SportClassDefinition;
use App\Models\SportEvent;
use App\Models\User;
use App\Models\UserEntry;
use App\Models\UserRaceProfile;
use App\Models\UserSetting;
use Illuminate\Support\Facades\Mail;

beforeEach(function (): void {
    // The default store is "file" (config reads CACHE_DRIVER), which would leak dedup keys between runs
    config(['cache.default' => 'array']);
    Mail::fake();
});

/**
 * @param array<string, mixed> $options
 */
function notificationUser(array $options, bool $active = true): User
{
    $user = User::factory()->create(['active' => $active]);

    // Other setting rows exist for most users and must not shadow the mail one
    UserSetting::query()->create(['user_id' => $user->id, 'type' => UserSetting::USER_EVENT_FILTERS_NAME, 'options' => ['event_filters' => []]]);
    UserSetting::query()->create(['user_id' => $user->id, 'type' => 'usersAllowSignForRace', 'options' => ['users_allow_sign_up_for_race' => []]]);
    UserSetting::query()->create(['user_id' => $user->id, 'type' => UserSetting::TYPE_MAIL, 'options' => $options]);

    return $user;
}

function notificationPost(string $title, PostStatus $status, User $author): Post
{
    return Post::query()->create([
        'title' => $title,
        'content' => 'Obsah novinky',
        'content_mode' => ContentFormat::Markdown,
        'private' => $status,
        'user_id' => $author->id,
    ]);
}

function preRaceEntry(SportEvent $event, User $user): UserEntry
{
    $definition = SportClassDefinition::query()->firstOrCreate(
        ['sport_id' => $event->sport_id, 'name' => 'H21'],
        ['age_from' => 21, 'age_to' => 34, 'gender' => 'M'],
    );
    $profile = UserRaceProfile::factory()->create(['user_id' => $user->id]);

    return UserEntry::query()->create([
        'sport_event_id' => $event->id,
        'class_definition_id' => $definition->id,
        'user_race_profile_id' => $profile->id,
        'class_name' => 'H21',
        'entry_status' => EntryStatus::Create->value,
        'rent_si' => false,
        'entry_created' => now(),
    ]);
}

it('sends each post in exactly one news digest, even when the job runs repeatedly', function (): void {
    $user = notificationUser(['news' => ['0', '1'], 'news_time_trigger' => 8]);

    $this->travelTo(now()->subDay()->setTime(20, 15));
    notificationPost('Souboj v H45 rozhodlo 58 sekund', PostStatus::Public, $user);

    // Trigger hour, run twice (the scheduler URL used to fire at :00 and :30)
    $this->travelTo(now()->addDay()->setTime(8, 0));
    (new SendNewPostsEmailJob())->handle();
    $this->travelTo(now()->setTime(8, 30));
    (new SendNewPostsEmailJob())->handle();

    // Next day the same post is no longer in the window
    $this->travelTo(now()->addDay()->setTime(8, 0));
    (new SendNewPostsEmailJob())->handle();

    Mail::assertQueuedCount(1);
    Mail::assertQueued(NewPosts::class, fn (NewPosts $mail): bool => $mail->hasTo($user->email));
});

it('matches a single-digit news trigger hour and respects the public/internal choice', function (): void {
    $publicOnly = notificationUser(['news' => ['0'], 'news_time_trigger' => 7]);
    $legacyPadded = notificationUser(['news' => ['1'], 'news_time_trigger' => '07']);

    $this->travelTo(now()->setTime(6, 10));
    notificationPost('Interní: sraz na soustředění', PostStatus::Private, $publicOnly);

    $this->travelTo(now()->setTime(7, 0));
    (new SendNewPostsEmailJob())->handle();

    Mail::assertQueuedCount(1);
    Mail::assertQueued(NewPosts::class, fn (NewPosts $mail): bool => $mail->hasTo($legacyPadded->email));
});

it('skips inactive users in the news digest', function (): void {
    notificationUser(['news' => ['0', '1'], 'news_time_trigger' => 9], active: false);

    $this->travelTo(now()->setTime(8, 10));
    notificationPost('Výsledky oblastního žebříčku', PostStatus::Public, User::factory()->create());

    $this->travelTo(now()->setTime(9, 0));
    (new SendNewPostsEmailJob())->handle();

    Mail::assertNothingQueued();
});

it('sends the pre-race summary from the mail setting row, once per trigger hour', function (): void {
    $this->travelTo(now()->setTime(22, 42));
    $user = notificationUser([
        'pre_race_summary_enabled' => true,
        'pre_race_summary_days_before' => 1,
        'pre_race_summary_time_trigger' => 22,
    ]);
    $event = SportEvent::factory()->create(['name' => 'Oblastní žebříček Testov', 'date' => now()->addDay()->toDateString()]);
    preRaceEntry($event, $user);

    (new ReportEmailPreRaceSummary())->run();
    (new ReportEmailPreRaceSummary())->run();

    Mail::assertSentCount(1);
    Mail::assertSent(PreRaceSummaryMail::class);
});

it('does not send the pre-race summary when it is disabled', function (): void {
    $this->travelTo(now()->setTime(17, 42));
    $user = notificationUser(['pre_race_summary_enabled' => false, 'pre_race_summary_time_trigger' => 17]);
    $event = SportEvent::factory()->create(['date' => now()->addDay()->toDateString()]);
    preRaceEntry($event, $user);

    (new ReportEmailPreRaceSummary())->run();

    Mail::assertNothingSent();
});

it('sends the weekly report for any selected sport, not only the first one', function (): void {
    $event = SportEvent::factory()->create(['entry_date_1' => now()->addDays(3)]);
    $user = notificationUser(['week_report_by_sport' => [(string) $event->sport_id]]);

    (new ReportEmailEventWeeklyEndsBySport())->run();

    Mail::assertQueued(EventWeeklyEndsBySport::class, fn (EventWeeklyEndsBySport $mail): bool => $mail->hasTo($user->email));
});

it('matches a single-digit entry deadline trigger hour', function (): void {
    $this->travelTo(now()->setTime(8, 0));
    $event = SportEvent::factory()->create(['entry_date_1' => now()->addDays(3)->setTime(12, 0)]);
    $user = notificationUser([
        'sport' => [(string) $event->sport_id],
        'sport_time_trigger' => 8,
        'days_before_event_entry_ends' => 3,
    ]);

    (new SendSportEventEntryEndingEmailJob())->handle();

    Mail::assertQueued(EventEntryEnds::class, fn (EventEntryEnds $mail): bool => $mail->hasTo($user->email));
});
