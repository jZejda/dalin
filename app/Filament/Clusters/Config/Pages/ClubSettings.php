<?php

declare(strict_types=1);

namespace App\Filament\Clusters\Config\Pages;

use App\Filament\Clusters\Config\ConfigCluster;
use App\Models\AppSetting;
use BezhanSalleh\FilamentShield\Traits\HasPageShield;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Notifications\Notification;
use Filament\Pages\Page;
use Filament\Schemas\Components\Section;

class ClubSettings extends Page implements HasForms
{
    use HasPageShield;
    use InteractsWithForms;

    protected static ?string $cluster = ConfigCluster::class;

    protected static ?string $slug = 'club';

    protected static string | \BackedEnum | null $navigationIcon = 'heroicon-o-identification';

    protected static ?int $navigationSort = 11;

    public static function getNavigationLabel(): string
    {
        return __('club-settings.navigation_label');
    }

    public function getTitle(): string
    {
        return __('club-settings.title');
    }

    protected string $view = 'filament.clusters.config.pages.club-settings';

    public string $abbr = '';

    public string $full_name = '';

    public string $primary_bank_account_number = '';

    public string $primary_bank_account_name = '';

    public ?string $iban = null;

    public string $user_credit_limit = '0';

    public string $regular_membership_fees_prefix = '';

    public string $extra_membership_fees_prefix = '';

    public ?string $technical_email = null;

    public function mount(): void
    {
        $this->abbr = $this->clubConfigString('abbr');
        $this->full_name = $this->clubConfigString('full_name');
        $this->primary_bank_account_number = $this->clubConfigString('primary_bank_account_number');
        $this->primary_bank_account_name = $this->clubConfigString('primary_bank_account_name');
        $this->iban = $this->clubConfigStringOrNull('iban');
        $this->user_credit_limit = $this->clubConfigString('user_credit_limit');
        $this->regular_membership_fees_prefix = $this->clubConfigString('regular_membership_fees_prefix');
        $this->extra_membership_fees_prefix = $this->clubConfigString('extra_membership_fees_prefix');
        $this->technical_email = $this->clubConfigStringOrNull('technical_email');
    }

    protected function getFormSchema(): array
    {
        return [
            Section::make(__('club-settings.form.identification.section'))
                ->schema([
                    TextInput::make('abbr')
                        ->label(__('club-settings.form.identification.abbr'))
                        ->disabled()
                        ->dehydrated(false)
                        ->helperText(__('club-settings.form.identification.abbr_helper')),
                    TextInput::make('full_name')
                        ->label(__('club-settings.form.identification.full_name'))
                        ->required()
                        ->maxLength(255),
                ]),
            Section::make(__('club-settings.form.bank_details.section'))
                ->description(__('club-settings.form.bank_details.description'))
                ->schema([
                    TextInput::make('primary_bank_account_number')
                        ->label(__('club-settings.form.bank_details.primary_bank_account_number'))
                        ->required()
                        ->maxLength(50),
                    TextInput::make('primary_bank_account_name')
                        ->label(__('club-settings.form.bank_details.primary_bank_account_name'))
                        ->required()
                        ->maxLength(100),
                    TextInput::make('iban')
                        ->label(__('club-settings.form.bank_details.iban'))
                        ->maxLength(34)
                        ->helperText(__('club-settings.form.bank_details.iban_helper')),
                ]),
            Section::make(__('club-settings.form.credit.section'))
                ->schema([
                    TextInput::make('user_credit_limit')
                        ->label(__('club-settings.form.credit.user_credit_limit'))
                        ->required()
                        ->integer()
                        ->maxValue(0)
                        ->helperText(__('club-settings.form.credit.user_credit_limit_helper')),
                    TextInput::make('regular_membership_fees_prefix')
                        ->label(__('club-settings.form.credit.regular_membership_fees_prefix'))
                        ->required()
                        ->regex('/^\d{1,6}$/')
                        ->helperText(__('club-settings.form.credit.regular_membership_fees_prefix_helper')),
                    TextInput::make('extra_membership_fees_prefix')
                        ->label(__('club-settings.form.credit.extra_membership_fees_prefix'))
                        ->required()
                        ->regex('/^\d{1,6}$/')
                        ->helperText(__('club-settings.form.credit.extra_membership_fees_prefix_helper')),
                ]),
            Section::make(__('club-settings.form.contacts.section'))
                ->schema([
                    TextInput::make('technical_email')
                        ->label(__('club-settings.form.contacts.technical_email'))
                        ->email()
                        ->helperText(__('club-settings.form.contacts.technical_email_helper')),
                ]),
        ];
    }

    public function submit(): void
    {
        $data = (array) $this->getForm('form')?->getState();

        AppSetting::set(AppSetting::CLUB_FULL_NAME, $data['full_name']);
        AppSetting::set(AppSetting::CLUB_PRIMARY_BANK_ACCOUNT_NUMBER, $data['primary_bank_account_number']);
        AppSetting::set(AppSetting::CLUB_PRIMARY_BANK_ACCOUNT_NAME, $data['primary_bank_account_name']);
        AppSetting::set(AppSetting::CLUB_IBAN, ($data['iban'] ?? '') !== '' ? $data['iban'] : null);
        AppSetting::set(AppSetting::CLUB_USER_CREDIT_LIMIT, (int) $data['user_credit_limit']);
        AppSetting::set(AppSetting::CLUB_REGULAR_MEMBERSHIP_FEES_PREFIX, $data['regular_membership_fees_prefix']);
        AppSetting::set(AppSetting::CLUB_EXTRA_MEMBERSHIP_FEES_PREFIX, $data['extra_membership_fees_prefix']);
        AppSetting::set(AppSetting::CLUB_TECHNICAL_EMAIL, ($data['technical_email'] ?? '') !== '' ? $data['technical_email'] : null);

        AppSetting::applyClubConfigOverrides();

        Notification::make()
            ->title(__('club-settings.notification.saved_title'))
            ->success()
            ->send();
    }

    private function clubConfigString(string $key): string
    {
        $value = config('site-config.club.'.$key);

        return is_scalar($value) ? (string) $value : '';
    }

    private function clubConfigStringOrNull(string $key): ?string
    {
        $value = config('site-config.club.'.$key);

        return is_scalar($value) ? (string) $value : null;
    }
}
