<?php

declare(strict_types=1);

use App\Enums\ContentFormat;
use App\Enums\PostStatus;
use App\Filament\Resources\Posts\Pages\EditPost;
use App\Models\Post;
use App\Models\User;
use App\Services\Seo\PostSeo;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

use function Pest\Livewire\livewire;

beforeEach(function (): void {
    Cache::flush();
    config(['site-config.club.full_name' => 'OK Testov', 'site-config.club.abbr' => 'TST']);
});

function createPublicPost(array $attributes = []): Post
{
    return Post::query()->create(array_merge([
        'user_id' => User::factory()->create(['name' => 'Jana Nováková'])->id,
        'title' => 'Jarní soustředění v Jeseníkách',
        'content' => "## Program\n\nTři dny tréninků v **kamenitém** terénu.",
        'content_mode' => ContentFormat::Markdown,
        'private' => PostStatus::Public,
    ], $attributes));
}

/**
 * @return array<string, array<string, mixed>>
 */
function postJsonLd(string $html): array
{
    preg_match_all('#<script type="application/ld\+json">(.*?)</script>#s', $html, $matches);

    return collect($matches[1])
        ->map(static fn (string $json): array => json_decode($json, true, flags: JSON_THROW_ON_ERROR))
        ->keyBy('@type')
        ->all();
}

it('builds a slugged public url and redirects bare ids and outdated slugs permanently', function (): void {
    $post = createPublicPost();

    expect($post->publicUrl())->toBe(url('/novinka/'.$post->id.'-jarni-soustredeni-v-jesenikach'));

    $this->get('/novinka/'.$post->id)->assertRedirect($post->publicUrl())->assertStatus(301);
    $this->get('/novinka/'.$post->id.'-stary-nazev')->assertRedirect($post->publicUrl())->assertStatus(301);
    $this->get($post->publicUrl())->assertOk();
});

it('returns 404 for private posts and malformed ids', function (): void {
    $private = createPublicPost(['private' => PostStatus::Private]);

    $this->get('/novinka/'.$private->id)->assertNotFound();
    $this->get($private->publicUrl())->assertNotFound();
    $this->get('/novinka/abc')->assertNotFound();
});

it('renders article meta tags with the editorial as description', function (): void {
    $post = createPublicPost(['editorial' => "Zveme vás na **jarní** soustředění.\n\nPřihlášky do pátku."]);

    $this->get($post->publicUrl())
        ->assertOk()
        ->assertSee('<title>Jarní soustředění v Jeseníkách | TST</title>', escape: false)
        ->assertSee('<meta name="description" content="Zveme vás na jarní soustředění. Přihlášky do pátku.">', escape: false)
        ->assertSee('<meta name="author" content="Jana Nováková">', escape: false)
        ->assertSee('<meta property="og:type" content="article">', escape: false)
        ->assertSee('<meta property="article:published_time" content="'.$post->created_at?->toIso8601String().'">', escape: false)
        ->assertSee('<link rel="canonical" href="'.$post->publicUrl().'">', escape: false);
});

it('falls back to the start of the content, cut on a word boundary', function (): void {
    $post = createPublicPost(['content' => str_repeat('Orientační běh v lese. ', 20)]);

    $description = app(PostSeo::class)->excerpt($post);

    expect($description)
        ->toStartWith('Orientační běh v lese. Orientační')
        ->toEndWith('…')
        ->and(mb_strlen((string) $description))->toBeLessThanOrEqual(160);
});

