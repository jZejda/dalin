<?php

declare(strict_types=1);

namespace App\Filament\Clusters\Other\Pages;

use App\Filament\Clusters\Other\OtherCluster;
use App\Enums\AppHeroIcons;
use CodeWithDennis\FilamentLucideIcons\Enums\LucideIcon;
use App\Models\SportList;
use App\Models\User;
use App\Models\UserSetting;
use App\Enums\SportEventType;
use App\Shared\Helpers\EmptyType;
use BezhanSalleh\FilamentShield\Traits\HasPageShield;
use Filament\Forms\Components\CheckboxList;
use Filament\Forms\Components\Hidden;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Notifications\Notification;
use Filament\Pages\Page;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Tabs;
use Filament\Schemas\Components\Tabs\Tab;
use Filament\Schemas\Components\View;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use App\Services\CalendarTokenService;

class UserMailNotification extends Page implements HasForms
{
    use InteractsWithForms;
    use HasPageShield;

    protected static ?string $cluster = OtherCluster::class;

    protected static ?int $navigationSort = 2;
    protected static string | \BackedEnum | null $navigationIcon = LucideIcon::UserRoundCog;
    protected string $view = 'filament.clusters.other.pages.user-mail-notification';
    protected static ?string $slug = 'user-settings';

    public static function getNavigationLabel(): string
    {
        return __('user-mail-notification.navigation_label');
    }

    public function getTitle(): string
    {
        return __('user-mail-notification.title');
    }

    private const int DEFAULT_TRIGGER_EVENT = 17;

    public array $news = [];
    public int $news_time_trigger = self::DEFAULT_TRIGGER_EVENT;

    public array $sport = [];
    public int $sport_time_trigger = self::DEFAULT_TRIGGER_EVENT;
    public int $days_before_event_entry_ends = 4;

    public array $week_report_by_sport = [];

    public bool $pre_race_summary_enabled = false;
    public int $pre_race_summary_days_before = 1;
    public int $pre_race_summary_time_trigger = self::DEFAULT_TRIGGER_EVENT;

    public array $users_allow_sign_up_for_race = [];

    public array $event_filters = [];

    public ?string $api_key = null;
    public bool $has_api_key = false;
    public bool $show_api_key = false;

    public ?string $calendar_token = null;
    public bool $has_calendar_token = false;

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

