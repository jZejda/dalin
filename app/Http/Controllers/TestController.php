<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Enums\AppRoles;
use App\Enums\UserParamType;
use App\Models\User;
use Illuminate\Support\Facades\Auth;

class TestController extends Controller
{
    public function test(): void
    {

        $users = User::role(AppRoles::BillingSpecialist->value)->where('active', '=', 1)->get();

        dd($users);

        dd(Auth::user()->canCreateEntry());

        //        /**@var User $user */
        //        $user = User::query()->findOrFail(4);
        //
        //        $user->setParam(UserParamType::UserActualBalance, 250.01);
        //
        //        var_dump($user->getParam(UserParamType::UserActualBalance));



    }

}
