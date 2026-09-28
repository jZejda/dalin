<?php

declare(strict_types=1);

use App\Enums\ContentFormat;
use App\Enums\PostStatus;
use App\Filament\Resources\Posts\Pages\EditPost;
use App\Models\Post;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

use function Pest\Livewire\livewire;

uses(DatabaseTransactions::class);

it('stores an uploaded cover image with card and detail conversions', function (): void {
    Storage::fake('public');
    $admin = actingAsSuperAdmin();
    $post = Post::create([
        'user_id' => $admin->id,
        'title' => 'Novinka bez obrázku',
        'content' => 'Obsah',
        'content_mode' => ContentFormat::Markdown,
        'private' => PostStatus::Public,
    ]);

    livewire(EditPost::class, ['record' => $post->getRouteKey()])
        ->assertFormFieldExists('cover')
        ->fillForm(['cover' => UploadedFile::fake()->image('zavod.jpg', 1400, 400)])
        ->call('save')
        ->assertHasNoFormErrors();

    $media = $post->refresh()->getFirstMedia(Post::MEDIA_COLLECTION_COVER);

    expect($media)->not->toBeNull()
        ->and($media?->hasGeneratedConversion('card'))->toBeTrue()
        ->and($media?->hasGeneratedConversion('detail'))->toBeTrue()
        ->and($post->coverUrl())->toMatch('~/conversions/[^/]+-card\.jpg$~');
});
