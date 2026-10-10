<?php

declare(strict_types=1);

namespace App\Services\Mail;

use App\Models\AppSetting;
use App\Shared\Helpers\AppHelper;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

/**
 * Club identity used by the branded ("club") mail layout: name, logo, accent colours
 * and footer links. Built from the club settings on every render, so a long-running
 * queue worker picks up a changed setting without a restart.
 */
final readonly class MailBranding
{
    public const string DEFAULT_ACCENT = '#ffd329';

    public const string INK = '#172126';

    public const string PRODUCT_NAME = 'DaLin';

    /** Share of the accent mixed into white for soft highlights (deadline pill, note). */
    private const float SOFT_ACCENT_RATIO = 0.17;

    /** WCAG relative luminance above which dark text reads better than white. */
    private const float LIGHT_LUMINANCE_THRESHOLD = 0.179;

    /**
     * Known social networks, matched by host, with their display label.
     *
     * @var array<string, string>
     */
    private const array SOCIAL_LABELS = [
        'facebook.com' => 'Facebook',
        'instagram.com' => 'Instagram',
        'strava.com' => 'Strava',
        'youtube.com' => 'YouTube',
        'tiktok.com' => 'TikTok',
        'x.com' => 'X',
        'twitter.com' => 'X',
        'linkedin.com' => 'LinkedIn',
    ];

    /**
     * @param list<array{label: string, url: string}> $socialLinks
     */
    public function __construct(
        public string $clubName,
        public string $clubInitials,
        public ?string $logoUrl,
        public string $accent,
        public string $onAccent,
        public string $accentSoft,
        public string $helpUrl,
        public ?string $contactEmail,
        public array $socialLinks,
    ) {
    }

    public static function fromSettings(): self
    {
        $abbr = self::configString('site-config.club.abbr') ?? '';
        $accent = AppSetting::getBrandingAccentColor() ?? self::DEFAULT_ACCENT;
        $logoPath = AppSetting::getSeoLogoPath();

        return new self(
            clubName: self::configString('site-config.club.full_name') ?? ($abbr !== '' ? $abbr : self::PRODUCT_NAME),
            clubInitials: Str::upper(Str::substr($abbr !== '' ? $abbr : self::PRODUCT_NAME, 0, 3)),
            logoUrl: $logoPath !== null ? Storage::disk('public')->url($logoPath) : null,
            accent: $accent,
            onAccent: self::contrastText($accent),
            accentSoft: self::mixWithWhite($accent, self::SOFT_ACCENT_RATIO),
            helpUrl: AppHelper::getPageHelpUrl(''),
            contactEmail: self::configString('site-config.club.technical_email'),
            socialLinks: array_map(
                static fn (string $url): array => ['label' => self::socialLabel($url), 'url' => $url],
                AppSetting::getSeoSameAs(),
            ),
        );
    }

    public function iconUrl(string $name): string
    {
        return asset('images/mail/icons/'.$name.'.png');
    }

    /**
     * Dark ink on light accents (e.g. yellow), white on dark ones.
     */
    public static function contrastText(string $hex): string
    {
        [$red, $green, $blue] = array_map(
            static function (int $channel): float {
                $value = $channel / 255;

                return $value <= 0.04045 ? $value / 12.92 : (($value + 0.055) / 1.055) ** 2.4;
            },
            self::rgb($hex),
        );

        $luminance = 0.2126 * $red + 0.7152 * $green + 0.0722 * $blue;

        return $luminance > self::LIGHT_LUMINANCE_THRESHOLD ? self::INK : '#ffffff';
    }

    /**
     * Mail clients don't support `color-mix()`, so tints are precomputed.
     */
    public static function mixWithWhite(string $hex, float $ratio): string
    {
        return '#'.implode('', array_map(
            static fn (int $channel): string => str_pad(dechex((int) round(255 - (255 - $channel) * $ratio)), 2, '0', STR_PAD_LEFT),
            self::rgb($hex),
        ));
    }

    public static function socialLabel(string $url): string
    {
        $host = Str::lower((string) parse_url($url, PHP_URL_HOST));
        $host = Str::after($host, 'www.');

        foreach (self::SOCIAL_LABELS as $domain => $label) {
            if ($host === $domain || Str::endsWith($host, '.'.$domain)) {
                return $label;
            }
        }

        return $host !== '' ? $host : $url;
    }

    /**
     * @return array{0: int, 1: int, 2: int}
     */
    private static function rgb(string $hex): array
    {
        $hex = ltrim($hex, '#');

        return [(int) hexdec(substr($hex, 0, 2)), (int) hexdec(substr($hex, 2, 2)), (int) hexdec(substr($hex, 4, 2))];
    }

    private static function configString(string $key): ?string
    {
        $value = config($key);

        return is_string($value) && trim($value) !== '' ? trim($value) : null;
    }
}
