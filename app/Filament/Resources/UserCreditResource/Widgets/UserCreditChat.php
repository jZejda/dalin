<?php

namespace App\Filament\Resources\UserCreditResource\Widgets;

use Filament\Widgets\Widget;
use Illuminate\Database\Eloquent\Model;

class UserCreditChat extends Widget
{
    public ?Model $record = null;

    protected static string $view = 'filament.resources.user-credit-resource.widgets.user-credit-chat';
}
