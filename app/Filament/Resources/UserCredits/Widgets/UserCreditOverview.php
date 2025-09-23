<?php

namespace App\Filament\Resources\UserCredits\Widgets;

use Filament\Widgets\Widget;
use Illuminate\Database\Eloquent\Model;

class UserCreditOverview extends Widget
{
    public ?Model $record = null;

    protected string $view = 'filament.resources.user-credit-resource.widgets.user-credit-overview';
}
