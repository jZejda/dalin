<?php

declare(strict_types=1);

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Shared\Helpers\EmptyType;
use Illuminate\View\View;
use App\Models\Post as PostModel;

class PostController extends Controller
{
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
