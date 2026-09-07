# Map icons (DEV-29)

How a `SportEvent` (or one of its auxiliary `SportEventMarker` points) ends up as a marker on the
Leaflet map, and how to extend the icon set without breaking consistency.

## Architecture

Markers are **not** PNG images (`L.Icon`) anymore — they're composed HTML/SVG (`L.divIcon`): a
classic teardrop pin (colored head + pointed tip, standard CSS trick: a rounded square rotated
-45deg) with an inline SVG icon in the head, an optional small category badge in the top-right
corner, and an optional letter badge in the top-left corner. This avoids a combinatorial explosion
of pre-rendered PNGs and lets the same building blocks be reused outside the map (see `EventIcon`
below — note `EventIcon` keeps the plain rounded-badge look, only the map pin has the pointed tip).

The pin's geometry is fixed in `resources/views/components/map/marker-icon.blade.php` and must stay
in sync with the `iconSize`/`iconAnchor`/`popupAnchor` passed to `L.divIcon` in
`leaflet-map-widget.blade.php` — the tip (not the center) has to sit exactly on the GPS coordinate.

Single source of truth: **`App\Services\Map\MapMarkerResolver`**. It turns a `SportEvent` (or a
`SportEventMarker` + its parent event) into a **`MapMarkerVisual`** DTO:

```php
final readonly class MapMarkerVisual
{
    public string $iconSlug;           // which icon partial to draw inside the circle
    public string $colorHex;           // circle background color
    public ?string $modifierLetter;    // top-left letter badge: 'E' / 'R' / a custom marker letter / null
    public ?string $categoryIconSlug;  // corner category badge: null for Race, else training/training-camp/club-championship/other
}
```

Rendering happens in `resources/views/components/map/marker-icon.blade.php`, which is rendered
server-side to an HTML string and passed into `L.divIcon({html: ...})` in
`resources/views/filament/shared/leaflet-map-widget.blade.php` (escaped via Laravel's
`Illuminate\Support\Js::from()`).

`App\View\Components\SportEvent\EventIcon` (used in the admin race list, `entryType.blade.php`)
reuses the **same** icon partials and the resolver's `categoryIconSlugFor()`, so the map and the
admin list never drift apart.

## Sport colors

Stored on `sport_lists.color` (hex), seeded in `database/seeders/SportListsSeeder.php`:

| Sport | `sport_lists.id` | Icon slug | Color | Note |
|---|---|---|---|---|
| OB (běh) | 1 | `ob` | `#FF8C00` | **Deviation from manual.ceskyorientak.cz/barvy** (official color is gray) — a deliberate club decision, gray didn't read well on the map. |
| LOB (lyžařský OB) | 2 | `lob` | `#1565C0` | Placeholder — replace with the exact manual hex if/when available. |
| MTBO | 3 | `mtbo` | `#2E7D32` | Placeholder — replace with the exact manual hex if/when available. |
| TrailO | 4 | `trail` | `#6A1B9A` | Placeholder — replace with the exact manual hex if/when available. |

To change a color: update `SportListsSeeder` and re-seed, or update the row directly — there's no
Filament resource for `SportList` (it's a small seed-only lookup table, same as `SportDiscipline`).

## Modifier letter (top-left badge)

Computed by `MapMarkerResolver::resolveModifierLetter()` for the main event pin:
- **`R`** — the event's discipline is a relay (`SportEvent::isRelayDiscipline()`).
- **`E`** — the event has more than one stage (`SportEvent::stages > 1`).
- `null` — plain single-day, non-relay event (no badge).

For auxiliary `SportEventMarker` points (parking, stage start/end, accommodation, other), the badge
instead shows that marker's own `letter` column (e.g. `P`, `S`, `F`, `U`) — a free-text label set per
marker in the admin, unrelated to E/R.

## Category badge (top-right badge)

`MapMarkerResolver::categoryIconSlugFor(SportEventType $type)`:

| `SportEventType` | `categoryIconSlug` | Icon file |
|---|---|---|
| `Race` | `null` (no badge — the clean, default look) | — |
| `Training` | `training` | `category-training.blade.php` |
| `TrainingCamp` | `training-camp` | `category-training-camp.blade.php` |
| `ClubChampionship` | `club-championship` | `category-club-championship.blade.php` |
| `Other` | `other` | `category-other.blade.php` |

## Auxiliary point icons (`SportEventMarkerType`)

Rendered with a neutral gray circle (`#616161`), no sport color:

| `SportEventMarkerType` | Icon slug | Icon file |
|---|---|---|
| `Parking` | `parking` | `parking.blade.php` |
| `StageStart` | `stage-start` | `stage-start.blade.php` |
| `StageEnd` | `stage-end` | `stage-end.blade.php` |
| `Accommodation` | `accommodation` | `accommodation.blade.php` |
| `Other` | `other` | `other.blade.php` |

`DefaultMarker`, `ObRaceSimple`, `ObRaceDot`, `ObRaceStages`, `Training`, `TrainingCamp` are treated
as "this point represents the event itself" (e.g. the event centre) and get the **same** sport
icon/color/badges as the main event pin — see `MapMarkerResolver::resolveForMarker()`.

Of these six, only `DefaultMarker` is offered when manually creating a new marker — see
`SportEventMarkerType::isSelectableForNewMarker()`. The other five (`ObRaceSimple`, `ObRaceDot`,
`ObRaceStages`, `Training`, `TrainingCamp`) predate the current icon system, where the marker's own
type used to pick a differently-styled OB pin; today the pin's look is derived purely from the
event (sport/stages/discipline), so all six render **identically**. Offering five indistinguishable
duplicates in the dropdown just made it look like "every point on this MTB event has the same icon,
no matter what I pick" — they're kept as valid enum cases (existing/imported rows still resolve
correctly) but are filtered out of the create-marker Select.

