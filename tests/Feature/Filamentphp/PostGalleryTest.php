<?php

declare(strict_types=1);

use App\Enums\ContentFormat;
use App\Enums\PostStatus;
use App\Filament\Resources\Posts\Pages\EditPost;
use App\Models\Post;
use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

use function Pest\Livewire\livewire;

uses(DatabaseTransactions::class);

function galleryTestPost(int $userId, PostStatus $status = PostStatus::Public): Post
{
    return Post::query()->create([
        'user_id' => $userId,
        'title' => 'Noční závod v Moravském krasu',
        'content' => 'Fotky z nočního závodu.',
        'content_mode' => ContentFormat::Markdown,
        'private' => $status,
    ]);
}

it('stores uploaded gallery photos with thumbnail and lightbox conversions', function (): void {
    Storage::fake('public');
    $admin = actingAsSuperAdmin();
    $post = galleryTestPost($admin->id);

    livewire(EditPost::class, ['record' => $post->getRouteKey()])
        ->assertFormFieldExists('gallery')
        ->fillForm(['gallery' => [
            UploadedFile::fake()->image('start.jpg', 1200, 900),
            UploadedFile::fake()->image('cil.jpg', 900, 1200),
        ]])
        ->call('save')
        ->assertHasNoFormErrors();

    $media = $post->refresh()->getMedia(Post::MEDIA_COLLECTION_GALLERY);

    expect($media)->toHaveCount(2)
        ->and($media->every(fn ($item): bool => $item->hasGeneratedConversion('card') && $item->hasGeneratedConversion('detail')))->toBeTrue()
        ->and($post->getFirstMedia(Post::MEDIA_COLLECTION_COVER))->toBeNull();
});

it('hides the gallery field for internal posts', function (): void {
    $admin = actingAsSuperAdmin();
    $post = galleryTestPost($admin->id, PostStatus::Private);

    livewire(EditPost::class, ['record' => $post->getRouteKey()])
        ->assertFormFieldHidden('gallery');
});

it('shows the gallery under the article in the admin order, with alt texts and lazy loading', function (): void {
    Storage::fake('public');
    $post = galleryTestPost(User::factory()->create()->id);

    foreach (['prvni', 'druha', 'treti'] as $name) {
        $post->addMedia(UploadedFile::fake()->image($name.'.jpg', 1200, 900))
            ->usingFileName($name.'.jpg')
            ->toMediaCollection(Post::MEDIA_COLLECTION_GALLERY);
    }

    $images = $post->refresh()->galleryImages();

    expect($images)->toHaveCount(3)
        ->and($images[0]['full'])->toContain('prvni-detail.jpg')
        ->and($images[2]['thumb'])->toContain('treti-card.jpg');

    $this->get($post->publicUrl())
        ->assertOk()
        ->assertSee('id="post-gallery-heading"', escape: false)
        ->assertSeeInOrder(['prvni-card.jpg', 'druha-card.jpg', 'treti-card.jpg'], escape: false)
        ->assertSee('alt="'.e(__('content.post.public.gallery_photo_alt', ['title' => $post->title, 'number' => 1])).'"', escape: false)
        ->assertSee('loading="lazy"', escape: false);
});

it('renders no gallery section for a post without photos', function (): void {
    $post = galleryTestPost(User::factory()->create()->id);

    $this->get($post->publicUrl())
        ->assertOk()
        ->assertDontSee('post-gallery-heading', escape: false);
});
