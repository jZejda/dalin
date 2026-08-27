<?php

declare(strict_types=1);

namespace App\Http\Requests\Api\V1;

use App\Enums\ContentFormat;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StorePostRequest extends FormRequest
{
    public function authorize(): bool
    {
        // Access is gated by the `role:` middleware on the route (Redactor/SuperAdmin/EventMaster).
        return $this->user() !== null;
    }

    /** @return array<string, list<mixed>|string> */
    public function rules(): array
    {
        $contentMode = $this->integer('content_mode', ContentFormat::Markdown->value);

        return [
            'title' => ['required', 'string', 'max:255'],
            // TipTap content is a JSON document (array); HTML/Markdown content is a plain string.
            'content' => ['required', $contentMode === ContentFormat::TipTapJson->value ? 'array' : 'string'],
            'content_mode' => ['nullable', 'integer', Rule::enum(ContentFormat::class)],
            'private' => ['nullable', 'boolean'],
            'editorial' => ['nullable', 'string'],
            'img_url' => ['nullable', 'string', 'max:255'],
        ];
    }
}
