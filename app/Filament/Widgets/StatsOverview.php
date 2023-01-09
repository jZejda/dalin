<?php

declare(strict_types=1);

namespace App\Filament\Widgets;

use App\Models\Post;
use BezhanSalleh\FilamentShield\Traits\HasWidgetShield;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Card;
use Illuminate\Support\Facades\DB;

class StatsOverview extends BaseWidget
{
    use HasWidgetShield;

    protected function getCards(): array
    {
        return [
            Card::make('Unique views', '192.1k'),
            Card::make('Bounce rate', '21%'),
            Card::make('Average time on page', $this->getLastPost()),
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
