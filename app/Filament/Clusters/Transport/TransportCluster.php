<?php

declare(strict_types=1);

namespace App\Filament\Clusters\Transport;

use Filament\Clusters\Cluster;

class TransportCluster extends Cluster
{
    protected static string | \BackedEnum | null $navigationIcon = 'heroicon-o-truck';

    protected static ?string $navigationLabel = 'Doprava';

    protected static ?string $clusterBreadcrumb = 'Doprava';

    protected static ?int $navigationSort = 99;
}
