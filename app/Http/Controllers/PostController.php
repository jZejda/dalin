<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Models\Post;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class PostController extends Controller
{
    public function index(Request $request): JsonResource
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

        $posts = $query->orderByDesc('created_at')->get([
            'id', 'user_id', 'title', 'editorial', 'img_url', 'content', 'content_mode', 'private', 'created_at', 'updated_at',
        ]);

        return JsonResource::collection($posts);
    }
}


