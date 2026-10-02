<?php

declare(strict_types=1);

use App\Enums\ContentFormat;
use App\Enums\PostStatus;
use App\Filament\Resources\Posts\Pages\CreatePost;
use App\Filament\Resources\Posts\Pages\EditPost;
use App\Models\Post;
use App\Models\User;

use function Pest\Livewire\livewire;

function createPostWithStatus(PostStatus $status): Post
{
    return Post::query()->create([
        'user_id' => User::factory()->create()->id,
        'title' => 'Oddílová schůze',
        'content' => 'Schůze proběhne ve čtvrtek v klubovně.',
        'editorial' => 'Pozvánka na schůzi',
        'content_mode' => ContentFormat::Markdown,
        'private' => $status,
    ]);
}

it('hides the editorial, SEO and cover fields of an internal post', function (): void {
    actingAsSuperAdmin();
    $post = createPostWithStatus(PostStatus::Private);

    livewire(EditPost::class, ['record' => $post->getRouteKey()])
        ->assertFormFieldHidden('editorial')
        ->assertFormFieldHidden('seo.title')
        ->assertFormFieldHidden('cover')
        ->fillForm(['private' => false])
        ->assertFormFieldVisible('editorial')
        ->assertFormFieldVisible('seo.title')
        ->assertFormFieldVisible('cover');
});

it('shows the fields of a public post and hides them once it is switched to internal', function (): void {
    actingAsSuperAdmin();
    $post = createPostWithStatus(PostStatus::Public);

    livewire(EditPost::class, ['record' => $post->getRouteKey()])
        ->assertFormFieldVisible('editorial')
        ->assertFormFieldVisible('cover')
        ->fillForm(['private' => true])
        ->assertFormFieldHidden('editorial')
        ->assertFormFieldHidden('cover');
});

it('hides the fields on create, where a new post is internal by default', function (): void {
    actingAsSuperAdmin();

    livewire(CreatePost::class)
        ->assertFormFieldHidden('editorial')
        ->assertFormFieldHidden('cover');
});

it('keeps the editorial of an internal post untouched on save', function (): void {
    actingAsSuperAdmin();
    $post = createPostWithStatus(PostStatus::Private);

    livewire(EditPost::class, ['record' => $post->getRouteKey()])
        ->fillForm(['title' => 'Oddílová schůze – změna místa'])
        ->call('save')
        ->assertHasNoFormErrors();

    expect($post->refresh()->editorial)->toBe('Pozvánka na schůzi')
        ->and($post->title)->toBe('Oddílová schůze – změna místa');
});
