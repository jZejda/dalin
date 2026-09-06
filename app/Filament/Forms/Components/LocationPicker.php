<?php

declare(strict_types=1);

namespace App\Filament\Forms\Components;

use Filament\Forms\Components\Field;

/**
 * An interactive Leaflet map for picking GPS coordinates. State is a two-element
 * array [lat, lon] (or null). Purely a picker, not meant to be persisted itself —
 * the consuming form reads the value out (via an Action's submit) or mirrors it
 * into real lat/lon fields with ->live()->afterStateUpdated().
 */
class LocationPicker extends Field
{
    protected string $view = 'filament.forms.components.location-picker';
}
