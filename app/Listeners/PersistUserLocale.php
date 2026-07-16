<?php

declare(strict_types=1);

namespace App\Listeners;

use App\Models\User;
use BezhanSalleh\LanguageSwitch\Events\LocaleChanged;
use Illuminate\Support\Facades\Auth;

class PersistUserLocale
{
    /**
     * Store the locale chosen in the panel language switch as the user's
     * default language — it then also drives the locale of outgoing e-mails
     * (User implements HasLocalePreference).
     */
    public function handle(LocaleChanged $event): void
    {
        $user = Auth::user();

        if ($user instanceof User && in_array($event->locale, ['cs', 'en'], true)) {
            $user->update(['locale' => $event->locale]);
        }
    }
}
