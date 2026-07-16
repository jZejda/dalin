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
            Section::make(__('settings.form.transport.section'))
                ->description(__('settings.form.transport.description'))
                ->schema([
                    Toggle::make('transport_enabled')
                        ->label(__('settings.form.transport.toggle_label'))
                        ->helperText(__('settings.form.transport.toggle_helper')),
                ]),
            Section::make(__('settings.form.event_payments.section'))
                ->description(__('settings.form.event_payments.description'))
                ->schema([
                    Toggle::make('event_payments_enabled')
                        ->label(__('settings.form.event_payments.toggle_label'))
                        ->helperText(__('settings.form.event_payments.toggle_helper')),
                ]),
            Section::make(__('settings.form.service_orders.section'))
                ->description(__('settings.form.service_orders.description'))
                ->schema([
                    Toggle::make('service_orders_enabled')
                        ->label(__('settings.form.service_orders.toggle_label'))
                        ->helperText(__('settings.form.service_orders.toggle_helper')),
                ]),
            Section::make(__('settings.form.marketplace.section'))
                ->description(__('settings.form.marketplace.description'))
                ->schema([
                    Toggle::make('marketplace_enabled')
                        ->label(__('settings.form.marketplace.toggle_label'))
                        ->helperText(__('settings.form.marketplace.toggle_helper')),
                ]),
            Section::make(__('settings.form.bank.section'))
                ->description(__('settings.form.bank.description'))
                ->schema([
                    Toggle::make('bank_enabled')
                        ->label(__('settings.form.bank.toggle_label'))
                        ->helperText(__('settings.form.bank.toggle_helper')),
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
            ->title(__('settings.notification.saved_title'))
            ->success()
            ->send();
    }
}
