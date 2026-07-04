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

    protected static ?string $navigationLabel = 'Nastavení klubu';

    protected static ?string $title = 'Nastavení klubu';

    protected static ?int $navigationSort = 11;

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
            Section::make('Identifikace klubu')
                ->schema([
                    TextInput::make('abbr')
                        ->label('Zkratka klubu')
                        ->disabled()
                        ->dehydrated(false)
                        ->helperText('Zkratka se používá pro ORIS API a cestu k logu, proto ji lze změnit pouze v souboru config/site-config.php.'),
                    TextInput::make('full_name')
                        ->label('Celý název klubu')
                        ->required()
                        ->maxLength(255),
                ]),
            Section::make('Bankovní údaje')
                ->description('Údaje se zobrazují členům v pokynech pro platbu kreditu.')
                ->schema([
                    TextInput::make('primary_bank_account_number')
                        ->label('Číslo hlavního účtu')
                        ->required()
                        ->maxLength(50),
                    TextInput::make('primary_bank_account_name')
                        ->label('Název banky')
                        ->required()
                        ->maxLength(100),
                    TextInput::make('iban')
                        ->label('IBAN')
                        ->maxLength(34)
                        ->helperText('Používá se pro generování QR platby. Ponechte prázdné, pokud chcete použít hodnotu z konfiguračního souboru.'),
                ]),
            Section::make('Kredit a členské příspěvky')
                ->schema([
                    TextInput::make('user_credit_limit')
                        ->label('Limit kreditu člena')
                        ->required()
                        ->integer()
                        ->maxValue(0)
                        ->helperText('Záporné celé číslo. Nejnižší povolený zůstatek kreditu — po jeho dosažení se člen nemůže přihlásit na závod.'),
                    TextInput::make('regular_membership_fees_prefix')
                        ->label('Prefix VS řádných členských příspěvků')
                        ->required()
                        ->regex('/^\d{1,6}$/')
                        ->helperText('Pouze číslice.'),
                    TextInput::make('extra_membership_fees_prefix')
                        ->label('Prefix VS mimořádných příspěvků (dobití kreditu)')
                        ->required()
                        ->regex('/^\d{1,6}$/')
                        ->helperText('Pozor: používá se pro automatické párování bankovních transakcí. Platby zaslané se starým prefixem se po změně nespárují.'),
                ]),
            Section::make('Kontakty')
                ->schema([
                    TextInput::make('technical_email')
                        ->label('Technický e-mail')
                        ->email()
                        ->helperText('Kontakt uváděný v e-mailech členům (reset hesla, změny kreditu apod.).'),
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
            ->title('Nastavení klubu uloženo')
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
