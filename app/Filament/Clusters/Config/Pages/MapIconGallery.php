<?php

declare(strict_types=1);

namespace App\Filament\Clusters\Config\Pages;

use App\Enums\SportEventMarkerType;
use App\Enums\SportEventType;
use App\Filament\Clusters\Config\ConfigCluster;
use App\Models\SportList;
use App\Services\Map\MapMarkerResolver;
use App\Services\Map\MapMarkerVisual;
use BezhanSalleh\FilamentShield\Traits\HasPageShield;
use CodeWithDennis\FilamentLucideIcons\Enums\LucideIcon;
use Filament\Pages\Page;

class MapIconGallery extends Page
{
    use HasPageShield;

    protected static ?string $cluster = ConfigCluster::class;

    protected static string | \BackedEnum | null $navigationIcon = LucideIcon::Palette;

    protected static ?int $navigationSort = 20;

    protected string $view = 'filament.clusters.config.pages.map-icon-gallery';

    /**
     * Auxiliary marker types that don't just stand in for the event pin
     * itself — see MapMarkerResolver::resolveForMarker() for the full mapping.
     *
     * @var list<SportEventMarkerType>
     */
    private const array AUXILIARY_MARKER_TYPES = [
        SportEventMarkerType::Parking,
        SportEventMarkerType::StageStart,
        SportEventMarkerType::StageEnd,
        SportEventMarkerType::Accommodation,
        SportEventMarkerType::Other,
    ];

    public static function getNavigationLabel(): string
    {
        return __('map-icon-gallery.navigation_label');
    }

    public function getTitle(): string
    {
        return __('map-icon-gallery.title');
    }

    /**
     * @return array<string, mixed>
     */
    protected function getViewData(): array
    {
        $resolver = app(MapMarkerResolver::class);

        return [
            'sports' => $this->sportsData($resolver),
            'categories' => $this->categoriesData($resolver),
            'auxiliary' => $this->auxiliaryData($resolver),
        ];
    }

    /**
     * @return array<int, array{label: string, color: string, variants: array<string, MapMarkerVisual>}>
     */
    private function sportsData(MapMarkerResolver $resolver): array
    {
        return SportList::query()->orderBy('id')->get()->values()->map(function (SportList $sport) use ($resolver): array {
            $iconSlug = $resolver->iconSlugForSport($sport->short_name);

            return [
                'label' => $sport->short_name,
                'color' => $sport->color,
                'variants' => [
                    __('map-icon-gallery.modifier_none') => new MapMarkerVisual($iconSlug, $sport->color, null, null),
                    __('map-icon-gallery.modifier_e') => new MapMarkerVisual($iconSlug, $sport->color, 'E', null),
                    __('map-icon-gallery.modifier_r') => new MapMarkerVisual($iconSlug, $sport->color, 'R', null),
                ],
            ];
        })->values()->all();
    }

    /**
     * @return list<array{label: string, visual: MapMarkerVisual}>
     */
    private function categoriesData(MapMarkerResolver $resolver): array
    {
        return array_map(
            static fn (SportEventType $type): array => [
                'label' => __('sport-event.type_enum.'.$type->value),
                'visual' => new MapMarkerVisual('ob', '#9CA3AF', null, $resolver->categoryIconSlugFor($type)),
            ],
            SportEventType::cases(),
        );
    }

    /**
     * @return list<array{label: string, visual: MapMarkerVisual}>
     */
    private function auxiliaryData(MapMarkerResolver $resolver): array
    {
        $iconSlugs = [
            SportEventMarkerType::Parking->value => 'parking',
            SportEventMarkerType::StageStart->value => 'stage-start',
            SportEventMarkerType::StageEnd->value => 'stage-end',
            SportEventMarkerType::Accommodation->value => 'accommodation',
            SportEventMarkerType::Other->value => 'other',
        ];

        return array_map(
            static fn (SportEventMarkerType $type): array => [
                'label' => __('sport-event.type_enum_markers.'.$type->value),
                'visual' => new MapMarkerVisual($iconSlugs[$type->value], $resolver->auxColor(), null, null),
            ],
            self::AUXILIARY_MARKER_TYPES,
        );
    }
}
