<?php

declare(strict_types=1);

namespace App\Shared\Helpers;

use Illuminate\Support\HtmlString;
use Illuminate\Support\Str;

/**
 * Renders user-written Markdown (credit notes, comments) for display in the panel.
 */
final class Markdown
{
    /**
     * Markdown formatting is kept, inline HTML is shown as text and only safe link schemes are allowed.
     */
    public static function userText(?string $markdown): HtmlString
    {
        return new HtmlString(Str::markdown($markdown ?? '', [
            'html_input' => 'escape',
            'allow_unsafe_links' => false,
        ]));
    }
}
