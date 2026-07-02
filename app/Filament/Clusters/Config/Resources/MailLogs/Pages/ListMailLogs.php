<?php

declare(strict_types=1);

namespace App\Filament\Clusters\Config\Resources\MailLogs\Pages;

use App\Filament\Clusters\Config\Resources\MailLogs\MailLogResource;
use Filament\Resources\Pages\ListRecords;

class ListMailLogs extends ListRecords
{
    protected static string $resource = MailLogResource::class;

    protected function getHeaderActions(): array
    {
        return [];
    }
}
