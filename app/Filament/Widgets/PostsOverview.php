<?php

namespace App\Filament\Widgets;

use App\Models\Post;
use Filament\Widgets\Widget;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Spatie\LaravelMarkdown\MarkdownRenderer;

class PostsOverview extends Widget
{
    protected static string $view = 'filament.widgets.posts-overview';

    public function render(): View
    {

        /**
         * @var Post $lastPost
         */
        $lastPost = DB::table('posts')->where('private', '=', 0)->limit(1)->first();

        return view(static::$view, [
            'title' => $lastPost->title ?? '',
            'content' => $lastPost->content ?? 'nic',
        ]);
    }

    public function getColumnSpan(): int | string | array
    {
        return 2;
    }
}
