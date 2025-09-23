<?php

declare(strict_types=1);

namespace App\Filament\Resources\SportEvents\Widgets;

use Filament\Widgets\Widget;
use Illuminate\Database\Eloquent\Model;

class EventLink extends Widget
{
    public ?Model $record = null;

    // protected static string $view = 'filament.resources.user-credit-resource.widgets.user-credit-overview';
    protected string $view = 'filament.admin.resources.sport-event-resource.widgets.event-links';
}
