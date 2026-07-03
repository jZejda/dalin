<?php

declare(strict_types=1);

namespace App\Filament\Clusters\Transport\Pages;

use App\Filament\Clusters\Transport\TransportCluster;
use App\Models\AppSetting;
use BezhanSalleh\FilamentShield\Traits\HasPageShield;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Notifications\Notification;
use Filament\Pages\Page;
use Filament\Schemas\Components\Section;

class TransportSettings extends Page implements HasForms
{
    use HasPageShield;
    use InteractsWithForms;

    protected static ?string $cluster = TransportCluster::class;

    protected static string | \BackedEnum | null $navigationIcon = 'heroicon-o-cog-6-tooth';

    protected static ?string $navigationLabel = 'Nastavení dopravy';

    protected static ?string $title = 'Nastavení dopravy';

    protected static ?int $navigationSort = 10;

    protected string $view = 'filament.clusters.transport.pages.transport-settings';

    public bool $transport_enabled = false;

    public function mount(): void
    {
        $this->transport_enabled = AppSetting::isTransportModuleEnabled();
    }

    protected function getFormSchema(): array
    {
        return [
            Section::make('Modul Doprava')
                ->description('Po zapnutí modulu bude možné u závodů nastavit typ dopravy, členové uvidí správu svých vozidel a budou moci nabízet spolujízdu.')
                ->schema([
                    Toggle::make('transport_enabled')
                        ->label('Modul doprava je zapnutý')
                        ->helperText('Vypnutí modulu skryje dopravu v celé aplikaci, uložená data zůstanou zachována.'),
                ]),
        ];
    }

    public function submit(): void
    {
        AppSetting::set(AppSetting::TRANSPORT_MODULE_ENABLED, $this->transport_enabled);

        Notification::make()
            ->title('Nastavení dopravy uloženo')
            ->success()
            ->send();
    }
}
