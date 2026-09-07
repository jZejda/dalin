<?php

declare(strict_types=1);

namespace App\Services\Map;

/**
 * Everything needed to render one map marker: which icon partial to draw inside the
 * colored circle, the circle's background color, an optional corner letter badge
 * (E/R modifier for races, or a custom letter for auxiliary points), and an optional
 * small category badge (training / training camp / club championship / other).
 *
 * See docs/map-icons.md for the full icon taxonomy and how to extend it.
 */
final readonly class MapMarkerVisual
{
    public function __construct(
        public string $iconSlug,
        public string $colorHex,
        public ?string $modifierLetter,
        public ?string $categoryIconSlug,
    ) {
    }
}
