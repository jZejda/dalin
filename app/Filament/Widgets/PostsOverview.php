<?php

declare(strict_types=1);

namespace App\Filament\Widgets;

use App\Enums\PostStatus;
use App\Models\Post;
use BezhanSalleh\FilamentShield\Traits\HasWidgetShield;
use Filament\Widgets\Widget;
use Illuminate\Contracts\View\View;

class PostsOverview extends Widget
{
    use HasWidgetShield;

    protected string $view = 'filament.widgets.posts-overview';

    public function render(): View
    {
        $posts = Post::with('user')
            ->where('private', PostStatus::Private)
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
