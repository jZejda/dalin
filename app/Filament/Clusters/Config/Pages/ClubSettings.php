<?php

declare(strict_types=1);

namespace App\Filament\Clusters\Config\Pages;

use App\Filament\Clusters\Config\ConfigCluster;
use App\Models\AppSetting;
use App\Services\Mail\MailBranding;
use BezhanSalleh\FilamentShield\Traits\HasPageShield;
use Filament\Forms\Components\ColorPicker;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\TagsInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Notifications\Notification;
use Filament\Pages\Page;
use Filament\Schemas\Components\Section;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

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

    public ?string $seo_description = null;

    public mixed $seo_image = null;

    public mixed $seo_logo = null;

    /** @var list<string> */
    public array $seo_same_as = [];

    public ?string $accent_color = null;

    private const string SEO_UPLOAD_DIRECTORY = 'seo';

    public function mount(): void
    {
        // Filled through the schema (not by direct property assignment) so FileUpload runs
        // its afterStateHydrated() hook, which wraps the stored path into its array state.
        $this->getForm('form')?->fill([
            'abbr' => $this->clubConfigString('abbr'),
            'full_name' => $this->clubConfigString('full_name'),
            'primary_bank_account_number' => $this->clubConfigString('primary_bank_account_number'),
            'primary_bank_account_name' => $this->clubConfigString('primary_bank_account_name'),
            'iban' => $this->clubConfigStringOrNull('iban'),
            'user_credit_limit' => $this->clubConfigString('user_credit_limit'),
            'regular_membership_fees_prefix' => $this->clubConfigString('regular_membership_fees_prefix'),
            'extra_membership_fees_prefix' => $this->clubConfigString('extra_membership_fees_prefix'),
            'technical_email' => $this->clubConfigStringOrNull('technical_email'),
            'seo_description' => AppSetting::getSeoDescription(),
            'seo_image' => AppSetting::getSeoImagePath(),
            'seo_logo' => AppSetting::getSeoLogoPath(),
            'seo_same_as' => AppSetting::getSeoSameAs(),
            'accent_color' => AppSetting::getBrandingAccentColor(),
        ]);
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
            Section::make(__('club-settings.form.branding.section'))
                ->description(__('club-settings.form.branding.description'))
                ->schema([
                    ColorPicker::make('accent_color')
                        ->label(__('club-settings.form.branding.accent_color'))
                        ->helperText(__('club-settings.form.branding.accent_color_helper', ['default' => MailBranding::DEFAULT_ACCENT]))
                        ->regex('/^#[0-9a-fA-F]{6}$/'),
                ]),
            Section::make(__('club-settings.form.seo.section'))
                ->description(__('club-settings.form.seo.description'))
                ->schema([
                    Textarea::make('seo_description')
                        ->label(__('club-settings.form.seo.seo_description'))
                        ->helperText(__('club-settings.form.seo.seo_description_helper'))
                        ->rows(3)
                        ->maxLength(300),
                    FileUpload::make('seo_image')
                        ->label(__('club-settings.form.seo.seo_image'))
                        ->helperText(__('club-settings.form.seo.seo_image_helper'))
                        ->image()
                        ->acceptedFileTypes(['image/jpeg', 'image/png', 'image/webp'])
                        ->disk('public')
                        ->directory(self::SEO_UPLOAD_DIRECTORY)
                        ->visibility('public')
                        ->maxSize(4096),
                    FileUpload::make('seo_logo')
                        ->label(__('club-settings.form.seo.seo_logo'))
                        ->helperText(__('club-settings.form.seo.seo_logo_helper'))
                        ->image()
                        ->acceptedFileTypes(['image/jpeg', 'image/png', 'image/webp'])
                        ->disk('public')
                        ->directory(self::SEO_UPLOAD_DIRECTORY)
                        ->visibility('public')
                        ->maxSize(2048),
                    TagsInput::make('seo_same_as')
                        ->label(__('club-settings.form.seo.seo_same_as'))
                        ->helperText(__('club-settings.form.seo.seo_same_as_helper'))
                        ->placeholder('https://www.facebook.com/...')
                        ->nestedRecursiveRules(['url:http,https', 'max:255']),
                ]),
        ];
    }

    public function submit(): void
    {
        // Read FileUpload values from getState()'s return value: its state cast collapses
        // the internal array to a path only there, never on the property itself.
        $data = (array) $this->getForm('form')?->getState();

        AppSetting::set(AppSetting::CLUB_FULL_NAME, $data['full_name']);
        AppSetting::set(AppSetting::CLUB_PRIMARY_BANK_ACCOUNT_NUMBER, $data['primary_bank_account_number']);
        AppSetting::set(AppSetting::CLUB_PRIMARY_BANK_ACCOUNT_NAME, $data['primary_bank_account_name']);
        AppSetting::set(AppSetting::CLUB_IBAN, ($data['iban'] ?? '') !== '' ? $data['iban'] : null);
        AppSetting::set(AppSetting::CLUB_USER_CREDIT_LIMIT, (int) $data['user_credit_limit']);
        AppSetting::set(AppSetting::CLUB_REGULAR_MEMBERSHIP_FEES_PREFIX, $data['regular_membership_fees_prefix']);
        AppSetting::set(AppSetting::CLUB_EXTRA_MEMBERSHIP_FEES_PREFIX, $data['extra_membership_fees_prefix']);
        AppSetting::set(AppSetting::CLUB_TECHNICAL_EMAIL, ($data['technical_email'] ?? '') !== '' ? $data['technical_email'] : null);

        $seoDescription = trim((string) ($data['seo_description'] ?? ''));
        AppSetting::set(AppSetting::SEO_DESCRIPTION, $seoDescription !== '' ? $seoDescription : null);
        $this->saveSeoUpload(AppSetting::SEO_IMAGE, AppSetting::getSeoImagePath(), $data['seo_image'] ?? null);
        $this->saveSeoUpload(AppSetting::SEO_LOGO, AppSetting::getSeoLogoPath(), $data['seo_logo'] ?? null);
        AppSetting::set(AppSetting::SEO_SAME_AS, array_values(array_filter(
            (array) ($data['seo_same_as'] ?? []),
            static fn (mixed $url): bool => is_string($url) && $url !== '',
        )));

        $accentColor = trim((string) ($data['accent_color'] ?? ''));
        AppSetting::set(AppSetting::BRANDING_ACCENT_COLOR, $accentColor !== '' ? strtolower($accentColor) : null);

        AppSetting::applyClubConfigOverrides();

        Notification::make()
            ->title(__('club-settings.notification.saved_title'))
            ->success()
            ->send();
    }

    /**
     * The FileUpload value is a client-writable Livewire property, so only a real file
     * inside the SEO upload directory is accepted; a replaced or removed file is deleted.
     */
    private function saveSeoUpload(string $settingKey, ?string $previousPath, mixed $newPath): void
    {
        $path = is_string($newPath) && $this->isSeoUploadPath($newPath) ? $newPath : null;

        if ($previousPath !== null && $previousPath !== $path && $this->isSeoUploadPath($previousPath)) {
            Storage::disk('public')->delete($previousPath);
        }

        AppSetting::set($settingKey, $path);
    }

    private function isSeoUploadPath(string $path): bool
    {
        return Str::startsWith($path, self::SEO_UPLOAD_DIRECTORY.'/')
            && ! Str::contains($path, '..')
            && Storage::disk('public')->exists($path);
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
