<?php

declare(strict_types=1);

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Models\Post as PostModel;
use App\Shared\Helpers\EmptyType;
use Illuminate\View\View;

class PostController extends Controller
{
    public const int PER_PAGE = 12;

    public function index(): View
    {
        $posts = PostModel::query()
            ->with(['media', 'user'])
            ->where('private', '=', 0)
            ->orderByDesc('created_at')
            ->paginate(self::PER_PAGE);

        return view('pages.frontend.posts', [
            'posts' => $posts,
            'sponsorSectionId' => 0,
        ]);
    }

    public function post(string $id): View
    {
        $post = null;
        if (EmptyType::stringNotEmpty($id)) {
            $post = PostModel::query()
                ->where('id', '=', $id)
                ->where('private', '=', 0)
                ->first();
        }

        if ($post === null) {
            abort('404');
        }

        return view('pages.frontend.show-post', [
            'post' => $post,
            'sponsorSectionId' => 0,  // logic from model
        ]);
    }
}
