<?php

declare(strict_types=1);

use App\Enums\ContentFormat;
use App\Filament\Resources\Pages\Pages\EditPage;
use App\Mcp\Servers\DalinServer;
use App\Mcp\Tools\GetPageTool;
use App\Mcp\Tools\PagesTool;
use App\Models\ContentCategory;
use App\Models\Page;
use App\Models\SportEvent;
use App\Models\User;
use App\Services\Seo\PageSeo;
use Illuminate\Support\Facades\Cache;
use RalphJSmit\Laravel\SEO\Models\SEO;
use Spatie\Permission\Models\Role;

use function Pest\Livewire\livewire;

beforeEach(function (): void {
    Cache::flush();
    config(['site-config.club.full_name' => 'OK Testov', 'site-config.club.abbr' => 'TST']);
});

/**
 * @return array<string, array<string, mixed>>
 */
function pageJsonLd(string $html): array
{
    preg_match_all('#<script type="application/ld\+json">(.*?)</script>#s', $html, $matches);

    return collect($matches[1])
        ->map(static fn (string $json): array => json_decode($json, true, flags: JSON_THROW_ON_ERROR))
        ->keyBy('@type')
        ->all();
}

it('renders title, description from the content and breadcrumbs for a markdown page', function (): void {
    $page = Page::factory()->create([
        'title' => 'Členské **příspěvky**',
        'slug' => 'clenske-prispevky',
        'content_format' => ContentFormat::Markdown,
        'content' => "## Kolik platíme\n\nPříspěvek na rok 2026 je *1 200 Kč*.",
    ]);

    $html = $this->get('/stranka/clenske-prispevky')
        ->assertOk()
        ->assertSee('<title>Členské příspěvky | TST</title>', escape: false)
        ->assertSee('<meta name="description" content="Kolik platíme Příspěvek na rok 2026 je 1 200 Kč.">', escape: false)
        ->assertSee('<link rel="canonical" href="'.$page->publicUrl().'">', escape: false)
        ->getContent();

    expect(array_column(pageJsonLd((string) $html)['BreadcrumbList']['itemListElement'], 'name'))
        ->toBe([__('app.seo.breadcrumb_home'), 'Členské příspěvky']);
});

it('strips markup from HTML titles and reads TipTap content as text', function (): void {
    $page = Page::factory()->create([
        'title' => '<strong>Tréninky</strong> pro děti',
        'content_format' => ContentFormat::TipTapJson,
        'content' => [
            'type' => 'doc',
            'content' => [
                ['type' => 'paragraph', 'content' => [['type' => 'text', 'text' => 'Trénujeme každé úterý v lese.']]],
            ],
        ],
    ]);

    $data = app(PageSeo::class)->dynamicData($page);

    expect($data->title)->toBe('Tréninky pro děti')
        ->and($data->description)->toBe('Trénujeme každé úterý v lese.');
});

it('nests event pages under the event in the breadcrumbs', function (): void {
    $event = SportEvent::factory()->create(['name' => 'Oblastní žebříček Brno']);
    $category = ContentCategory::factory()->create(['sport_event_id' => $event->id]);
    $page = Page::factory()->create(['content_category_id' => $category->id, 'page_menu' => true]);

    $html = $this->get($page->publicUrl())->assertOk()->getContent();

    expect(array_column(pageJsonLd((string) $html)['BreadcrumbList']['itemListElement'], 'item'))
        ->toBe([url('/'), route('sport-event.show', $event->id), $page->publicUrl()]);
});

it('prefers the manual SEO title and description from the admin', function (): void {
    $page = Page::factory()->create();
    $page->seo->update(['title' => 'Ruční titulek', 'description' => 'Ruční popis stránky.']);

    $this->get($page->publicUrl())
        ->assertSee('<title>Ruční titulek | TST</title>', escape: false)
        ->assertSee('<meta name="description" content="Ruční popis stránky.">', escape: false);
});

it('saves the SEO section from the admin form', function (): void {
    actingAsSuperAdmin();
    // The author select only offers active redactors
    $redactor = User::factory()->create(['active' => true]);
    Role::findOrCreate(User::ROLE_REDACTOR);
    $redactor->assignRole(User::ROLE_REDACTOR);
    $page = Page::factory()->create(['user_id' => $redactor->id]);

    livewire(EditPage::class, ['record' => $page->getRouteKey()])
        ->assertFormFieldDoesNotExist('meta_items')
        ->fillForm([
            'seo.title' => 'Titulek pro Google',
            'seo.description' => 'Popis pro Google.',
        ])
        ->call('save')
        ->assertHasNoFormErrors();

    expect($page->refresh()->seo->title)->toBe('Titulek pro Google')
        ->and($page->seo->description)->toBe('Popis pro Google.');
});

it('moves legacy page meta into the seo table', function (): void {
    $legacy = Page::factory()->create(['meta' => ['og:title' => 'OG titulek', 'description' => 'Starý popis', 'keywords' => 'ob, klub']]);
    $keywordsOnly = Page::factory()->create(['meta' => ['keywords' => 'jen klíčová slova']]);
    // Pages created before this release have no seo row yet
    SEO::query()->where('model_type', Page::class)->delete();

    (require database_path('migrations/2026_10_02_100000_move_page_meta_to_seo_table.php'))->up();

    expect($legacy->refresh()->seo->only(['title', 'description']))
        ->toBe(['title' => 'OG titulek', 'description' => 'Starý popis'])
        ->and($legacy->meta)->toHaveKey('keywords')
        ->and($keywordsOnly->refresh()->seo->exists)->toBeFalse();
});

it('exposes the SEO title and description as meta in the API and MCP', function (): void {
    $page = Page::factory()->create(['meta' => ['keywords' => 'zastaralé']]);
    $page->seo->update(['description' => 'Popis z SEO.']);

    $user = User::factory()->create(['active' => true]);
    Role::findOrCreate(User::ROLE_REDACTOR);
    $user->assignRole(User::ROLE_REDACTOR);
    $user->setApiKey('test-api-key-page-'.$user->id);

    $this->withHeader('x-apikey', (string) $user->api_key_hash)
        ->getJson('/api/v1/page/'.$page->id)
        ->assertOk()
        ->assertJsonPath('data.meta', ['description' => 'Popis z SEO.']);

    $this->withHeader('x-apikey', (string) $user->api_key_hash)
        ->getJson('/api/v1/page?per_page=100')
        ->assertOk()
        ->assertJsonFragment(['meta' => ['description' => 'Popis z SEO.']]);

    DalinServer::tool(GetPageTool::class, ['page_id' => $page->id])
        ->assertOk()
        ->assertSee('"meta":{"description":"Popis z SEO."}');

    DalinServer::tool(PagesTool::class, ['per_page' => 100])
        ->assertOk()
        ->assertSee('"meta":{"description":"Popis z SEO."}')
        ->assertDontSee('zastaral');
});
