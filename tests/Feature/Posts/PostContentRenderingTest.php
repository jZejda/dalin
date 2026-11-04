<?php

declare(strict_types=1);

use App\Enums\ContentFormat;
use App\Enums\PostStatus;
use App\Mail\NewPost;
use App\Models\Post;
use App\Models\User;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

function renderingTestPost(ContentFormat $format, string $content): Post
{
    return Post::query()->create([
        'user_id' => User::factory()->create()->id,
        'title' => 'Oblastní žebříček – 3. kolo',
        'content' => $content,
        'content_mode' => $format,
        'private' => PostStatus::Public,
    ]);
}

function tipTapDocument(): string
{
    return (string) json_encode([
        'type' => 'doc',
        'content' => [
            ['type' => 'heading', 'attrs' => ['level' => 2], 'content' => [['type' => 'text', 'text' => 'Výsledky kola']]],
            ['type' => 'paragraph', 'content' => [['type' => 'text', 'text' => 'Ema doběhla <první> v D14.']]],
        ],
    ]);
}

it('renders every content format on the post detail', function (ContentFormat $format, string $content, string $expected): void {
    $post = renderingTestPost($format, $content);

    $this->get($post->publicUrl())
        ->assertOk()
        ->assertSee($expected, escape: false);
})->with([
    'html' => [ContentFormat::Html, '<p>Závod se <em>vydařil</em>.</p>', '<p>Závod se <em>vydařil</em>.</p>'],
    'markdown' => [ContentFormat::Markdown, 'Závod se **vydařil**.', '<strong>vydařil</strong>'],
    'tiptap' => [ContentFormat::TipTapJson, tipTapDocument(), '<h2>Výsledky kola</h2>'],
]);

it('escapes text of TipTap content on the post detail', function (): void {
    $post = renderingTestPost(ContentFormat::TipTapJson, tipTapDocument());

    $this->get($post->publicUrl())
        ->assertOk()
        ->assertSee('Ema doběhla &lt;první&gt; v D14.', escape: false)
        ->assertDontSee('<první>', escape: false);
});

it('renders TipTap content into the new post mail', function (): void {
    $post = renderingTestPost(ContentFormat::TipTapJson, tipTapDocument());

    $html = (new NewPost($post, 'Novinka'))->render();

    expect($html)->toContain('Výsledky kola')->toContain('Ema doběhla');
});

it('resolves images attached in the TipTap editor to their media URL', function (): void {
    Storage::fake('public');
    $post = renderingTestPost(ContentFormat::TipTapJson, tipTapDocument());
    $media = $post->addMedia(UploadedFile::fake()->image('cil.jpg', 800, 600))
        ->toMediaCollection(Post::MEDIA_COLLECTION_CONTENT_ATTACHMENTS);

    $post->update(['content' => json_encode([
        'type' => 'doc',
        'content' => [['type' => 'image', 'attrs' => ['id' => $media->uuid, 'src' => null, 'alt' => 'Cíl závodu']]],
    ])]);

    $this->get($post->refresh()->publicUrl())
        ->assertOk()
        ->assertSee('src="'.$media->getUrl().'"', escape: false);
});
