<?php

declare(strict_types=1);

use App\Enums\ContentFormat;
use App\Enums\PostStatus;
use App\Filament\Resources\Posts\Pages\CreatePost;
use App\Filament\Resources\Posts\Pages\EditPost;
use App\Models\Post;
use Filament\Forms\Components\MarkdownEditor;
use Filament\Forms\Components\RichEditor;

use function Pest\Livewire\livewire;

/**
 * @return array<string, mixed>
 */
function tipTapEditorDocument(string $heading = 'Výsledky kola'): array
{
    return [
        'type' => 'doc',
        'content' => [
            ['type' => 'heading', 'attrs' => ['level' => 2], 'content' => [['type' => 'text', 'text' => $heading]]],
            ['type' => 'paragraph', 'content' => [['type' => 'text', 'text' => 'Ema doběhla první v D14.']]],
        ],
    ];
}

it('offers the TipTap editor for new posts and stores the document as JSON', function (): void {
    $admin = actingAsSuperAdmin();

    livewire(CreatePost::class)
        ->assertSchemaStateSet(['content_mode' => ContentFormat::TipTapJson->value])
        ->assertFormComponentExists('content', fn (RichEditor $editor): bool => true)
        ->fillForm([
            'title' => 'Oblastní žebříček – 3. kolo',
            'user_id' => $admin->id,
            'private' => false,
            'content' => tipTapEditorDocument(),
        ])
        ->call('create')
        ->assertHasNoFormErrors();

    $post = Post::query()->where('title', 'Oblastní žebříček – 3. kolo')->firstOrFail();

    expect($post->content_mode)->toBe(ContentFormat::TipTapJson)
        ->and(json_decode($post->content, true)['content'][0]['content'][0]['text'])->toBe('Výsledky kola');

    $this->get($post->publicUrl())->assertOk()->assertSee('<h2>Výsledky kola</h2>', escape: false);
});

it('keeps the Markdown editor when Markdown is chosen', function (): void {
    $admin = actingAsSuperAdmin();

    livewire(CreatePost::class)
        ->fillForm(['content_mode' => ContentFormat::Markdown->value])
        ->assertFormComponentExists('content', fn (MarkdownEditor $editor): bool => true)
        ->fillForm([
            'title' => 'Markdown novinka',
            'user_id' => $admin->id,
            'content' => 'Závod se **vydařil**.',
        ])
        ->call('create')
        ->assertHasNoFormErrors();

    expect(Post::query()->where('title', 'Markdown novinka')->firstOrFail())
        ->content_mode->toBe(ContentFormat::Markdown)
        ->content->toBe('Závod se **vydařil**.');
});

it('edits a TipTap post in the rich editor', function (): void {
    $admin = actingAsSuperAdmin();
    $post = Post::query()->create([
        'user_id' => $admin->id,
        'title' => 'TipTap novinka',
        'content' => json_encode(tipTapEditorDocument()),
        'content_mode' => ContentFormat::TipTapJson,
        'private' => PostStatus::Public,
    ]);

    livewire(EditPost::class, ['record' => $post->getRouteKey()])
        ->assertFormComponentExists('content', fn (RichEditor $editor): bool => true)
        ->assertActionHidden('convertToTipTap')
        ->fillForm(['content' => tipTapEditorDocument('Opravené výsledky')])
        ->call('save')
        ->assertHasNoFormErrors();

    expect(json_decode($post->refresh()->content, true)['content'][0]['content'][0]['text'])->toBe('Opravené výsledky');
});

it('converts a Markdown post to TipTap', function (): void {
    $admin = actingAsSuperAdmin();
    $post = Post::query()->create([
        'user_id' => $admin->id,
        'title' => 'Stará novinka',
        'content' => "## Program\n\nTři dny v **kamenitém** terénu.",
        'content_mode' => ContentFormat::Markdown,
        'private' => PostStatus::Public,
    ]);

    livewire(EditPost::class, ['record' => $post->getRouteKey()])
        ->assertActionVisible('convertToTipTap')
        ->callAction('convertToTipTap')
        ->assertRedirect();

    $post->refresh();

    expect($post->content_mode)->toBe(ContentFormat::TipTapJson)
        ->and($post->richContentRenderer()->toHtml())->toContain('<h2>Program</h2>')->toContain('<strong>kamenitém</strong>');
});
