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
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Blade;
use Illuminate\Support\Facades\Storage;
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
        ->assertSee('data-terrain-theme-toggle', false)
        ->assertSee(__('frontend.theme.switch_to_light'), false)
        ->assertSee('aria-controls="terrain-navigation"', false)
        ->assertSee(__('frontend.dalin_promo.heading'))
        ->assertSee('images/dalin/races-dark.webp', false)
        ->assertSee('href="https://dalin.cz"', false)
        ->assertSee(route('posts.index'), false)
        ->assertSee(__('content.post.public.more_link'))
        ->assertSee('Mapa nadcházejících akcí')
        ->assertSee('https://mapy.orientacnisporty.cz/cs/clubs/test', false)
        ->assertDontSee('7-jihomoravska-liga-2026-novinky');
});

it('keeps public news filtering and shows a cover image or a placeholder with a detail link', function () {
    Storage::fake('public');
    $user = User::factory()->create();
    $public = Post::create([
        'user_id' => $user->id,
        'title' => 'Veřejná Terrain novinka',
        'content' => 'Obsah',
        'editorial' => '<p>Text s <a href="/stranka/o-klubu">odkazem</a>.</p>',
        'content_mode' => ContentFormat::Html,
        'private' => PostStatus::Public,
    ]);
    $withCover = Post::create([
        'user_id' => $user->id,
        'title' => 'Novinka s obrázkem',
        'content' => 'Obsah',
        'content_mode' => ContentFormat::Html,
        'private' => PostStatus::Public,
    ]);
    $withCover->addMedia(UploadedFile::fake()->image('cover.jpg', 1400, 400))
        ->toMediaCollection(Post::MEDIA_COLLECTION_COVER);
    Post::create([
        'user_id' => $user->id,
        'title' => 'Interní Terrain novinka',
        'content' => 'Obsah jen pro členy',
        'content_mode' => ContentFormat::Markdown,
        'private' => PostStatus::Private,
    ]);

    Livewire::test(PostCards::class, ['terrain' => true])
        ->assertSee('Veřejná Terrain novinka')
        ->assertSeeInOrder([$user->name, $public->created_at->format('d. m. Y')])
        ->assertSee(url('/novinka', $public->id))
        ->assertSee('terrain-cover-placeholder', false)
        ->assertSee('conversions/cover-card.jpg', false)
        ->assertSee(url('/novinka', $withCover->id))
        ->assertDontSee('Text s odkazem.')
        ->assertDontSee('Interní Terrain novinka');

    Livewire::test(PostCards::class)->assertViewIs('livewire.frontend.post-cards');
});

it('prefers the uploaded cover, keeps absolute img_url and ignores lost legacy relative paths', function () {
    Storage::fake('public');
    $user = User::factory()->create();
    $attributes = [
        'user_id' => $user->id,
        'title' => 'Novinka',
        'content' => 'Obsah',
        'content_mode' => ContentFormat::Html,
        'private' => PostStatus::Public,
    ];

    $legacy = Post::create([...$attributes, 'img_url' => 'media/2022/06/thubnails/masinka.png']);
    $absolute = Post::create([...$attributes, 'img_url' => 'https://example.com/obrazek.jpg']);
    $uploaded = Post::create([...$attributes, 'img_url' => 'https://example.com/obrazek.jpg']);
    $uploaded->addMedia(UploadedFile::fake()->image('cover.jpg', 1400, 400))
        ->toMediaCollection(Post::MEDIA_COLLECTION_COVER);

    expect($legacy->coverUrl())->toBeNull()
        ->and($absolute->coverUrl())->toBe('https://example.com/obrazek.jpg')
        ->and($uploaded->coverUrl())->toEndWith('conversions/cover-card.jpg')
        ->and($uploaded->coverUrl('detail'))->toEndWith('conversions/cover-detail.jpg');
});

it('shows the post detail in the terrain layout with the uploaded cover and author', function () {
    Storage::fake('public');
    $this->withoutVite();
    $author = User::factory()->create();
    $post = Post::create([
        'user_id' => $author->id,
        'title' => 'Novinka s obrázkem v detailu',
        'content' => 'Obsah',
        'content_mode' => ContentFormat::Html,
        'private' => PostStatus::Public,
    ]);
    $post->addMedia(UploadedFile::fake()->image('cover.jpg', 1400, 400))
        ->toMediaCollection(Post::MEDIA_COLLECTION_COVER);

    $this->get(url('/novinka', $post->id))
        ->assertOk()
        ->assertSee('conversions/cover-detail.jpg', false)
        ->assertSee('max-w-terrain', false)
        ->assertSee(route('posts.index'), false)
        ->assertSeeInOrder(['Obsah', $author->name, $post->created_at->format('d. m. Y')]);
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

    expect($html)->toContain(route('sport-event.show', 322), 'Podzimní testovací závod', 'Oblastní žebříček', 'Brno', 'ORIS 12345', 'border-terrain-accent', 'bg-terrain-accent text-terrain-on-accent');
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

    expect($html)->toContain('size-[4.5rem]', 'bg-terrain-accent text-terrain-on-accent', '>' . $day . '</span>', '>' . $month . '</span>');
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

    expect($html)->toContain('lucide-map-pin', 'bg-terrain-muted text-terrain-ink')
        ->not->toContain('bg-terrain-accent')
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

it('lists up to six upcoming events on the terrain homepage', function () {
    SportEvent::factory()->count(7)->create([
        'date' => Carbon::tomorrow(),
        'sport_id' => 1,
        'event_type' => SportEventType::Race,
        'cancelled' => false,
    ]);

    expect(Livewire::test(EventList::class, ['terrain' => true])->viewData('events'))->toHaveCount(6);
});
