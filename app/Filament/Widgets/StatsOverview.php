<?php

declare(strict_types=1);

namespace App\Filament\Widgets;

use App\Models\Post;
use Auth;
use BezhanSalleh\FilamentShield\Traits\HasWidgetShield;
use Carbon\Carbon;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Card;
use Illuminate\Support\Facades\DB;

class StatsOverview extends BaseWidget
{
    use HasWidgetShield;

    protected function getCards(): array
    {
        $date = Carbon::now();

        $startOfYear = $date->copy()->startOfYear();
        $endOfYear   = $date->copy()->endOfYear();

        $usersCount = DB::table('users')->count();
        $userProfilesCount = DB::table('user_race_profiles')->count();
        $sportEventsCount = DB::table('sport_events')
            ->where('date', '>', $startOfYear)
            ->where('date', '<', $endOfYear)
            ->count();

        $jo = Auth::user()?->hasRole('super_admin');

        return [
            Card::make('Uživatelů', $usersCount),
            Card::make('test', $jo),
            Card::make('Registrace', $userProfilesCount),
            Card::make('Závodu v roce ' . $date->format('Y'), $sportEventsCount),
        ];
    }

    private function getLastPost(): string
    {
        /**
         * @var Post $lastPrivatePost
         */
        $lastPrivatePost = DB::table('posts')
            ->where('private', '=', 1)
            ->first();

        if(!is_null($lastPrivatePost)) {
            return $lastPrivatePost->content;
        } else {
            return '';
        }
    }
}
