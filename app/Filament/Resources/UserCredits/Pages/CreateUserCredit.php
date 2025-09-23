<?php

namespace App\Filament\Resources\UserCredits\Pages;

use App\Filament\Resources\UserCredits\UserCreditResource;
use Filament\Resources\Pages\CreateRecord;

class CreateUserCredit extends CreateRecord
{
    protected static string $resource = UserCreditResource::class;
}
