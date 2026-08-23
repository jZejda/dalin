<?php

declare(strict_types=1);

namespace App\Filament\Widgets;

use App\Services\AppVersionService;
use App\Shared\Helpers\AppHelper;
use Filament\Widgets\Widget;
use Illuminate\Contracts\View\View;

class AppVersion extends Widget
{
    protected string $view = 'filament.widgets.app-version';

    // Patička dashboardu — ostatní widgety sort nemají a jdou první.
    protected static ?int $sort = 10;

    // Widget nechodí do DB, nemá smysl kvůli němu dělat druhý request.
    protected static bool $isLazy = false;

    public function render(): View
    {
        $version = app(AppVersionService::class);

        $build = $version->build();

        if ($build === null) {
            $buildLabel = __('dashboard.version.unknown_build');
        } else {
            $buildLabel = __('dashboard.version.build', ['build' => $build]);
        }

        $builtAt = $version->builtAt();
        $deployedAt = null;

        if ($builtAt !== null) {
            $deployedAt = __('dashboard.version.deployed_at', [
                'date' => $builtAt->format(AppHelper::DATE_FORMAT),
            ]);
        }

        return view($this->view, [
            'appName' => config('app.name'),
            'tag' => $version->tag(),
            'buildLabel' => $buildLabel,
            'deployedAt' => $deployedAt,
            'runtime' => __('dashboard.version.runtime', [
                'php' => PHP_MAJOR_VERSION.'.'.PHP_MINOR_VERSION,
                'laravel' => app()->version(),
            ]),
        ]);
    }

    public function getColumnSpan(): int | string | array
    {
        return 1;
    }
}
