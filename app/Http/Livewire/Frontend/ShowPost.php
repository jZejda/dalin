<?php

declare(strict_types=1);

namespace App\Http\Livewire\Frontend;

use App\Models\Post;
use Illuminate\View\View;
use Livewire\Component;

class ShowPost extends Component
{
    public Post $post;

    public function mount(int $id): void
    {
        $this->post = Post::where('id', '=', $id)->where('private', '=', 0)->first();
    }

    public function render(): View
    {
        if ($this->post !== null) {
            return view('livewire.frontend.show-post', ['posts' => $this->post]);
        } else {
            abort(404);
        }
    }
}
