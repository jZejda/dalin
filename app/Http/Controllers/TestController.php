<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Enums\AppRoles;
use App\Http\Controllers\Cron\Jobs\UpdateBankTransaction;
use App\Models\User;
use App\Services\Bank\BankAccountService;
use App\Services\Bank\Connector\MonetaBank;
use Illuminate\Support\Facades\Auth;

class TestController extends Controller
{
    public function test(): void
    {


//        $sync = new BankAccountService();
        $sync = new UpdateBankTransaction();

        $sync->run();


        dd($sync);


//
//        $list = new MonetaBank(1);
//
//        $list->getTransactions();
//
//



//        $users = User::role(AppRoles::BillingSpecialist->value)->where('active', '=', 1)->get();
//
//        dd($users);
//
//        dd(Auth::user()->canCreateEntry());

        //        /**@var User $user */
        //        $user = User::query()->findOrFail(4);
        //
        //        $user->setParam(UserParamType::UserActualBalance, 250.01);
        //
        //        var_dump($user->getParam(UserParamType::UserActualBalance));

    }
}
