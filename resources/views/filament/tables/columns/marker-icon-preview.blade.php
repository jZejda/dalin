<?php

use App\Models\SportEventMarker;
use App\Services\Map\MapMarkerResolver;

/** @var SportEventMarker $marker */
$marker = $getRecord();
$sportEvent = $marker->sportEvent;
$visual = $sportEvent ? (new MapMarkerResolver())->resolveForMarker($marker, $sportEvent) : null;
?>

@if ($visual)
    <x-map.marker-icon :visual="$visual" />
@endif
