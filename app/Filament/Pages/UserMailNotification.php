<?php

declare(strict_types=1);

namespace App\Filament\Pages;

use Filament\Schemas\Components\Section;
use App\Models\SportList;
use App\Models\User;
use App\Models\UserSetting;
use BezhanSalleh\FilamentShield\Traits\HasPageShield;
use Filament\Forms\Components\CheckboxList;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Notifications\Notification;
use Filament\Pages\Page;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;

class UserMailNotification extends Page implements HasForms
{
    use InteractsWithForms;
    use HasPageShield;

    protected static ?int $navigationSort = 37;
    protected static string | \BackedEnum | null $navigationIcon = 'heroicon-o-document-text';
    protected string $view = 'filament.pages.user-mail-notification';
    protected static ?string $slug = 'mail-notification';
    protected static ?string $navigationLabel = 'Uživatelská nastavení';
    protected static string | \UnitEnum | null $navigationGroup = 'Uživatel';
    protected static ?string $title = 'Uživatelská nastavení';

    private const int DEFAULT_TRIGGER_EVENT = 17;

    public array $news = [];
    public int $news_time_trigger = self::DEFAULT_TRIGGER_EVENT;

    public array $sport = [];
    public int $sport_time_trigger = self::DEFAULT_TRIGGER_EVENT;
    public int $days_before_event_entry_ends = 4;

    public array $week_report_by_sport = [];

    public array $users_allow_sign_up_for_race = [];

    public function mount(): void
    {
        $mailNotification = UserSetting::where('user_id', '=', Auth::user()?->id)
            ->where('type', '=', 'mail')
            ->first();
        if (!is_null($mailNotification)) {
            $this->news = $mailNotification->options['news'] ?? [];
            $this->news_time_trigger = $mailNotification->options['news_time_trigger'] ?? self::DEFAULT_TRIGGER_EVENT;

            $this->sport = $mailNotification->options['sport'] ?? [];
            $this->sport_time_trigger = $mailNotification->options['sport_time_trigger'] ?? self::DEFAULT_TRIGGER_EVENT;
            $this->days_before_event_entry_ends = $mailNotification->options['days_before_event_entry_ends'] ?? 4;

            $this->week_report_by_sport = $mailNotification->options['week_report_by_sport'] ?? [];
        }

        $usersAllowSingUpForRace = UserSetting::where('user_id', '=', Auth::user()?->id)
            ->where('type', '=', 'usersAllowSignForRace')
            ->first();

        $this->users_allow_sign_up_for_race = $usersAllowSingUpForRace->options['users_allow_sign_up_for_race'] ?? [];



    }
    public function submit(): void
    {
        /** @var \Filament\Schemas\Schema $form */
        $form = $this->form;
        $form->getState();

        /**
         * Mail form Options
         */
        $mailOptions['news'] = $this->news;
        $mailOptions['news_time_trigger'] = $this->news_time_trigger;

        $mailOptions['sport'] = $this->sport;
        $mailOptions['sport_time_trigger'] = $this->sport_time_trigger;

        $mailOptions['days_before_event_entry_ends'] = $this->days_before_event_entry_ends;

        $mailOptions['week_report_by_sport'] = $this->week_report_by_sport;
        $this->storeMailOptions($mailOptions);

        /**
         * Allow user sign for race Options
         */
        $allowUsersOptions['users_allow_sign_up_for_race'] = $this->users_allow_sign_up_for_race;
        $this->storeUsersAllowSingUpForRace($allowUsersOptions);

        Notification::make()
            ->title('Nastavení uloženo')
            ->success()
            ->body('Změny v nastavení byly uloženy.')
            ->send();
    }


    public function getCancelButtonUrlProperty(): string
    {
        return static::getUrl();
    }

    public function getBreadcrumbs(): array
    {
        return [
            url('/admin/users') => 'Uzivatel',
            url()->current() => 'Nastavení',
        ];
    }

    protected function getHeaderActions(): array
    {
        return [
            // ButtonAction::make('settings')->action('openSettingsModal'),
        ];
    }

