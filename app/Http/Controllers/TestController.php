<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Models\User;

class TestController extends Controller
{
    public function test(): void
    {

        $user = User::find(1);

        $user->setApiKey($this->generateApiKey());


//        $users = User::query()
//            ->whereIn('id', [1,4])
//            ->get();
//
//
//        dd($users);

    }

    function generateApiKey(int $length = 32): string {
        return bin2hex(random_bytes($length));
    }
}
