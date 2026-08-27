<?php

declare(strict_types=1);

namespace App\Http\Requests\Api\V1;

use App\Enums\ContentFormat;
use App\Models\Post;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdatePostRequest extends FormRequest
{
    public function authorize(): bool
    {
        // Access is gated by the `role:` middleware on the route (Redactor/SuperAdmin/EventMaster).
        return $this->user() !== null;
    }

    /** @return array<string, list<mixed>|string> */
    public function rules(): array
    {
        /** @var Post|null $post */
        $post = $this->route('post');
        $contentMode = $this->has('content_mode')
            ? $this->integer('content_mode')
            : $post?->content_mode?->value;

        return [
            'title' => ['sometimes', 'required', 'string', 'max:255'],
            // TipTap content is a JSON document (array); HTML/Markdown content is a plain string.
            'content' => ['sometimes', 'required', $contentMode === ContentFormat::TipTapJson->value ? 'array' : 'string'],
            'content_mode' => ['nullable', 'integer', Rule::enum(ContentFormat::class)],
            'private' => ['nullable', 'boolean'],
            'editorial' => ['nullable', 'string'],
            'img_url' => ['nullable', 'string', 'max:255'],
        ];
    }
}
