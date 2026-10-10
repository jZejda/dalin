<?php

declare(strict_types=1);

namespace App\Services\Mail;

use App\Enums\ContentFormat;
use App\Filament\Forms\Components\RichEditor\RichContentCustomBlocks\RichContentBlocks;
use Filament\Forms\Components\RichEditor\RichContentRenderer;
use Illuminate\Support\Str;
use Symfony\Component\HtmlSanitizer\HtmlSanitizerInterface;

/**
 * Turns content written in the editor (HTML, Markdown or TipTap JSON) into HTML that is
 * safe to embed in a club mail, plus a plain-text alternative.
 */
final class MailRichContent
{
    public static function html(string $content, ContentFormat $format): string
    {
        $html = match ($format) {
            ContentFormat::Html => $content,
            ContentFormat::Markdown => Str::markdown($content, ['html_input' => 'escape', 'allow_unsafe_links' => false]),
            ContentFormat::TipTapJson => self::tipTapHtml($content),
        };

        // Filament's sanitizer config: safe elements only, no scripts or event handlers
        $html = app(HtmlSanitizerInterface::class)->sanitize($html);

        // Relative links and images don't resolve in a mail client
        $html = (string) preg_replace_callback(
            '/\b(src|href)="(\/[^"\/][^"]*)"/i',
            static fn (array $match): string => $match[1].'="'.url($match[2]).'"',
            $html,
        );

        // The mail layout parses its slot as Markdown, where a blank line would end the HTML block
        return trim((string) preg_replace("/\n\s*\n/", "\n", $html));
    }

    public static function text(string $html): string
    {
        $text = (string) preg_replace(['/<br\s*\/?>/i', '/<\/(p|div|h[1-6]|li|blockquote|tr)>/i', '/<li[^>]*>/i'], ["\n", "\n", '- '], $html);
        $text = html_entity_decode(strip_tags($text), ENT_QUOTES | ENT_HTML5, 'UTF-8');

        return trim((string) preg_replace("/\n{3,}/", "\n\n", (string) preg_replace("/[ \t]*\n[ \t]*/", "\n", $text)));
    }

    private static function tipTapHtml(string $content): string
    {
        $document = json_decode($content, true);

        if (! is_array($document)) {
            return '';
        }

        return (string) rescue(
            static fn (): string => RichContentRenderer::make($document)
                ->customBlocks(RichContentBlocks::all())
                ->toHtml(),
            '',
            report: false,
        );
    }
}
