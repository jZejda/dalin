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
        $lastPost = DB::table('posts')->where('private', '=', 1)->limit(1)->first();

        return view(static::$view, [
            'title' => $lastPost->title,
            'content' => (new MarkdownRenderer())->toHtml($lastPost->content),
        ]);
    }

    public function getColumnSpan(): int | string | array
    {
        return 2;
    }
}
