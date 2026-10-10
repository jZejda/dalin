# Link-source icons

Every `SportEventLink` shows a small square icon telling the user **where the link leads** (ORIS,
OResults, a photo gallery, a file uploaded to DaLin, …) — independent of the link's *type*
(`SportEventLinkType`: invitation, results, …), which says *what* the document is.

## Architecture

Single source of truth: **`App\Enums\SportEventLinkSource`**.

- **Derived, not persisted.** `SportEventLinkSource::fromUrl($url)` resolves the source from the
  URL host on the fly, so existing links and future ORIS syncs pick up a new source without a
  migration. Use `$link->source()` on the model — it also handles uploaded files: a link with
  `source_path` set is always `Dalin`.
- **Matching rules** (`fromUrl()`):
  1. the host is lowercased and a leading `www.` is stripped;
  2. the host of `config('app.url')` → `Dalin` (each site resolves links to itself);
  3. `docs.google.com` is split by path (`/document`, `/spreadsheets`, `/forms`, else Drive),
     `google.com/maps` → Google Maps;
  4. `domains()` — a domain matches itself **and all its subdomains**
     (`rajce.idnes.cz` covers `skzabovresky.rajce.idnes.cz`, but `notoresults.eu` ≠ `oresults.eu`);
  5. anything else → `Web`.
- **Two variants per icon:**
  - `getIcon()` — **minimal**, monochrome (`fill="currentColor"`), used in link lists; inherits the
    text colour, so it works in light and dark mode;
  - `getColorIcon()` — **full** brand colours, for links shown on other pages of the system.
- **Placeholders:** sources without their own square SVG yet return a Lucide icon from both
  methods (`placeholderIcon()`): `Livelox` → `lucide-link`, `Web` → `lucide-globe`.

The SVGs live in **`resources/svg/link-sources/`** and are registered in
`AppServiceProvider::register()` as the blade-icons set `link-sources` with prefix
**`linksource`** (`SportEventLinkSource::ICON_SET_PREFIX`) — so `oris.svg` is `linksource-oris`
and `oris-color.svg` is `linksource-oris-color`. A blade-icons prefix must not contain `-`.

### Rendering

- Blade: `<x-link-source-icon :source="$link->source()" />` (`resources/views/components/link-source-icon.blade.php`),
  `:colored="true"` for the full variant. Default size `size-4`, override via `class`. The icon is
  decorative (`aria-hidden`), the source name is in `title`.
- Filament table: `IconColumn::make('source')->state(fn (SportEventLink $r) => $r->source())` — the
  enum implements `HasIcon`/`HasLabel` (see `SportEventLinkRelationManager`).
- Link text everywhere is `$link->label()` (name → description → link type; EN falls back to the
  Czech texts) — ORIS "other" links carry the meaningful text in the description.
- Used in: public event detail (`pages/frontend/single-event.blade.php`, compact list), event entry page
  (`partials/backend/sport-event-links.blade.php`), links relation manager in the admin.
- Not used in e-mails — SVG is not reliably supported by mail clients.

## Files uploaded to DaLin

The "Create/Edit link" modal (`SportEventLinkRelationManager`) has a **Link target** toggle:
*Web link* (`source_url`) or *File in DaLin* (`source_path`). A link is always one or the other —
`normalizeTarget()` clears the other column and sets `internal = true` for files. ORIS links
(`external_key` set) are locked to *Web link*, ORIS sync rewrites them anyway.

- Storage: public disk **`events`** (`storage/app/public/events`, shared between deploys) under
  `links/<sport_event_id>/` — constants `SportEventLink::FILE_DISK` / `FILE_DIRECTORY`.
- Always link via **`$link->url()`** (disk URL for a file, else `source_url`) — never
  `source_url` directly. Views, the API (`url`) and the pre-race mail already do.
- Allowed: PDF, JPG/PNG/WebP/GIF, TXT, DOC(X), XLS(X), ODT/ODS, max 10 MB (Livewire's default
  temporary upload limit is 12 MB). The MIME type is detected from the content; the stored
  extension comes from `ACCEPTED_FILE_TYPES` for that type, **never from the client** (Filament's
  default keeps the client extension — on a public disk served by Apache a text file named
  `x.php` could otherwise end up executable). Stored name: `<slug of original name>-<ulid>.<ext>`.
  Never add HTML, SVG or anything executable to the list.
