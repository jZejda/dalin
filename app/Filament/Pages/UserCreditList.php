<?php

declare(strict_types=1);

namespace App\Filament\Pages;

use App\Filament\Widgets\UserCreditBalance;
use App\Filament\Widgets\UserSendCreditInfo;
use BezhanSalleh\FilamentShield\Traits\HasPageShield;
use Filament\Pages\Page;

class UserCreditList extends Page
{
    use HasPageShield;

    protected static ?string $navigationIcon = 'heroicon-o-banknotes';

    protected static ?string $title = 'Finance';
    protected static ?string $navigationGroup = 'Uživatel';
    protected static ?int $navigationSort = 34;

    protected function getHeaderWidgets(): array
    {
        return [
            UserCreditBalance::class,
            UserSendCreditInfo::class,
        ];
    }

    protected static string $view = 'filament.pages.user-credit-list';
}
