<?php

declare(strict_types=1);

namespace App\Filament\Clusters\Config\Pages;

use App\Filament\Clusters\Config\ConfigCluster;
use App\Models\AppSetting;
use BezhanSalleh\FilamentShield\Traits\HasPageShield;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Notifications\Notification;
use Filament\Pages\Page;
use Filament\Schemas\Components\Section;

class Settings extends Page implements HasForms
{
    use HasPageShield;
    use InteractsWithForms;

    protected static ?string $cluster = ConfigCluster::class;

    protected static string | \BackedEnum | null $navigationIcon = 'heroicon-o-cog-6-tooth';

    protected static ?string $navigationLabel = 'Nastavení';

    protected static ?string $title = 'Nastavení';

    protected static ?int $navigationSort = 10;

    protected string $view = 'filament.clusters.config.pages.settings';

    public bool $transport_enabled = false;

    public bool $event_payments_enabled = false;

    public bool $service_orders_enabled = false;

    public bool $marketplace_enabled = false;

    public bool $bank_enabled = false;

    public function mount(): void
    {
        $this->transport_enabled = AppSetting::isTransportModuleEnabled();
        $this->event_payments_enabled = AppSetting::isEventPaymentsModuleEnabled();
        $this->service_orders_enabled = AppSetting::isServiceOrdersModuleEnabled();
        $this->marketplace_enabled = AppSetting::isMarketplaceModuleEnabled();
        $this->bank_enabled = AppSetting::isBankModuleEnabled();
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
            Section::make('Modul Platby u závodů')
                ->description('Po zapnutí modulu se na detailu závodu zobrazí záložka Platby / Finance se správou plateb závodních profilů.')
                ->schema([
                    Toggle::make('event_payments_enabled')
                        ->label('Modul plateb u závodů je zapnutý')
                        ->helperText('Vypnutí modulu skryje záložku plateb na detailu závodu, uložená data zůstanou zachována.'),
                ]),
            Section::make('Modul Doplňkové služby')
                ->description('Po zapnutí modulu si členové mohou na detailu závodu objednávat doplňkové služby (ubytování, nocleh apod.) včetně termínů plateb. Při vypnutém modulu je vidět jen náhled nabízených služeb.')
                ->schema([
                    Toggle::make('service_orders_enabled')
                        ->label('Modul doplňkových služeb je zapnutý')
                        ->helperText('Vypnutí modulu skryje záložku objednávek na detailu závodu, uložená data zůstanou zachována.'),
                ]),
            Section::make('Modul Tržiště')
                ->description('Po zapnutí modulu mohou členové vystavovat nabídky produktů za sebe nebo za oddíl a ostatní si je objednávat. Po ukončení nabídky se náklady rozúčtují podle objednaných kusů.')
                ->schema([
                    Toggle::make('marketplace_enabled')
                        ->label('Modul tržiště je zapnutý')
                        ->helperText('Vypnutí modulu skryje tržiště v celé aplikaci, uložená data zůstanou zachována.'),
                ]),
            Section::make('Modul Napojení na banku')
                ->description('Po zapnutí modulu bude dostupná stránka Bankovní výpis a správa bankovních napojení. Transakce se automaticky stahují, jen pokud je modul zapnutý a existuje alespoň jedno aktivní bankovní napojení.')
                ->schema([
                    Toggle::make('bank_enabled')
                        ->label('Modul napojení na banku je zapnutý')
                        ->helperText('Vypnutí modulu skryje bankovní výpis a zastaví stahování transakcí, uložená data zůstanou zachována.'),
                ]),
        ];
    }

    public function submit(): void
    {
        AppSetting::set(AppSetting::TRANSPORT_MODULE_ENABLED, $this->transport_enabled);
        AppSetting::set(AppSetting::EVENT_PAYMENTS_MODULE_ENABLED, $this->event_payments_enabled);
        AppSetting::set(AppSetting::SERVICE_ORDERS_MODULE_ENABLED, $this->service_orders_enabled);
        AppSetting::set(AppSetting::MARKETPLACE_MODULE_ENABLED, $this->marketplace_enabled);
        AppSetting::set(AppSetting::BANK_MODULE_ENABLED, $this->bank_enabled);

        Notification::make()
            ->title('Nastavení uloženo')
            ->success()
            ->send();
    }
}
