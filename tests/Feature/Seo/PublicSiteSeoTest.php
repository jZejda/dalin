<?php

declare(strict_types=1);

use App\Enums\ContentFormat;
use App\Enums\PageStatus;
use App\Enums\PostStatus;
use App\Http\Controllers\Frontend\SitemapController;
use App\Models\AppSetting;
use App\Models\Page;
use App\Models\Post;
use App\Models\SportEvent;
use App\Models\User;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Storage;

beforeEach(function (): void {
    Cache::flush();
    config(['site-config.club.full_name' => 'OK Testov', 'site-config.club.abbr' => 'TST']);
});

function createSeoTestPost(array $attributes = []): Post
{
    return Post::query()->create(array_merge([
        'user_id' => User::factory()->create()->id,
        'title' => 'Jarní soustředění',
        'content' => '<p>Obsah</p>',
        'content_mode' => ContentFormat::Html,
        'private' => PostStatus::Public,
    ], $attributes));
}

/**
 * @return list<array<string, mixed>>
 */
function jsonLdBlocks(string $html): array
{
    preg_match_all('#<script type="application/ld\+json">(.*?)</script>#s', $html, $matches);

    return array_map(static fn (string $json): array => json_decode($json, true, flags: JSON_THROW_ON_ERROR), $matches[1]);
}

it('renders title, social tags and club structured data on the homepage', function (): void {
    Storage::fake('public');
    Storage::disk('public')->putFileAs('seo', UploadedFile::fake()->image('sharing.jpg', 1200, 630), 'sharing.jpg');
    AppSetting::set(AppSetting::SEO_DESCRIPTION, 'Oddíl orientačního běhu z Testova.');
    AppSetting::set(AppSetting::SEO_IMAGE, 'seo/sharing.jpg');
    AppSetting::set(AppSetting::SEO_SAME_AS, ['https://www.facebook.com/oktestov']);

    $html = $this->get('/')->assertOk()->getContent();

    expect($html)
        ->toContain('<title>Orienťák spojuje | OK Testov</title>')
        ->toContain('<meta name="description" content="Oddíl orientačního běhu z Testova.">')
        ->toContain('<meta property="og:site_name" content="OK Testov">')
        ->toContain('<meta property="og:locale" content="cs_CZ">')
        ->toContain('<meta property="og:image" content="'.Storage::disk('public')->url('seo/sharing.jpg').'">')
        ->toContain('<meta property="og:image:width" content="1200">')
        ->toContain('<meta name="twitter:card" content="summary_large_image">')
        ->toContain('<link rel="canonical" href="'.url('/').'">');

    $schemas = collect(jsonLdBlocks($html))->keyBy('@type');

    expect($schemas['SportsOrganization'])
        ->toMatchArray([
            'name' => 'OK Testov',
            'alternateName' => 'TST',
            'description' => 'Oddíl orientačního běhu z Testova.',
            'sameAs' => ['https://www.facebook.com/oktestov'],
        ])
        ->and($schemas['WebSite']['publisher'])->toBe(['@id' => $schemas['SportsOrganization']['@id']]);
});

it('omits the sharing image when the stored file is missing', function (): void {
    Storage::fake('public');
    AppSetting::set(AppSetting::SEO_IMAGE, 'seo/deleted.jpg');

    expect($this->get('/')->getContent())->not->toContain('og:image');
});

it('uses the page title section with the club suffix and does not double-escape it', function (): void {
    $post = createSeoTestPost(['title' => 'Závody & tréninky']);

    $this->get('/novinka/'.$post->id)
        ->assertOk()
        ->assertSee('<title>Závody &amp; tréninky | OK Testov</title>', escape: false)
        ->assertSee('<meta property="og:title" content="Závody &amp; tréninky | OK Testov">', escape: false);
});

it('keeps the page number in the canonical url of paginated listings', function (): void {
    $this->get('/novinky?page=2')
        ->assertOk()
        ->assertSee('<link rel="canonical" href="'.url('/novinky').'?page=2">', escape: false);
});

it('serves robots.txt with the sitemap and blocked admin', function (): void {
    $this->get('/robots.txt')
        ->assertOk()
        ->assertHeader('Content-Type', 'text/plain; charset=UTF-8')
        ->assertSee('Disallow: /admin')
        ->assertSee('Sitemap: '.url('/sitemap.xml'));
});

it('blocks all crawling on sites without a public frontend', function (): void {
    config(['site-config.features.public_site.use_public_site' => false]);

    $this->get('/robots.txt')->assertOk()->assertSee('Disallow: /')->assertDontSee('Sitemap:');
    $this->get('/sitemap.xml')->assertNotFound();
});

it('lists public posts, open pages and recent events in the sitemap', function (): void {
    $publicPost = createSeoTestPost();
    $privatePost = createSeoTestPost(['private' => PostStatus::Private]);
    $openPage = Page::factory()->create(['slug' => 'o-klubu-test']);
    $draftPage = Page::factory()->create(['slug' => 'koncept-test', 'status' => PageStatus::Draft]);
    $upcomingEvent = SportEvent::factory()->create(['date' => now()->addMonth()]);
    $oldEvent = SportEvent::factory()->create(['date' => now()->subYears(2), 'date_end' => now()->subYears(2)]);

    $response = $this->get('/sitemap.xml')
        ->assertOk()
        ->assertHeader('Content-Type', 'application/xml; charset=UTF-8');

    $xml = $response->getContent();

    expect($xml)
        ->toContain('<loc>'.route('posts.show', $publicPost->id).'</loc>')
        ->not->toContain('<loc>'.route('posts.show', $privatePost->id).'</loc>')
        ->toContain('<loc>'.url('/stranka/'.$openPage->slug).'</loc>')
        ->not->toContain('<loc>'.url('/stranka/'.$draftPage->slug).'</loc>')
        ->toContain('<loc>'.route('sport-event.show', $upcomingEvent->id).'</loc>')
        ->not->toContain('<loc>'.route('sport-event.show', $oldEvent->id).'</loc>')
        ->and(Cache::has(SitemapController::CACHE_KEY))->toBeTrue();
});