    protected function getFormModel(): Model | string | null
    {
        return UserSetting::class;
    }

    protected function getFormSchema(): array
    {
        return [
            Section::make('Upozornění na Novinky')
                ->description('Zde můžete nastavit upozornění na novinky veřejné a noviny z členské sekce.')
                ->aside()
                ->columns(1)
                ->schema([
                    CheckboxList::make('news')
                        ->label('Novinky')
                        ->options([
                            '0' => 'Novinky veřejné',
                            '1' => 'Novinky členské sekce',
                        ]),
                ]),
            Section::make('Upozornění na blížící se konec přihlášek k závodům')
                ->description('Pokud se bude blížit konec přihlášek k závodům, budete na toto upozorněni v e-mailu v uvedený čas s předstihem nastaveným ve volbě počtu dnů před koncem přihlášek.')
                ->aside()
                ->columns(3)
                ->schema([
                    CheckboxList::make('sport')
                        ->label('Sport')
                        ->options(SportList::all()->pluck('short_name', 'id')),

                    TextInput::make('sport_time_trigger')
                        ->label('Přibližná hodina upozornění')
                        ->numeric()
                        ->maxValue(24)
                        ->minValue(0)
                        ->default(self::DEFAULT_TRIGGER_EVENT),
                    TextInput::make('days_before_event_entry_ends')
                        ->label('Dnů před ukončením přihlášek')
                        ->numeric()
                        ->maxValue(14)
                        ->minValue(1)
                        ->default(4),
                ]),
            Section::make('Souhrn závodů u kterých končí termín přihlášek následující týden')
                ->description('V nastaveni definujete, které sporty budou v e-mailu souhrnně uvedeny. Souhrn obsahuje závody u kterých končí termín přihlášek následující týden.')
                ->aside()
                ->columns(2)
                ->schema([
                    CheckboxList::make('week_report_by_sport')
                        ->label('Sport')
                        ->options(SportList::all()->pluck('short_name', 'id')),
                ]),
            Section::make('Oprávnění k přihlašování')
                ->description('V nastavení můžete udělit právo přihlašovat všechny vámi spravované registrace vybraným uživatelům. Vhodné například pro rodinné příslušníky, kamarády. Právo můžete kdykoliv odvolat.')
                ->aside()
                ->schema([
                    Select::make('users_allow_sign_up_for_race')
                        ->label('Uživatelé kteří mě mohou přihlašovat a odhlašovat ze závodů')
                        ->multiple()
                        ->searchable()
                        ->options(User::all()->where('active', '=', 1)->pluck('user_identification', 'id'))
                        ->preload()
                ])
        ];
    }

    private function storeMailOptions(array $mailOptions): void
    {
        $mailNotification = UserSetting::where('user_id', '=', Auth::user()?->id)
            ->where('type', '=', 'mail')
            ->first();

        if (is_null($mailNotification) && Auth::user()?->id !== null) {
            $mailNotification = new UserSetting();
            $mailNotification->user_id = Auth::user()->id;
            $mailNotification->type = 'mail';
        }

        if ($mailNotification !== null) {
            $mailNotification->options = $mailOptions;
            $mailNotification->save();
        }
    }

    private function storeUsersAllowSingUpForRace(array $allowUsersOptions): void
    {
        $usersAllowSingUpForRace = UserSetting::where('user_id', '=', Auth::user()?->id)
            ->where('type', '=', 'usersAllowSignForRace')
            ->first();

        if (is_null($usersAllowSingUpForRace) && Auth::user()?->id !== null) {
            $usersAllowSingUpForRace = new UserSetting();
            $usersAllowSingUpForRace->user_id = Auth::user()->id;
            $usersAllowSingUpForRace->type = 'usersAllowSignForRace';
        }

        if ($usersAllowSingUpForRace !== null) {
            $usersAllowSingUpForRace->options = $allowUsersOptions;
            $usersAllowSingUpForRace->save();
        }
    }
}
