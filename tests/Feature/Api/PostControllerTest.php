<?php

declare(strict_types=1);

use App\Enums\ContentFormat;
use App\Enums\PostStatus;
use App\Models\Post;
use App\Models\User;
use Spatie\Permission\Models\Role;

function postApiUser(string $role = User::ROLE_REDACTOR): User
{
    $user = User::factory()->create(['active' => true]);

    Role::findOrCreate($role);
    $user->assignRole($role);
    $user->setApiKey('test-api-key-post-' . $user->id);

    return $user;
}

beforeEach(function (): void {
    $this->redactor = postApiUser();
    $this->asApiUser = fn (User $user) => $this->withHeader('x-apikey', (string) $user->api_key_hash);
});

// ---------------------------------------------------------------------------
// POST /api/v1/post
// ---------------------------------------------------------------------------

test('post store: unauthenticated request is rejected with 401', function (): void {
    $this->postJson('/api/v1/post', ['title' => 'Novinka', 'content' => 'Obsah'])
        ->assertUnauthorized();
});

test('post store: member without redactor role is rejected with 403', function (): void {
    $member = postApiUser(User::ROLE_MEMBER);

    ($this->asApiUser)($member)
        ->postJson('/api/v1/post', ['title' => 'Novinka', 'content' => 'Obsah'])
        ->assertForbidden();
});

test('post store: missing title is rejected with 422', function (): void {
    ($this->asApiUser)($this->redactor)
        ->postJson('/api/v1/post', ['content' => 'Obsah bez titulku'])
        ->assertUnprocessable()
        ->assertJsonValidationErrors(['title']);
});

test('post store: creates a private post by default, authored by the api key owner', function (): void {
    $response = ($this->asApiUser)($this->redactor)
        ->postJson('/api/v1/post', [
            'title' => 'Nová novinka',
            'content' => 'Obsah novinky v Markdownu.',
        ])
        ->assertCreated();

    $response->assertJsonPath('data.title', 'Nová novinka')
        ->assertJsonPath('data.user_id', $this->redactor->id)
        ->assertJsonPath('data.private', PostStatus::Private->value)
        ->assertJsonPath('data.content_mode', ContentFormat::Markdown->value);

    $this->assertDatabaseHas('posts', [
        'title' => 'Nová novinka',
        'user_id' => $this->redactor->id,
        'private' => PostStatus::Private->value,
    ]);
});

test('post store: can create a public post', function (): void {
    ($this->asApiUser)($this->redactor)
        ->postJson('/api/v1/post', [
            'title' => 'Veřejná novinka',
            'content' => 'Obsah pro veřejnost.',
            'private' => false,
        ])
        ->assertCreated()
        ->assertJsonPath('data.private', PostStatus::Public->value);
});

test('post store: stores TipTap JSON content as an encoded document', function (): void {
    $response = ($this->asApiUser)($this->redactor)
        ->postJson('/api/v1/post', [
            'title' => 'TipTap novinka',
            'content_mode' => ContentFormat::TipTapJson->value,
            'content' => ['type' => 'doc', 'content' => []],
        ])
        ->assertCreated();

    $post = Post::query()->findOrFail($response->json('data.id'));

    expect(json_decode($post->content, true))->toBe(['type' => 'doc', 'content' => []]);
});

// ---------------------------------------------------------------------------
// PUT /api/v1/post/{post}
// ---------------------------------------------------------------------------

test('post update: unknown post is rejected with 404', function (): void {
    ($this->asApiUser)($this->redactor)
        ->putJson('/api/v1/post/999999', ['title' => 'X'])
        ->assertNotFound();
});

test('post update: partially updates only the given fields', function (): void {
    $post = Post::query()->create([
        'title' => 'Původní titulek',
        'content' => 'Původní obsah',
        'content_mode' => ContentFormat::Markdown,
        'private' => PostStatus::Private,
        'user_id' => $this->redactor->id,
    ]);

    ($this->asApiUser)($this->redactor)
        ->putJson('/api/v1/post/' . $post->id, ['private' => false])
        ->assertOk()
        ->assertJsonPath('data.title', 'Původní titulek')
        ->assertJsonPath('data.private', PostStatus::Public->value);

    $this->assertDatabaseHas('posts', [
        'id' => $post->id,
        'title' => 'Původní titulek',
        'content' => 'Původní obsah',
        'private' => PostStatus::Public->value,
    ]);
});
