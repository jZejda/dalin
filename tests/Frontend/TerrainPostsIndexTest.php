<?php

declare(strict_types=1);

use App\Enums\ContentFormat;
use App\Enums\PostStatus;
use App\Http\Controllers\Frontend\PostController;
use App\Models\Post;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\DatabaseTransactions;

uses(DatabaseTransactions::class);

/**
 * Creates public posts dated in the future so they sort before any rows already in the testing DB.
 *
 * @return list<Post>
 */
function createFuturePosts(int $count, User $author): array
{
    $posts = [];
    for ($i = 1; $i <= $count; $i++) {
        $posts[] = Post::query()->forceCreate([
            'user_id' => $author->id,
            'title' => sprintf('Stránkovaná novinka %02d', $i),
            'content' => 'Obsah',
            'content_mode' => ContentFormat::Html,
            'private' => PostStatus::Public,
            'created_at' => Carbon::now()->addDays(100 - $i),
        ]);
    }

    return $posts;
}

it('lists public news newest first, twelve per page with terrain pagination', function () {
    $this->withoutVite();
    $author = User::factory()->create();
    $posts = createFuturePosts(PostController::PER_PAGE + 1, $author);
    Post::query()->forceCreate([
        'user_id' => $author->id,
        'title' => 'Interní stránkovaná novinka',
        'content' => 'Obsah',
        'content_mode' => ContentFormat::Html,
        'private' => PostStatus::Private,
        'created_at' => Carbon::now()->addDays(200),
    ]);

    $firstPage = $this->get(route('posts.index'))
        ->assertOk()
        ->assertSee('max-w-terrain', false)
        ->assertSeeInOrder(['Stránkovaná novinka 01', 'Stránkovaná novinka 02', 'Stránkovaná novinka 12'])
        ->assertDontSee('Stránkovaná novinka 13')
        ->assertDontSee('Interní stránkovaná novinka')
        ->assertSee($posts[0]->publicUrl())
        ->assertSee($author->name)
        ->assertSee('aria-current="page"', false)
        ->assertSee(route('posts.index', ['page' => 2]), false);

    expect(substr_count($firstPage->getContent(), '<article'))->toBe(PostController::PER_PAGE);

    $this->get(route('posts.index', ['page' => 2]))
        ->assertOk()
        ->assertSee('Stránkovaná novinka 13')
        ->assertDontSee('Stránkovaná novinka 12')
        ->assertSee(__('content.post.public.index_page_title', ['page' => 2]));
});