`SportEventMarkerType::iconSlug()` is the single source of truth for which of the two behaviors a
case gets: it returns `null` for the "represents the event itself" cases above, and the icon slug
(`'parking'`, `'stage-start'`, …) for every auxiliary point type. `MapMarkerResolver::resolveForMarker()`
and the admin icon gallery (`MapIconGallery::auxiliaryData()`) both read this method instead of
duplicating the type → icon mapping, so adding a new auxiliary point type is a one-method change
(see below) — no `match` arm to add anywhere else.

## Where an auxiliary point type's icon shows up

- **Map pin** — `MapMarkerResolver::resolveForMarker()` (via `leaflet-map-widget.blade.php`).
- **Admin "Vytvořit/upravit Bod zájmu" dialog** (`SportMarkersRelationManager::form()`) — the `type`
  Select uses `->allowHtml()` with options pre-rendered through
  `resources/views/filament/forms/components/marker-type-option.blade.php`, so each dropdown row
  shows the real `<x-map.marker-icon>` next to its label instead of a bare text option.
- **Admin markers table** — the `type` column formats the enum through
  `SportEventMarkerType::enumArray()` so it shows the translated label, not the raw enum value.
- **Public event detail page** (`resources/views/pages/frontend/single-event.blade.php`, "Body
  zájmu" section) — renders `<x-map.marker-icon :visual="$markerResolver->resolveForMarker($marker, $event)" />`
  per marker, so the list uses the exact same icon/color as the map instead of a generic letter badge.

All four read `MapMarkerResolver` (directly or via `iconSlug()`), so they can never drift from each
other or from the map.

## How to add a new icon

1. Add the enum case (`SportEventType` or `SportEventMarkerType`). For an auxiliary
   `SportEventMarkerType`, also add its `iconSlug()` match arm — that's the only place the new case
   needs to be wired into resolution logic; the Select dropdown, admin table, map and detail page all
   pick it up automatically.
2. Add the matching lang key in **both** `lang/cs/sport-event.php` and `lang/en/sport-event.php`
   (`type_enum` / `type_enum_markers`) — `tests/Feature/LangParityTest.php` enforces this.
3. Add the SVG partial under `resources/views/components/map/icons/` (viewBox `0 0 24 24`,
   `stroke="currentColor"`/`fill="currentColor"` so it inherits the pin's text color, sized ~21-23px
   for a main icon or ~12px for a corner badge — match whatever the existing partials use).
4. For a new `SportEventType` category badge, wire it into `MapMarkerResolver::categoryIconSlugFor()`.
5. Add a Pest test case to `tests/Unit/Services/Map/MapMarkerResolverTest.php`.
6. If it's demo-worthy, seed an example in `database/seeders/Demo/DemoSportEventExtrasSeeder.php`
   (per the project's demo-data convention in `CLAUDE.md`).
7. Run `vendor/bin/sail bin pint --dirty` and `make phpstan`.

## Known simplifications (follow-ups, not blockers)

- The four sport colors above are **placeholders** except OB (an intentional deviation) — swap in
  the exact manual hex codes for LOB/MTBO/TrailO when available.
- Icon artwork for auxiliary/category badges is intentionally simple geometric SVG, not
  pixel-perfect illustration — refine visually later without touching the resolver/data model.
- The separate `orienteering-icons-website` project (iof-icon.dalin.cz) is a different icon
  domain (IOF control-description symbols) and is not used here.