            $this->pre_race_summary_enabled = (bool) ($mailNotification->options['pre_race_summary_enabled'] ?? false);
            $this->pre_race_summary_days_before = (int) ($mailNotification->options['pre_race_summary_days_before'] ?? 1);
            $this->pre_race_summary_time_trigger = (int) ($mailNotification->options['pre_race_summary_time_trigger'] ?? self::DEFAULT_TRIGGER_EVENT);
        }

        $usersAllowSingUpForRace = UserSetting::where('user_id', '=', Auth::user()?->id)
            ->where('type', '=', 'usersAllowSignForRace')
            ->first();

        $this->users_allow_sign_up_for_race = $usersAllowSingUpForRace?->options['users_allow_sign_up_for_race'] ?? [];

        $filtersSetting = UserSetting::where('user_id', '=', Auth::user()?->id)
            ->where('type', '=', UserSetting::USER_EVENT_FILTERS_NAME)
            ->first();

        $this->event_filters = $filtersSetting?->options['event_filters'] ?? [];

        // Add ID to existing filters without ID
        $this->event_filters = array_map(function ($filter) {
            if (!isset($filter['id']) || EmptyType::stringEmpty($filter['id'])) {
                $filter['id'] = (string) Str::uuid();
            }
            return $filter;
        }, $this->event_filters);

        /** @var User $user */
        $user = Auth::user();
        $this->has_api_key = !is_null($user?->api_key_hash);

        if ($this->has_api_key && $user?->api_key_hash !== null) {
            $this->api_key = $user->api_key_hash;
            $this->show_api_key = true;
        }

        $this->has_calendar_token = $user->calendar_token !== null;
        if ($this->has_calendar_token) {
            $this->calendar_token = $user->calendar_token;
        }
    }
    public function generateApiKey(): void
    {
        /** @var User $user */
        $user = Auth::user();

        $plainApiKey = bin2hex(random_bytes(32));
        $user->setApiKey($plainApiKey);

        // Load Hash from DB
        $user->refresh();
        $this->api_key = $user->api_key_hash;
        $this->has_api_key = true;
        $this->show_api_key = true;

        Notification::make()
            ->title(__('user-mail-notification.actions.generate_api_key.notification_title'))
            ->success()
            ->body(__('user-mail-notification.actions.generate_api_key.notification_body'))
            ->send();
    }

    public function regenerateApiKey(): void
    {
        /** @var User $user */
        $user = Auth::user();

        $plainApiKey = bin2hex(random_bytes(32));
        $user->setApiKey($plainApiKey);

        // Load Hash from DB
        $user->refresh();
        $this->api_key = $user->api_key_hash;
        $this->has_api_key = true;
        $this->show_api_key = true;

        Notification::make()
            ->title(__('user-mail-notification.actions.regenerate_api_key.notification_title'))
            ->success()
            ->body(__('user-mail-notification.actions.regenerate_api_key.notification_body'))
            ->send();
    }

    public function deleteApiKey(): void
    {
        /** @var User $user */
        $user = Auth::user();
        $user->api_key_hash = null;
        $user->saveOrFail();

        $this->api_key = null;
        $this->has_api_key = false;
        $this->show_api_key = false;

        Notification::make()
            ->title(__('user-mail-notification.actions.delete_api_key.notification_title'))
            ->success()
            ->body(__('user-mail-notification.actions.delete_api_key.notification_body'))
            ->send();
    }

    public function copyApiKey(): void
    {
        Notification::make()
            ->title(__('user-mail-notification.common.copied_title'))
            ->success()
            ->body(__('user-mail-notification.actions.copy_api_key.notification_body'))
            ->send();
    }

    public function generateCalendarToken(): void
    {
        /** @var User $user */
        $user = Auth::user();
        $service = new CalendarTokenService();
        $this->calendar_token = $service->generate($user);
        $this->has_calendar_token = true;

        Notification::make()
            ->title(__('user-mail-notification.actions.generate_calendar_token.notification_title'))
            ->success()
            ->body(__('user-mail-notification.actions.generate_calendar_token.notification_body'))
            ->send();
    }

    public function regenerateCalendarToken(): void
    {
        /** @var User $user */
        $user = Auth::user();
        $service = new CalendarTokenService();
        $this->calendar_token = $service->regenerate($user);
        $this->has_calendar_token = true;

        Notification::make()
            ->title(__('user-mail-notification.actions.regenerate_calendar_token.notification_title'))
            ->success()
            ->body(__('user-mail-notification.actions.regenerate_calendar_token.notification_body'))
            ->send();
    }

    public function revokeCalendarToken(): void
    {
        /** @var User $user */
        $user = Auth::user();
        $service = new CalendarTokenService();
        $service->revoke($user);
        $this->calendar_token = null;
        $this->has_calendar_token = false;

        Notification::make()
            ->title(__('user-mail-notification.actions.revoke_calendar_token.notification_title'))
            ->success()
            ->body(__('user-mail-notification.actions.revoke_calendar_token.notification_body'))
            ->send();
    }

    public function copyCalendarToken(): void
    {
        Notification::make()
            ->title(__('user-mail-notification.common.copied_title'))
            ->success()
            ->body(__('user-mail-notification.actions.copy_calendar_token.notification_body'))
            ->send();
    }

    public function copyCalendarUrl(): void
    {
        Notification::make()
            ->title(__('user-mail-notification.common.copied_title'))
            ->success()
            ->body(__('user-mail-notification.actions.copy_calendar_url.notification_body'))
            ->send();
    }

    public function copyCalendarUrlFailed(): void
    {
        Notification::make()
            ->title(__('user-mail-notification.common.error_title'))
            ->danger()
            ->body(__('user-mail-notification.actions.copy_calendar_url_failed.notification_body'))
            ->send();
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

        $mailOptions['pre_race_summary_enabled'] = $this->pre_race_summary_enabled;
        $mailOptions['pre_race_summary_days_before'] = $this->pre_race_summary_days_before;
        $mailOptions['pre_race_summary_time_trigger'] = $this->pre_race_summary_time_trigger;
        $this->storeMailOptions($mailOptions);

        /**
         * Allow user sign for race Options
         */
        $allowUsersOptions['users_allow_sign_up_for_race'] = $this->users_allow_sign_up_for_race;
        $this->storeUsersAllowSingUpForRace($allowUsersOptions);

        /**
         * Filters Options
         */
        $filtersOptions['event_filters'] = $this->event_filters;
        $this->storeFilters($filtersOptions);

        Notification::make()
            ->title(__('user-mail-notification.actions.submit.notification_title'))
            ->success()
            ->body(__('user-mail-notification.actions.submit.notification_body'))
            ->send();
    }


    public function getCancelButtonUrlProperty(): string
    {
        return static::getUrl();
    }

    public function getBreadcrumbs(): array
    {
        return [
            url('/admin/users') => __('user-mail-notification.breadcrumbs.users'),
            url()->current() => __('user-mail-notification.breadcrumbs.settings'),
        ];
    }

    protected function getHeaderActions(): array
    {
        return [
            // ButtonAction::make('settings')->action('openSettingsModal'),
        ];
    }

    protected function getFormSchema(): array
    {
        return [
            Tabs::make(__('user-mail-notification.tabs.group_label'))
            ->tabs([
                Tab::make(__('user-mail-notification.tabs.mail_settings'))
                    ->schema([
                        Section::make(__('user-mail-notification.sections.news.heading'))
                            ->description(__('user-mail-notification.sections.news.description'))
                            ->aside()
                            ->columns(1)
                            ->schema([
                                CheckboxList::make('news')
                                    ->label(__('user-mail-notification.form.news'))
                                    ->options([
                                        '0' => __('user-mail-notification.form.news_option_public'),
                                        '1' => __('user-mail-notification.form.news_option_members'),
                                    ]),
                            ]),
                        Section::make(__('user-mail-notification.sections.entry_deadline.heading'))
                            ->description(__('user-mail-notification.sections.entry_deadline.description'))
                            ->aside()
                            ->columns(3)
                            ->schema([
                                CheckboxList::make('sport')
                                    ->label(__('user-mail-notification.form.sport'))
                                    ->options(SportList::all()->pluck('short_name', 'id')),

                                TextInput::make('sport_time_trigger')
                                    ->label(__('user-mail-notification.form.sport_time_trigger'))
                                    ->numeric()
                                    ->maxValue(24)
                                    ->minValue(0)
                                    ->default(self::DEFAULT_TRIGGER_EVENT),
                                TextInput::make('days_before_event_entry_ends')
                                    ->label(__('user-mail-notification.form.days_before_event_entry_ends'))
                                    ->numeric()
                                    ->maxValue(14)
                                    ->minValue(1)
                                    ->default(4),
                            ]),
                        Section::make(__('user-mail-notification.sections.weekly_summary.heading'))
                            ->description(__('user-mail-notification.sections.weekly_summary.description'))
                            ->aside()
                            ->columns(2)
                            ->schema([
                                CheckboxList::make('week_report_by_sport')
                                    ->label(__('user-mail-notification.form.week_report_by_sport'))
                                    ->options(SportList::all()->pluck('short_name', 'id')),
                            ]),
                        Section::make(__('user-mail-notification.sections.other_mails.heading'))
                            ->description(__('user-mail-notification.sections.other_mails.description'))
                            ->aside()
                            ->columns(3)
                            ->schema([
                                Toggle::make('pre_race_summary_enabled')
                                    ->label(__('user-mail-notification.form.pre_race_summary_enabled'))
                                    ->columnSpanFull()
                                    ->default(false),
                                TextInput::make('pre_race_summary_days_before')
                                    ->label(__('user-mail-notification.form.pre_race_summary_days_before'))
                                    ->numeric()
                                    ->minValue(1)
                                    ->maxValue(14)
                                    ->default(1),
                                TextInput::make('pre_race_summary_time_trigger')
                                    ->label(__('user-mail-notification.form.pre_race_summary_time_trigger'))
                                    ->numeric()
                                    ->minValue(0)
                                    ->maxValue(23)
                                    ->default(self::DEFAULT_TRIGGER_EVENT),
                            ]),
                    ]),

                Tab::make(__('user-mail-notification.tabs.display_filters'))
                    ->schema([
                        Repeater::make('event_filters')
                            ->label(__('user-mail-notification.form.event_filters'))
                            ->schema([
                                Hidden::make('id')
                                    ->default(fn () => (string) Str::uuid()),
                                TextInput::make('name')
                                    ->label(__('user-mail-notification.form.filter_name'))
                                    ->hint(__('user-mail-notification.form.filter_name_hint'))
                                    ->required(),
                                Select::make('sport_list')
                                    ->label(__('user-mail-notification.form.filter_sport_list'))
                                    ->options(SportList::whereIn('short_name', ['OB', 'LOB', 'MTBO', 'TRAIL'])->pluck('short_name', 'id'))
                                    ->multiple()
                                    ->default([])
                                    ->required(),
                                Select::make('sport_event_type')
                                    ->label(__('user-mail-notification.form.filter_sport_event_type'))
                                    ->options(SportEventType::enumArray())
                                    ->multiple()
                                    ->default([])
                                    ->required(),
                                Select::make('icon')
                                    ->label(__('user-mail-notification.form.filter_icon'))
                                    ->options(AppHeroIcons::enumArray())
                                    ->required(),
                            ])
                            ->columns(4)
                            ->addActionLabel(__('user-mail-notification.form.event_filters_add_action'))
                            ->reorderable(false)
                            ->default([])
                    ]),

                Tab::make(__('user-mail-notification.tabs.other'))
                    ->schema([
                        Section::make(__('user-mail-notification.sections.sign_up_permissions.heading'))
                            ->description(__('user-mail-notification.sections.sign_up_permissions.description'))
                            ->aside()
                            ->schema([
                                Select::make('users_allow_sign_up_for_race')
                                    ->label(__('user-mail-notification.form.users_allow_sign_up_for_race'))
                                    ->multiple()
                                    ->searchable()
                                    ->options(User::all()->where('active', '=', 1)->pluck('user_identification', 'id'))
                                    ->preload()
                            ]),
                    ]),

                Tab::make(__('user-mail-notification.tabs.api_key'))
                    ->schema([
                        Section::make(__('user-mail-notification.sections.api_key.heading'))
                            ->description(__('user-mail-notification.sections.api_key.description'))
                            ->aside()
                            ->schema([
                                View::make('filament.pages.components.api-key-manager')
                                    ->viewData([
                                        'hasApiKey' => $this->has_api_key,
                                        'showApiKey' => $this->show_api_key,
                                        'apiKey' => $this->api_key,
                                    ])
                            ]),
                    ]),

                Tab::make(__('user-mail-notification.tabs.calendar'))
                    ->schema([
                        Section::make(__('user-mail-notification.sections.calendar.heading'))
                            ->description(__('user-mail-notification.sections.calendar.description'))
                            ->aside()
                            ->schema([
                                View::make('filament.pages.components.calendar-token-manager')
                            ]),
                    ]),
            ])
            ->vertical()
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

    private function storeFilters(array $filtersOptions): void
    {
        $filtersSetting = UserSetting::where('user_id', '=', Auth::user()?->id)
            ->where('type', '=', UserSetting::USER_EVENT_FILTERS_NAME)
            ->first();

        if (is_null($filtersSetting) && Auth::user()?->id !== null) {
            $filtersSetting = new UserSetting();
            $filtersSetting->user_id = Auth::user()->id;
            $filtersSetting->type = 'event_filters';
        }

        if ($filtersSetting !== null) {
            if (isset($filtersOptions['event_filters'])) {
                $filtersOptions['event_filters'] = array_map(function ($filter) {
                    if (!isset($filter['id']) || empty($filter['id'])) {
                        $filter['id'] = (string) Str::uuid();
                    }
                    return $filter;
                }, array_values($filtersOptions['event_filters']));
            }
            $filtersSetting->options = $filtersOptions;
            $filtersSetting->save();
        }
    }
}
