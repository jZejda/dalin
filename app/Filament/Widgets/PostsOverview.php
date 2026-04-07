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

    protected string $view = 'filament.widgets.posts-overview';

    public function render(): View
    {
        $posts = DB::table('posts')
            ->where('private', '=', 1)
            ->orderByDesc('created_at')
            ->limit(5)
            ->get();

        return view($this->view, [
            'posts' => $posts,
        ]);
    }

    public function getColumnSpan(): int | string | array
    {
        return 3;
    }
}
