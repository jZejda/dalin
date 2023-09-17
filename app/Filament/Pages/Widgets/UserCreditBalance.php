<?php

namespace App\Filament\Pages\Widgets;

use App\Models\Post;
use BezhanSalleh\FilamentShield\Traits\HasWidgetShield;
use Filament\Widgets\Widget;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\DB;

class UserCreditBalance extends Widget
{
    //use HasWidgetShield;

    protected static string $view = 'filament.pages.widgets.user-credit-balance';

    public function render(): View
    {

        /**
         * @var Post $lastPost
         */
        $lastPost = DB::table('posts')->where('private', '=', 1)->limit(1)->first();

        $usersAmountCount = DB::table('user_credits')
            ->where('user_id', '=', Auth()->user()?->id)
            ->select(['amount'])
            ->sum('amount');

        return view(static::$view, [
            'user_balance' => $usersAmountCount,
        ]);
    }

    public function getColumnSpan(): int | string | array
    {
        return 1;
    }
}
