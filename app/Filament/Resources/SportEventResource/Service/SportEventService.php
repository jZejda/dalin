<?php

declare(strict_types=1);

namespace App\Filament\Resources\SportEventResource\Service;

use App\Models\SportEvent;

final class SportEventService
{
    public function getMultiEventStagesOptions(SportEvent $sportEvent): array
    {
        $options = [];
        for ($stage = 1; $stage <= $sportEvent->stages; $stage++) {
            $options['stage' . $stage] = 'Etapa ' . $stage;
        }

        return $options;
    }

    public function getMultiEventDefaultOptions(SportEvent $sportEvent): array
    {
        $defaultOptions = [];
        for ($stage = 1; $stage <= $sportEvent->stages; $stage++) {
            $defaultOptions[] = 'stage' . $stage;
        }

        return $defaultOptions;
    }

}
