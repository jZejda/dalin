<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Models\User;

class TestController extends Controller
{
    public function test(): void
    {

        $users = User::query()
            ->whereIn('id', [1,4])
            ->get();


        dd($users);

    }
}
