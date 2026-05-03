<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\User;

final class CalendarTokenService
{
    public function generate(User $user): string
    {
        $plain = bin2hex(random_bytes(32));
        $user->calendar_token = $plain;
        $user->save();

        return $plain;
    }

    public function validate(string $token): ?User
    {
        return User::where('calendar_token', $token)->first();
    }

    public function revoke(User $user): void
    {
        $user->calendar_token = null;
        $user->save();
    }

    public function regenerate(User $user): string
    {
        return $this->generate($user);
    }

    public function hasToken(User $user): bool
    {
        return $user->calendar_token !== null;
    }
}