it('uses the cropped cover for sharing and publishes NewsArticle and breadcrumbs', function (): void {
    Storage::fake('public');
    $post = createPublicPost();
    $post->addMedia(UploadedFile::fake()->image('cover.jpg', 2000, 1500))
        ->toMediaCollection(Post::MEDIA_COLLECTION_COVER);
    $post->refresh();

    $imageUrl = url((string) $post->getFirstMedia(Post::MEDIA_COLLECTION_COVER)?->getUrl(PostSeo::IMAGE_CONVERSION));

    $html = $this->get($post->publicUrl())
        ->assertOk()
        ->assertSee('<meta property="og:image" content="'.$imageUrl.'">', escape: false)
        ->assertSee('<meta property="og:image:width" content="1200">', escape: false)
        ->assertSee('<meta property="og:image:height" content="630">', escape: false)
        ->assertSee('<meta name="twitter:card" content="summary_large_image">', escape: false)
        ->getContent();

    $schemas = postJsonLd((string) $html);

    expect($schemas['NewsArticle'])
        ->toMatchArray([
            'headline' => 'Jarní soustředění v Jeseníkách',
            'image' => [$imageUrl],
            'author' => ['@type' => 'Person', 'name' => 'Jana Nováková'],
            'mainEntityOfPage' => ['@type' => 'WebPage', '@id' => $post->publicUrl()],
        ])
        ->and($schemas['NewsArticle']['publisher']['name'])->toBe('OK Testov')
        ->and(array_column($schemas['BreadcrumbList']['itemListElement'], 'item'))
        ->toBe([url('/'), route('posts.index'), $post->publicUrl()]);
});

it('prefers the manual SEO title and description from the admin', function (): void {
    $post = createPublicPost();
    $post->seo->update(['title' => 'Soustředění Jeseníky 2026', 'description' => 'Ruční popis pro vyhledávače.']);

    $this->get($post->publicUrl())
        ->assertSee('<title>Soustředění Jeseníky 2026 | TST</title>', escape: false)
        ->assertSee('<meta name="description" content="Ruční popis pro vyhledávače.">', escape: false);
});

it('creates the SEO record with a new post and saves it from the admin form', function (): void {
    actingAsSuperAdmin();
    $post = createPublicPost();

    expect($post->seo->exists)->toBeTrue();

    livewire(EditPost::class, ['record' => $post->getRouteKey()])
        ->fillForm([
            'seo.title' => 'Titulek pro Google',
            'seo.description' => 'Popis pro Google.',
        ])
        ->call('save')
        ->assertHasNoFormErrors();

    expect($post->refresh()->seo->title)->toBe('Titulek pro Google')
        ->and($post->seo->description)->toBe('Popis pro Google.');
});

it('keeps the query string when redirecting old links', function (): void {
    $post = createPublicPost();

    $this->get('/novinka/'.$post->id.'?utm_source=facebook&fbclid=abc')
        ->assertStatus(301)
        // Symfony normalizes (sorts) the query string
        ->assertRedirect($post->publicUrl().'?fbclid=abc&utm_source=facebook');
});

it('describes TipTap posts from their text instead of the club description', function (): void {
    $post = createPublicPost([
        'content_mode' => ContentFormat::TipTapJson,
        'content' => json_encode([
            'type' => 'doc',
            'content' => [['type' => 'paragraph', 'content' => [['type' => 'text', 'text' => 'Výsledky oblastního žebříčku jsou venku.']]]],
        ]),
    ]);

    expect(app(PostSeo::class)->excerpt($post))->toBe('Výsledky oblastního žebříčku jsou venku.');
});

it('reuses the loaded post for its SEO data instead of querying it again', function (): void {
    $post = createPublicPost();

    DB::enableQueryLog();
    $this->get($post->publicUrl())->assertOk();
    $postQueries = collect(DB::getQueryLog())
        ->filter(static fn (array $query): bool => str_contains($query['query'], 'from `posts`'));

    expect($postQueries)->toHaveCount(1);
});

it('names the homepage breadcrumb the same on news, pages and events', function (): void {
    $post = createPublicPost();
    $html = (string) $this->get($post->publicUrl())->getContent();

    expect(postJsonLd($html)['BreadcrumbList']['itemListElement'][0])
        ->toMatchArray(['name' => __('app.seo.breadcrumb_home'), 'item' => url('/')]);
});
