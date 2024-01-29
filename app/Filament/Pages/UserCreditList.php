<?php

declare(strict_types=1);

namespace App\Filament\Pages;

use App\Filament\Widgets\UserCreditBalance;
use App\Filament\Widgets\UserSendCreditInfo;
use Filament\Pages\Page;

class UserCreditList extends Page
{
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
