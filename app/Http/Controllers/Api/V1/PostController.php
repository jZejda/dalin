<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Resources\PostResource;
use Knuckles\Scribe\Attributes\Group;
use Knuckles\Scribe\Attributes\QueryParam;
use App\Models\Post;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Knuckles\Scribe\Attributes\Subgroup;
use Knuckles\Scribe\Attributes\Response;

#[Group("V1", "APIs V1")]
#[Subgroup("POST", "News")]
class PostController extends Controller
{
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
}
