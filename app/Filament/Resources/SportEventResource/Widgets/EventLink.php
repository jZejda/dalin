<?php

declare(strict_types=1);

namespace App\Filament\Resources\SportEventResource\Widgets;

use Filament\Widgets\Widget;
use Illuminate\Database\Eloquent\Model;

class EventLink extends Widget
{
    public ?Model $record = null;

    // protected static string $view = 'filament.resources.user-credit-resource.widgets.user-credit-overview';
    protected static string $view = 'filament.resources.sport-event-resource.widgets.event-links';
}