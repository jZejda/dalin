<?php

declare(strict_types=1);

use App\Enums\SportEventLinkSource;
use App\Models\SportEventLink;

// URLs taken from real production links (see docs/link-source-icons.md)
dataset('link urls', [
    'ORIS file' => ['https://oris.orientacnisporty.cz/files/6808_89dc31f84ec3f274665881a80075b162.pdf', SportEventLinkSource::Oris],
    'ORIS page (old domain)' => ['https://oris.ceskyorientak.cz/Zavod?id=10215', SportEventLinkSource::Oris],
    'ČSOS map archive' => ['https://mapy.orientacnisporty.cz/mapa/kralovec-1976', SportEventLinkSource::CsosMaps],
    'OResults' => ['https://oresults.eu/events/178', SportEventLinkSource::OResults],
    'Liveresultat' => ['https://liveresultat.orientering.se/followfull.php?comp=25813&lang=cz', SportEventLinkSource::Liveresultat],
    'Livelox' => ['https://www.livelox.com/Viewer/Event?eventId=1', SportEventLinkSource::Livelox],
    'Rajče subdomain' => ['https://skzabovresky.rajce.idnes.cz/album', SportEventLinkSource::Rajce],
    'Mapy.com' => ['https://mapy.com/s/abc', SportEventLinkSource::Mapy],
    'Facebook short' => ['https://fb.me/e/abc', SportEventLinkSource::Facebook],
    'YouTube short' => ['https://youtu.be/abc', SportEventLinkSource::YouTube],
    'Google Sheets' => ['https://docs.google.com/spreadsheets/d/1Zg/edit', SportEventLinkSource::GoogleSheets],
    'Google Docs' => ['https://docs.google.com/document/d/1Zg/edit', SportEventLinkSource::GoogleDocs],
    'Google Forms' => ['https://forms.gle/abc', SportEventLinkSource::GoogleForms],
    'Google Drive' => ['https://drive.google.com/file/d/115V/view', SportEventLinkSource::GoogleDrive],
    'Google Photos' => ['https://photos.app.goo.gl/abc', SportEventLinkSource::GooglePhotos],
    'Google Maps' => ['https://www.google.com/maps/place/Brno', SportEventLinkSource::GoogleMaps],
    'Flickr short' => ['https://flic.kr/s/abc', SportEventLinkSource::Flickr],
    'uppercase host' => ['HTTPS://WWW.INSTAGRAM.COM/p/abc', SportEventLinkSource::Instagram],
    'organiser website' => ['https://o-tour.cz/zavody/sumava-2023/', SportEventLinkSource::Web],
    'look-alike domain' => ['https://notoresults.eu/events/1', SportEventLinkSource::Web],
    'no host' => ['not a url', SportEventLinkSource::Web],
    'null' => [null, SportEventLinkSource::Web],
]);

it('resolves the source from a link url', function (?string $url, SportEventLinkSource $expected): void {
    expect(SportEventLinkSource::fromUrl($url))->toBe($expected);
})->with('link urls');

it('resolves links to this DaLin instance as DaLin', function (): void {
    config(['app.url' => 'https://abmbrno.cz']);

    expect(SportEventLinkSource::fromUrl('https://www.abmbrno.cz/vysledky/oddilovy-prebor-2025-e1'))
        ->toBe(SportEventLinkSource::Dalin);
});

it('resolves a file uploaded to DaLin as DaLin regardless of its url', function (): void {
    $link = new SportEventLink(['source_path' => 'sport-events/1/rozpis.pdf', 'source_url' => 'https://oresults.eu/events/1']);

    expect($link->source())->toBe(SportEventLinkSource::Dalin);
});

it('has a square mono and color svg for every source with its own icon', function (SportEventLinkSource $source): void {
    foreach ([$source->getIcon(), $source->getColorIcon()] as $icon) {
        if (str_starts_with($icon, 'lucide-')) {
            continue;
        }

        $file = resource_path('svg/link-sources/'.substr($icon, strlen(SportEventLinkSource::ICON_SET_PREFIX) + 1).'.svg');
        expect($file)->toBeFile();

        preg_match('/viewBox="\S+ \S+ (\S+) (\S+)"/', (string) file_get_contents($file), $box);
        expect((float) $box[1])->toBe((float) $box[2]);
    }

    expect(svg($source->getIcon())->toHtml())->toContain('<svg')
        ->and(svg($source->getColorIcon())->toHtml())->toContain('<svg');
})->with(SportEventLinkSource::cases());

it('has a label for every source', function (SportEventLinkSource $source): void {
    expect($source->getLabel())->not->toStartWith('sport-event.');
})->with(SportEventLinkSource::cases());
