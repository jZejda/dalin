<?php

declare(strict_types=1);

namespace App\Filament\Clusters\Other;

use CodeWithDennis\FilamentLucideIcons\Enums\LucideIcon;
use Filament\Clusters\Cluster;

class OtherCluster extends Cluster
{
    protected static string | \BackedEnum | null $navigationIcon = LucideIcon::UserCog;

    protected static ?int $navigationSort = 36;

    public static function getNavigationLabel(): string
    {
        return __('other-cluster.cluster.navigation_label');
    }

    public static function getNavigationGroup(): string | \UnitEnum | null
    {
        return __('app.navigation_groups.users');
    }

    public static function getClusterBreadcrumb(): ?string
    {
        return __('other-cluster.cluster.breadcrumb');
    }
}
