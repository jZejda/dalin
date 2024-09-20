<?php

declare(strict_types=1);

namespace App\Filament\Widgets;

use App\Enums\EntryStatus;
use App\Models\Post;
use App\Shared\Helpers\AppHelper;
use BezhanSalleh\FilamentShield\Traits\HasWidgetShield;
use Carbon\Carbon;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\DB;

class StatsOverview extends BaseWidget
{
    use HasWidgetShield;

    protected function getCards(): array
    {
        $date = Carbon::now();

        $startOfYear = $date->copy()->startOfYear();
        $endOfYear   = $date->copy()->endOfYear();

        $usersAmountCount = DB::table('user_credits')
            ->where('user_id', '=', Auth()->user()?->id)
            ->select(['amount'])
            ->sum('amount');

        $usersAmountChartData = DB::table('user_credits')
            ->where('user_id', '=', Auth()->user()?->id)
            ->select(['amount', 'created_at'])
            ->orderByDesc('created_at')
            ->limit(5)
            ->get();

        $users = DB::table('users')
            ->leftJoin('posts', 'users.id', '=', 'posts.user_id')
            ->get();

        $activeUserEntry = DB::table('user_entries AS ue')
            ->leftJoin('user_race_profiles AS urp', 'ue.user_race_profile_id', '=', 'urp.id')
            ->leftJoin('sport_events AS se', 'ue.sport_event_id', '=', 'se.id')
            ->select(['urp.user_id', 'urp.reg_number', 'ue.entry_status', 'se.date', 'se.name', 'se.alt_name'])
            ->where('urp.user_id', '=', Auth()->user()?->id)
            ->whereNotIn('ue.entry_status', [EntryStatus::Cancel->value])
            ->where('se.date', '>', $date->format(AppHelper::MYSQL_DATE_TIME))
            ->get();

        $userProfilesCount = DB::table('user_race_profiles')->where('user_id', '=', Auth()->user()?->id)->count();
        $sportEventsCount = DB::table('sport_events')
            ->where('date', '>', $startOfYear)
            ->where('date', '<', $endOfYear)
            ->count();

        return [
            Stat::make('Finance', $usersAmountCount . ' Kč')
                ->description($usersAmountCount >= 0 ? 'Hurá na závody' : 'Bylo by fajn zaslat dar')
                ->descriptionIcon($usersAmountCount >= 0 ? 'heroicon-m-arrow-trending-up' : 'heroicon-m-arrow-trending-down')
                ->chart(Arr::pluck($usersAmountChartData, 'amount'))
                ->color($usersAmountCount >= 0 ? 'success' : 'danger'),
            Stat::make('Prihlášen do závodů', count($activeUserEntry))
                ->description('Jsi přihlášen ' . count($activeUserEntry) . ' ve všech spravovaných profilech.'),
            Stat::make('Závodních profilů', $userProfilesCount)
                ->description('Aktuálně spravuješ závodních profilů'),

            Stat::make('Závodu v roce ' . $date->format('Y'), $sportEventsCount)
                ->description('V kalendáři je na tento rok zaneseno závodů'),
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

        if (!is_null($lastPrivatePost)) {
            return $lastPrivatePost->content;
        } else {
            return '';
        }
    }
}
