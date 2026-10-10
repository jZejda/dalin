<?php

declare(strict_types=1);

namespace App\Enums;

use Filament\Support\Contracts\HasIcon;
use Filament\Support\Contracts\HasLabel;
use Override;

/**
 * Where a SportEventLink leads (ORIS, OResults, a photo gallery, …), derived from its URL.
 * Not persisted — resolved on the fly, so existing links and future ORIS syncs pick up
 * new sources without a migration.
 *
 * Single source of truth for link-source icons: the square SVGs live in
 * resources/svg/link-sources/ (blade-icons set "linksource"). See docs/link-source-icons.md
 * for how to add a new source.
 */
enum SportEventLinkSource: string implements HasIcon, HasLabel
{
    case Dalin = 'dalin';
    case Oris = 'oris';
    case CsosMaps = 'csos-maps';
    case OResults = 'oresults';
    case Liveresultat = 'liveresultat';
    case Livelox = 'livelox';
    case Rajce = 'rajce';
    case Mapy = 'mapy';
    case Facebook = 'facebook';
    case Instagram = 'instagram';
    case YouTube = 'youtube';
    case GoogleDrive = 'google-drive';
    case GoogleDocs = 'google-docs';
    case GoogleSheets = 'google-sheets';
    case GoogleForms = 'google-forms';
    case GooglePhotos = 'google-photos';
    case GoogleMaps = 'google-maps';
    case Flickr = 'flickr';
    case Booking = 'booking';
    case Web = 'web';

    /** Prefix of the blade-icons set registered in AppServiceProvider (blade-icons prefixes must not contain "-"). */
    public const string ICON_SET_PREFIX = 'linksource';

    public static function fromUrl(?string $url): self
    {
        $host = self::hostOf($url);

        if ($host === null) {
            return self::Web;
        }

        if ($host === self::hostOf((string) config('app.url'))) {
            return self::Dalin;
        }

        $path = (string) parse_url((string) $url, PHP_URL_PATH);

        if ($host === 'docs.google.com') {
            return match (true) {
                str_starts_with($path, '/document') => self::GoogleDocs,
                str_starts_with($path, '/spreadsheets') => self::GoogleSheets,
                str_starts_with($path, '/forms') => self::GoogleForms,
                default => self::GoogleDrive,
            };
        }

        if (in_array($host, ['google.com', 'google.cz'], true) && str_starts_with($path, '/maps')) {
            return self::GoogleMaps;
        }

        foreach (self::domains() as $domain => $source) {
            if ($host === $domain || str_ends_with($host, '.'.$domain)) {
                return $source;
            }
        }

        return self::Web;
    }

    /**
     * Domain => source; a domain also matches all of its subdomains
     * (e.g. "rajce.idnes.cz" covers "skzabovresky.rajce.idnes.cz").
     *
     * @return array<string, self>
     */
    public static function domains(): array
    {
        return [
            'oris.orientacnisporty.cz' => self::Oris,
            'oris.ceskyorientak.cz' => self::Oris,
            'mapy.orientacnisporty.cz' => self::CsosMaps,
            'mapy.ceskyorientak.cz' => self::CsosMaps,
            'oresults.eu' => self::OResults,
            'liveresultat.orientering.se' => self::Liveresultat,
            'livelox.com' => self::Livelox,
            'rajce.idnes.cz' => self::Rajce,
            'rajce.net' => self::Rajce,
            'mapy.cz' => self::Mapy,
            'mapy.com' => self::Mapy,
            'facebook.com' => self::Facebook,
            'fb.me' => self::Facebook,
            'fb.watch' => self::Facebook,
            'instagram.com' => self::Instagram,
            'youtube.com' => self::YouTube,
            'youtu.be' => self::YouTube,
            'drive.google.com' => self::GoogleDrive,
            'forms.gle' => self::GoogleForms,
            'photos.google.com' => self::GooglePhotos,
            'photos.app.goo.gl' => self::GooglePhotos,
            'maps.google.com' => self::GoogleMaps,
            'maps.app.goo.gl' => self::GoogleMaps,
            'flickr.com' => self::Flickr,
            'flic.kr' => self::Flickr,
            'booking.com' => self::Booking,
        ];
    }

    #[Override]
    public function getLabel(): string
    {
        return __('sport-event.link_source_enum.'.$this->value);
    }

    /**
     * Minimal monochrome icon (fill="currentColor") for link lists.
     */
    #[Override]
    public function getIcon(): string
    {
        return $this->placeholderIcon() ?? self::ICON_SET_PREFIX.'-'.$this->value;
    }

    /**
     * Full-colour brand icon, e.g. for links on other pages of the system.
     */
    public function getColorIcon(): string
    {
        return $this->placeholderIcon() ?? self::ICON_SET_PREFIX.'-'.$this->value.'-color';
    }

    /**
     * Sources without their own square SVG yet fall back to a Lucide icon.
     */
    private function placeholderIcon(): ?string
    {
        return match ($this) {
            self::Livelox => 'lucide-link',
            self::Web => 'lucide-globe',
            default => null,
        };
    }

    private static function hostOf(?string $url): ?string
    {
        $host = parse_url((string) $url, PHP_URL_HOST);

        if (! is_string($host) || $host === '') {
            return null;
        }

        $host = strtolower($host);

        return str_starts_with($host, 'www.') ? substr($host, 4) : $host;
    }
}
