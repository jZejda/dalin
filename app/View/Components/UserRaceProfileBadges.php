<?php

declare(strict_types=1);

namespace App\View\Components;

use App\Models\UserRaceProfile;
use Illuminate\Support\Collection;
use Illuminate\View\Component;
use Illuminate\View\View;

class UserRaceProfileBadges extends Component
{
    /** @param Collection<int, UserRaceProfile> $profiles */
    public function __construct(
        public readonly Collection $profiles,
        public readonly string $size = 'text-xs',
    ) {
    }

    public function render(): View
    {
        return view('components.user-race-profile-badges');
    }
}
