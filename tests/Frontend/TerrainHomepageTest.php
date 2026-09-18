<?php

declare(strict_types=1);

use App\Enums\ContentFormat;
use App\Enums\PostStatus;
use App\Enums\SportEventType;
use App\Livewire\Frontend\EventList;
use App\Livewire\Frontend\PostCards;
use App\Models\Post;
use App\Models\SportEvent;
use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Support\Facades\Blade;
use Carbon\Carbon;
use Livewire\Livewire;

uses(DatabaseTransactions::class);

it('shows the terrain homepage with the public map, theme controls and configured club', function () {
    $this->withoutVite();
    config(['site-config.club.abbr' => 'TEST', 'site-config.club.full_name' => 'Testovací klub']);

    $this->get('/')
        ->assertOk()
        ->assertSee('Testovací klub')
        ->assertSee('Orientace')
        ->assertSee('data-terrain-theme', false)
        ->assertSee('aria-controls="terrain-navigation"', false)
        ->assertSee('Mapa nadcházejících akcí')
        ->assertSee('https://mapy.orientacnisporty.cz/cs/clubs/test', false)
        ->assertDontSee('7-jihomoravska-liga-2026-novinky');
});

it('keeps public news filtering and shows a plain editorial preview with a detail link', function () {
    $user = User::factory()->create();
    $public = Post::create([
        'user_id' => $user->id,
        'title' => 'Veřejná Terrain novinka',
        'content' => 'Obsah',
        'editorial' => '<p>Text s <a href="/stranka/o-klubu">odkazem</a>.</p>',
        'content_mode' => ContentFormat::Html,
        'private' => PostStatus::Public,
    ]);
    Post::create([
        'user_id' => $user->id,
        'title' => 'Interní Terrain novinka',
        'content' => 'Obsah jen pro členy',
        'content_mode' => ContentFormat::Markdown,
        'private' => PostStatus::Private,
    ]);

    Livewire::test(PostCards::class, ['terrain' => true])
        ->assertSee('Veřejná Terrain novinka')
        ->assertSee(url('/novinka', $public->id))
        ->assertSee('Text s odkazem.')
        ->assertDontSee('Interní Terrain novinka');

    Livewire::test(PostCards::class)->assertViewIs('livewire.frontend.post-cards');
});

it('renders real event details and highlights the configured organizing club', function () {
    config(['site-config.club.abbr' => 'PBM']);
    $event = new SportEvent([
        'name' => 'Podzimní testovací závod',
        'alt_name' => 'Oblastní žebříček',
        'date' => '2026-10-15',
        'event_type' => SportEventType::Race,
        'organization' => ['PBM'],
        'region' => ['JM'],
        'place' => 'Brno',
        'oris_id' => 12345,
    ]);
    $event->id = 322;
    $html = view('livewire.frontend.terrain.event-list', ['events' => collect([$event])])->render();

    expect($html)->toContain(route('sport-event.show', 322), 'Podzimní testovací závod', 'Oblastní žebříček', 'Brno', 'ORIS 12345', 'border-terrain-accent');
});

it('provides useful empty states for news and upcoming events', function () {
    expect(view('livewire.frontend.terrain.post-cards', ['posts' => collect()])->render())
        ->toContain('Zatím tu nejsou žádné novinky');
    expect(view('livewire.frontend.terrain.event-list', ['events' => collect()])->render())
        ->toContain('Právě nejsou naplánované žádné nadcházející akce');
});

it('preserves the existing event presentation outside terrain pages', function () {
    Livewire::test(EventList::class)->assertViewIs('livewire.frontend.event-list');
});

it('renders two-digit days with a dot and uppercase month labels', function (string $date, string $day, string $month) {
    app()->setLocale('cs');
    $html = Blade::render('<x-ui.event-date :date="$date" />', ['date' => Carbon::parse($date)]);

    expect($html)->toContain('size-[4.5rem]', 'bg-terrain-accent text-center text-terrain-on-accent', '>' . $day . '</span>', '>' . $month . '</span>');
})->with([
    ['2026-02-01', '01.', 'UNO'],
    ['2026-05-09', '09.', 'KVÉ'],
    ['2026-09-23', '23.', 'ZÁR'],
]);

it('replaces coordinates with a single place row and a lucide map pin', function () {
    $event = new SportEvent([
        'name' => 'Terrain závod', 'date' => '2026-05-09', 'event_type' => SportEventType::Race,
        'place' => 'Lovčičky', 'gps_lat' => '49.06956', 'gps_lon' => '16.85633', 'organization' => ['OTHER'],
    ]);
    $event->id = 322;
    $html = view('livewire.frontend.terrain.event-list', ['events' => collect([$event])])->render();

    expect($html)->toContain('lucide-map-pin', 'bg-terrain-accent text-center text-terrain-on-accent')
        ->not->toContain('49.06956', '16.85633');
    expect(substr_count(strip_tags($html), 'Lovčičky'))->toBe(1);

    $event->place = null;
    expect(view('livewire.frontend.terrain.event-list', ['events' => collect([$event])])->render())
        ->not->toContain('lucide-map-pin');
});

it('shortens long places to 30 characters while retaining the full name in the tooltip', function (string $place, string $display) {
    $event = new SportEvent(['name' => 'Závod', 'date' => '2026-10-01', 'event_type' => SportEventType::Race, 'place' => $place]);
    $event->id = 322;
    $html = view('livewire.frontend.terrain.event-list', ['events' => collect([$event])])->render();

    expect($html)->toContain('class="min-w-0 truncate"', 'title="' . $place . '">' . $display . '</span>');
})->with([
    ['Brno', 'Brno'],
    [str_repeat('Ž', 30), str_repeat('Ž', 30)],
    [str_repeat('Ž', 31), str_repeat('Ž', 29) . '…'],
]);
