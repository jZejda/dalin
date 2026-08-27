<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api\V1;

use App\Enums\ContentFormat;
use App\Enums\PostStatus;
use App\Http\Controllers\Controller;
use App\Http\Requests\Api\V1\StorePostRequest;
use App\Http\Requests\Api\V1\UpdatePostRequest;
use App\Http\Resources\PostResource;
use App\Models\User;
use Knuckles\Scribe\Attributes\BodyParam;
use Knuckles\Scribe\Attributes\Group;
use Knuckles\Scribe\Attributes\QueryParam;
use App\Models\Post;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Knuckles\Scribe\Attributes\Response;

#[Group('Post', 'Novinky a příspěvky klubu — veřejné i interní (jen pro členy).')]
class PostController extends Controller
{
    /**
     * Seznam příspěvků
     *
     * Vrátí stránkovaný seznam novinek a příspěvků klubu.
     */
    #[QueryParam('from', 'string', description:'Date from in Y-M-D', required: false, example: '2024-12-31')]
    #[QueryParam('to', 'string', description: 'Date to in Y-M-D', required: false, example: '2024-12-31')]
    #[QueryParam('page', 'integer', 'Filter by whether a post is public or not.', required: false, example: 1)]
    #[QueryParam('per_page', 'string', 'Field to sort by. Defaults to \'id\'.', required: false)]
    #[Response(<<<JSON
      {
        "data": [
            {
                "id": 1,
                "user_id": 1,
                "title": "Novinka jak noha",
                "editorial": null,
                "img_url": null,
                "content": "Toto je prvn9 novinka",
                "content_mode": 2,
                "private": 1,
                "created_at": "2025-10-05T21:26:52.000000Z",
                "updated_at": "2025-10-05T21:26:52.000000Z"
            }
        ],
        "links": {
            "first": "http://localhost/api/v1/posts/list?page=1",
            "last": null,
            "prev": null,
            "next": null
        },
        "meta": {
            "current_page": 1,
            "current_page_url": "http://localhost/api/v1/posts/list?page=1",
            "from": 1,
            "path": "http://localhost/api/v1/posts/list",
            "per_page": 20,
            "to": 1
        }
        }
    JSON)]
    public function list(Request $request): JsonResource
    {
        $query = Post::query();

        $from = $request->query('from');
        $to = $request->query('to');

        if ($from !== null) {
            $fromDate = Carbon::parse($from)->startOfDay();
            $query->where('created_at', '>=', $fromDate);
        }

        if ($to !== null) {
            $toDate = Carbon::parse($to)->endOfDay();
            $query->where('created_at', '<=', $toDate);
        }

        $posts = $query->orderByDesc('created_at')
            ->select([
                'id', 'user_id', 'title', 'editorial', 'img_url', 'content', 'content_mode', 'private', 'created_at', 'updated_at'
            ])
            ->simplePaginate($request->query('per_page', 20));

        return JsonResource::collection($posts);
    }

    /**
     * Detail příspěvku
     *
     * Vrátí detail konkrétního příspěvku podle ID.
     */
    #[Response(<<<JSON
      {
        "data": {
            "id": 1,
            "user_id": 1,
            "title": "Novinka jak noha",
            "editorial": null,
            "img_url": null,
            "content": "Toto je prvn9 novinka",
            "content_mode": 2,
            "private": 1,
            "created_at": "2025-10-05T21:26:52.000000Z",
            "updated_at": "2025-10-05T21:26:52.000000Z"
        }
      }
    JSON, 200)]
    #[Response(<<<JSON
      {
        "data": {
            "id": 2,
            "user_id": 1,
            "title": "<h1>HTML Post</h1>",
            "editorial": null,
            "img_url": null,
            "content": "<p>This is HTML content</p>",
            "content_mode": 1,
            "private": 0,
            "created_at": "2025-10-05T21:26:52.000000Z",
            "updated_at": "2025-10-05T21:26:52.000000Z"
        }
      }
    JSON, 200, description: "Example with HTML content (content_mode = 1)")]
    #[Response(<<<JSON
      {
        "data": {
            "id": 3,
            "user_id": 1,
            "title": "TipTap Post",
            "editorial": null,
            "img_url": null,
            "content": {
                "type": "doc",
                "content": [
                    {
                        "type": "paragraph",
                        "content": [
                            {
                                "type": "text",
                                "text": "This is TipTap JSON content"
                            }
                        ]
                    }
                ]
            },
            "content_mode": 3,
            "private": 0,
            "created_at": "2025-10-05T21:26:52.000000Z",
            "updated_at": "2025-10-05T21:26:52.000000Z"
        }
      }
    JSON, 200, description: "Example with TipTap JSON content (content_mode = 3)")]
    public function detail(Post $post): PostResource
    {
        return new PostResource($post);
    }

    /**
     * Vytvoření příspěvku
     *
     * Vytvoří novou novinku — veřejnou nebo interní (viditelnou jen členům), podle příznaku `private`.
     * Autorem se stává uživatel, jehož API klíč byl použit k autentizaci.
     */
    #[BodyParam('title', 'string', description: 'Post title.', example: 'Nová novinka z klubu')]
    #[BodyParam('content', 'string', description: 'Post content. Plain string for HTML/Markdown (content_mode 1/2), or a TipTap JSON document for content_mode 3.', example: 'Obsah novinky v Markdownu.')]
    #[BodyParam('content_mode', 'integer', description: 'Content format: 1 = HTML, 2 = Markdown, 3 = TipTap JSON. Defaults to 2 (Markdown).', required: false, example: 2)]
    #[BodyParam('private', 'boolean', description: 'true = internal post (members only), false = public. Defaults to true.', required: false, example: true)]
    #[BodyParam('editorial', 'string', description: 'Optional editorial note.', required: false, example: null)]
    #[BodyParam('img_url', 'string', description: 'Optional cover image URL/path.', required: false, example: null)]
    #[Response(<<<JSON
      {
        "data": {
            "id": 4,
            "user_id": 1,
            "title": "Nová novinka z klubu",
            "editorial": null,
            "img_url": null,
            "content": "Obsah novinky v Markdownu.",
            "content_mode": 2,
            "private": 1,
            "created_at": "2026-08-27T08:00:00.000000Z",
            "updated_at": "2026-08-27T08:00:00.000000Z"
        }
      }
    JSON, 201, description: 'Post created')]
    #[Response('{"message": "The title field is required.", "errors": {"title": ["The title field is required."]}}', 422, description: 'Validation error')]
    public function store(StorePostRequest $request): JsonResponse
    {
        $validated = $request->validated();

        /** @var User $user */
        $user = $request->user();

        $contentMode = ContentFormat::from($validated['content_mode'] ?? ContentFormat::Markdown->value);

        $post = Post::query()->create([
            'title' => $validated['title'],
            'content' => $this->normalizeContent($validated['content'], $contentMode),
            'content_mode' => $contentMode,
            'private' => $this->toPostStatus($validated['private'] ?? true),
            'editorial' => $validated['editorial'] ?? null,
            'img_url' => $validated['img_url'] ?? null,
            'user_id' => $user->id,
        ]);

        return (new PostResource($post))->response()->setStatusCode(201);
    }

    /**
     * Úprava příspěvku
     *
     * Upraví existující novinku. Odesílá se jen to, co se má změnit — ostatní pole zůstanou beze změny.
     */
    #[BodyParam('title', 'string', description: 'Post title.', required: false, example: 'Upravený titulek novinky')]
    #[BodyParam('content', 'string', description: 'Post content. Plain string for HTML/Markdown (content_mode 1/2), or a TipTap JSON document for content_mode 3.', required: false, example: 'Upravený obsah novinky.')]
    #[BodyParam('content_mode', 'integer', description: 'Content format: 1 = HTML, 2 = Markdown, 3 = TipTap JSON.', required: false, example: 2)]
    #[BodyParam('private', 'boolean', description: 'true = internal post (members only), false = public.', required: false, example: false)]
    #[BodyParam('editorial', 'string', description: 'Optional editorial note.', required: false, example: null)]
    #[BodyParam('img_url', 'string', description: 'Optional cover image URL/path.', required: false, example: null)]
    #[Response(<<<JSON
      {
        "data": {
            "id": 4,
            "user_id": 1,
            "title": "Upravený titulek novinky",
            "editorial": null,
            "img_url": null,
            "content": "Upravený obsah novinky.",
            "content_mode": 2,
            "private": 0,
            "created_at": "2026-08-27T08:00:00.000000Z",
            "updated_at": "2026-08-27T08:05:00.000000Z"
        }
      }
    JSON, 200, description: 'Post updated')]
    #[Response('{"message": "No query results for model [App\\\\Models\\\\Post] 999"}', 404, description: 'Post not found')]
    public function update(UpdatePostRequest $request, Post $post): PostResource
    {
        $validated = $request->validated();

        $contentMode = isset($validated['content_mode'])
            ? ContentFormat::from($validated['content_mode'])
            : $post->content_mode;

        if (array_key_exists('content', $validated)) {
            $post->content = $this->normalizeContent($validated['content'], $contentMode);
        }

        if (isset($validated['content_mode'])) {
            $post->content_mode = $contentMode;
        }

        if (array_key_exists('title', $validated)) {
            $post->title = $validated['title'];
        }

        if (array_key_exists('private', $validated)) {
            $post->private = $this->toPostStatus($validated['private']);
        }

        if (array_key_exists('editorial', $validated)) {
            $post->editorial = $validated['editorial'];
        }

        if (array_key_exists('img_url', $validated)) {
            $post->img_url = $validated['img_url'];
        }

        $post->save();

        return new PostResource($post);
    }

    private function normalizeContent(mixed $content, ContentFormat $contentMode): string
    {
        if ($contentMode === ContentFormat::TipTapJson && is_array($content)) {
            return json_encode($content, JSON_UNESCAPED_UNICODE | JSON_THROW_ON_ERROR);
        }

        return (string) $content;
    }

    private function toPostStatus(bool $private): PostStatus
    {
        return $private ? PostStatus::Private : PostStatus::Public;
    }
}
