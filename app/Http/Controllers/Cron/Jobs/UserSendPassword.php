<?php

declare(strict_types=1);

namespace App\Http\Controllers\Cron\Jobs;

use App\Mail\UserPasswordSend;
use App\Models\User;
use App\Shared\Helpers\AppHelper;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;

class UserSendPassword
{
    public function sendNewPassword(User $user, ?string $password = null, string $type = UserPasswordSend::ACTION_SEND_PASSWORD): void
    {
        if (is_null($password)) {
            $password = Str::random(AppHelper::GENERATED_PASSWORD_LENGTH);
        }

        $user->password = Hash::make($password);
        $user->saveOrFail();

        Mail::to($user)->queue(new UserPasswordSend($password, $user, $type));
    }

    /**
     * @param User[] $users
     */
    public function massResetPassword(array $users): void
    {
        //$users = User::query()->whereNotIn('id', [1,2,4,5,6,8,9,10,12,30])->get();

        foreach ($users as $user) {

            $password = Str::random(AppHelper::GENERATED_PASSWORD_LENGTH);
            $user->password = Hash::make($password);
            $user->saveOrFail();

            Mail::to($user)->queue(new UserPasswordSend($password, $user));
        }
    }


}
