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
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Utilities\Get;

class Settings extends Page implements HasForms
{
    use HasPageShield;
    use InteractsWithForms;

    protected static ?string $cluster = ConfigCluster::class;

    protected static string | \BackedEnum | null $navigationIcon = 'heroicon-o-cog-6-tooth';

    protected static ?int $navigationSort = 10;

    public static function getNavigationLabel(): string
    {
        return __('settings.navigation_label');
    }

    public function getTitle(): string
    {
        return __('settings.title');
    }

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
            Grid::make(2)
                ->schema([
                    $this->moduleSection('transport', 'transport_enabled', 'heroicon-o-truck'),
                    $this->moduleSection('event_payments', 'event_payments_enabled', 'heroicon-o-banknotes'),
                    $this->moduleSection('service_orders', 'service_orders_enabled', 'heroicon-o-shopping-cart'),
                    $this->moduleSection('marketplace', 'marketplace_enabled', 'heroicon-o-shopping-bag'),
                    $this->moduleSection('bank', 'bank_enabled', 'heroicon-o-building-library'),
                ]),
        ];
    }

    private function moduleSection(string $langKey, string $fieldName, string $icon): Section
    {
        return Section::make(__("settings.form.{$langKey}.section"))
            ->icon($icon)
            ->description(__("settings.form.{$langKey}.description"))
            ->schema([
                Toggle::make($fieldName)
                    ->live()
                    ->label(fn (Get $get): string => $get($fieldName)
                        ? __("settings.form.{$langKey}.toggle_label_enabled")
                        : __("settings.form.{$langKey}.toggle_label_disabled"))
                    ->helperText(__("settings.form.{$langKey}.toggle_helper")),
            ]);
    }

    public function submit(): void
    {
        AppSetting::set(AppSetting::TRANSPORT_MODULE_ENABLED, $this->transport_enabled);
        AppSetting::set(AppSetting::EVENT_PAYMENTS_MODULE_ENABLED, $this->event_payments_enabled);
        AppSetting::set(AppSetting::SERVICE_ORDERS_MODULE_ENABLED, $this->service_orders_enabled);
        AppSetting::set(AppSetting::MARKETPLACE_MODULE_ENABLED, $this->marketplace_enabled);
        AppSetting::set(AppSetting::BANK_MODULE_ENABLED, $this->bank_enabled);

        Notification::make()
            ->title(__('settings.notification.saved_title'))
            ->success()
            ->send();
    }
}
