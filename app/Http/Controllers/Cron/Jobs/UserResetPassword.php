<?php

declare(strict_types=1);

namespace App\Http\Controllers\Cron\Jobs;

use App\Mail\UserPasswordReset;
use App\Models\User;
use Hash;
use Illuminate\Support\Facades\Mail;

class UserResetPassword
{
    public function runSecure(): void
    {
        $users = User::whereNotIn('id', [1,2,4,5,6,8,9,10,12,30])->get();
        foreach ($users as $user) {

            $password = substr(sha1((string)time()), 0, 10);
            $user->password = Hash::make($password);
            $user->saveOrFail();

            Mail::to($user)->send(new UserPasswordReset($password, $user));
        }
    }
}
