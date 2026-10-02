<?php

declare(strict_types=1);

namespace App\Http\Controllers\Frontend;

use App\Enums\PostStatus;
use App\Http\Controllers\Controller;
use App\Models\Post as PostModel;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
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

    public function post(Request $request, string $post): View|RedirectResponse
    {
        $model = PostModel::query()
            ->with(['media', 'user', 'seo'])
            ->where('private', '=', PostStatus::Public)
            ->find((int) $post);

        if ($model === null) {
            abort(404);
        }

        if ($post !== $model->routeSlug()) {
            // Keep campaign parameters (utm_*, fbclid) of old shared links
            $query = $request->getQueryString();

            return redirect()->to($model->publicUrl().($query !== null ? '?'.$query : ''), 301);
        }

        return view('pages.frontend.show-post', [
            'post' => $model,
            'seo' => $model,
            'sponsorSectionId' => 0,  // logic from model
        ]);
    }
}
