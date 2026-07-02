<?php

declare(strict_types=1);

namespace App\Filament\Clusters\Config;

use Filament\Clusters\Cluster;

class ConfigCluster extends Cluster
{
    protected static string | \BackedEnum | null $navigationIcon = 'heroicon-o-cog-6-tooth';

    protected static ?string $navigationLabel = 'Konfigurace';

    protected static ?string $clusterBreadcrumb = 'Konfigurace';

    protected static ?int $navigationSort = 100;
}