- `SportEventLinkObserver` deletes the file when the link is deleted or its file is replaced /
  switched to a URL. Not covered: links removed by a DB cascade when a whole event is deleted.

## Icon set

| Case | Domains | Icon source | Colour |
|---|---|---|---|
| `Dalin` | `config('app.url')` host, `source_path` | "d" symbol cut out of the docs logo (Zudoku repo `public/dalin-logo-light.svg`) | `currentColor` + `#f57900` dot |
| `Oris` | `oris.orientacnisporty.cz`, `oris.ceskyorientak.cz` | official favicon — [manual.ceskyorientak.cz/podznacky/oris-fav](https://manual.ceskyorientak.cz/podznacky/oris-fav) | `#fe5900` |
| `CsosMaps` | `mapy.orientacnisporty.cz`, `mapy.ceskyorientak.cz` | site favicon (`/favicon.svg`) | `#fe5900` |
| `OResults` | `oresults.eu` | leading "O with a square" cut out of the wordmark `oresults.eu/assets/ores-*.svg` | `#007cff` |
| `Liveresultat` | `liveresultat.orientering.se` | site favicon (`/beta/favicon.svg`) | flag colours |
| `Livelox` | `livelox.com` | *placeholder* `lucide-link` (only a 64 px PNG exists) | — |
| `Rajce` | `rajce.idnes.cz`, `rajce.net` | Safari mask-icon | `#e61900` |
| `Mapy` | `mapy.cz`, `mapy.com` | Safari mask-icon | `#1eae00` |
| `Facebook`, `Instagram`, `YouTube`, `GoogleDrive`, `GoogleDocs`, `GoogleSheets`, `GoogleForms`, `GooglePhotos`, `GoogleMaps`, `Flickr`, `Booking` | see `domains()` | [Simple Icons](https://simpleicons.org) 16.34.0 (CC0) | brand hex from Simple Icons data |
| `Web` | everything else | *placeholder* `lucide-globe` | — |

### Known sources without an icon (yet)

From the production sample (2 887 links, Oct 2026) — currently resolved as `Web`:

- **Zonerama** (≈ 210 links) — favicon is not square (1869×1534);
- **Livelox** — only a raster favicon; placeholder until we get an SVG;
- OrienteerFeed, Česká televize, PlayMap — raster favicons only;
- Eventor (IOF), Tulospalvelu, ObPostupy, orientacnisporty.cz — no usable favicon found;
- Zoom — Simple Icons has only the wordmark, unreadable at 16 px.

## Adding a new source

1. **Get a square SVG.** Prefer an official SVG favicon / brand manual / Simple Icons; never trace
   a raster. Only square artwork qualifies — if only a wide logo exists, cut out the symbol and
   centre it on a square `viewBox` (as for DaLin and OResults).
2. **Normalise it into two files** in `resources/svg/link-sources/`:
   - `<key>.svg` — monochrome: `fill="currentColor"` on the root `<svg>`, no other fills
     (use `fill-rule="evenodd"` / holes instead of white shapes);
   - `<key>-color.svg` — full colours, explicit `fill`s. Avoid a near-black brand colour — it
     disappears in dark mode (use `currentColor` for that part, like the DaLin "d");
   - both: square `viewBox`, **no `width`/`height`**, no `<title>`, no XML declaration / DOCTYPE,
     no `<style>` or `class` (inline `fill` only).
3. **Add the enum case** (`value` = file name key) and its domains to `domains()`; if the source
   has no icon yet, add it to `placeholderIcon()` instead of creating files.
4. **Label** in `lang/{cs,en}/sport-event.php` → `link_source_enum` (brand names are not translated).
5. **Tests:** add a real URL to the `link urls` dataset in
   `tests/Unit/Enums/SportEventLinkSourceTest.php`. The same file checks that every case has both
   SVGs, that they are square and that blade-icons can render them.
6. If the icon does not show up locally, clear the blade-icons manifest:
   `make art c='icons:clear'` (deploy rebuilds caches via `artisan:optimize`).
