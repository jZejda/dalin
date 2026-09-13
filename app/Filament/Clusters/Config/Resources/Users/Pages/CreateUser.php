<?php

declare(strict_types=1);

namespace App\Filament\Clusters\Config\Resources\Users\Pages;

use App\Filament\Clusters\Config\Resources\Users\UserResource;
use App\Http\Controllers\Cron\Jobs\UserSendPassword;
use App\Models\User;
use Filament\Resources\Pages\CreateRecord;

class CreateUser extends CreateRecord
{
    protected static string $resource = UserResource::class;

    protected function afterCreate(): void
    {
        /** @var User $user */
        $user = $this->record;

        (new UserSendPassword())->sendNewPassword($user, $this->data['password']);
    }
}
