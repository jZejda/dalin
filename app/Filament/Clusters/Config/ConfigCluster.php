<?php

declare(strict_types=1);

namespace App\Filament\Clusters\Config;

use Filament\Clusters\Cluster;

class ConfigCluster extends Cluster
{
    protected static string | \BackedEnum | null $navigationIcon = 'heroicon-o-cog-6-tooth';

    protected static ?int $navigationSort = 100;

    public static function getNavigationLabel(): string
    {
        return __('settings.cluster.navigation_label');
    }

    public static function getNavigationGroup(): string | \UnitEnum | null
    {
        return __('settings.cluster.navigation_label');
    }

    public static function getClusterBreadcrumb(): ?string
    {
        return __('settings.cluster.breadcrumb');
    }
}
