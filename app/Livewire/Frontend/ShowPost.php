<?php

declare(strict_types=1);

namespace App\Livewire\Frontend;

use App\Models\Post;
use App\Shared\Helpers\EmptyType;
use Illuminate\View\View;
use Livewire\Component;

class ShowPost extends Component
{
    public Post|null $post = null;

    public function mount(int|null $id): void
    {
        if (EmptyType::intNotEmpty($id)) {
            $this->post = Post::where('id', '=', $id)->where('private', '=', 0)->first();
        }
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
