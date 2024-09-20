<?php

namespace App\Filament\Widgets;

use App\Models\Post;
use BezhanSalleh\FilamentShield\Traits\HasWidgetShield;
use Filament\Widgets\Widget;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\DB;

class PostsOverview extends Widget
{
    use HasWidgetShield;

    protected static string $view = 'filament.widgets.posts-overview';

    public function render(): View
    {
        /**
         * @var Post $lastPost
         */
        $lastPost = DB::table('posts')
            ->where('private', '=', 1)
            ->limit(1)
            ->orderByDesc('created_at')
            ->first();

        return view(static::$view, [
            'content_mode' => $lastPost->content_mode ?? 1,
            'title' => $lastPost->title ?? '',
            'content' => $lastPost->content ?? 'nic',
        ]);
    }

    public function getColumnSpan(): int | string | array
    {
        return 3;
    }
}
