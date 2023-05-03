<?php

declare(strict_types=1);

namespace App\Filament\Pages;

use App\Models\SportList;
use App\Models\UserSetting;
use BezhanSalleh\FilamentShield\Traits\HasPageShield;
use Filament\Facades\Filament;
use Filament\Forms\Components\CheckboxList;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Notifications\Notification;
use Filament\Pages\Actions\ButtonAction;
use Filament\Pages\Page;
use Filament\Tables\Concerns\InteractsWithTable;
use Filament\Tables\Contracts\HasTable;
use Illuminate\Database\Eloquent\Model;

class UserMailNotification extends Page implements HasForms, HasTable
{
    use HasPageShield;

    use InteractsWithForms;
    use InteractsWithTable;

    protected static ?int $navigationSort = 37;
    protected static ?string $navigationIcon = 'heroicon-o-document-text';
    protected static string $view = 'filament.pages.user-mail-notification';
    protected static ?string $slug = 'mail-notification';
    protected static ?string $navigationLabel = 'Nastavení notifikací';
    protected static ?string $navigationGroup = 'Uživatel';
    protected static ?string $title = 'Nastavení notifikací';

    private const DEFAULT_TRIGGER_EVENT = 17;

    public array $news = [];
    public int $news_time_trigger = self::DEFAULT_TRIGGER_EVENT;
    public array $sport = [];
    public int $sport_time_trigger = self::DEFAULT_TRIGGER_EVENT;
    public int $days_before_event_entry_ends = 4;

    public function mount(): void
    {

        $mailNotification = UserSetting::where('user_id', '=', auth()->user()->id)->first();
        if (!is_null($mailNotification)) {
            $this->news = $mailNotification->options['news'] ?? [];
            $this->news_time_trigger = $mailNotification->options['news_time_trigger'] ?? self::DEFAULT_TRIGGER_EVENT;

            $this->sport = $mailNotification->options['sport'] ?? [];
            $this->sport_time_trigger = $mailNotification->options['sport_time_trigger'] ?? self::DEFAULT_TRIGGER_EVENT;
            $this->days_before_event_entry_ends = $mailNotification->options['days_before_event_entry_ends'] ?? 4;
        }

    }
    public function submit(): void
    {
        $this->form->getState();

        $mailNotification = UserSetting::where('user_id', '=', auth()->user()->id)->first();
        if (is_null($mailNotification)) {
            $mailNotification = new UserSetting();
            $mailNotification->user_id = auth()->user()->id;
            $mailNotification->type = 'mail';
        }

        $options['news'] = $this->news;
        $options['news_time_trigger'] = $this->news_time_trigger;

        $options['sport'] = $this->sport;
        $options['sport_time_trigger'] = $this->sport_time_trigger;

        $options['days_before_event_entry_ends'] = $this->days_before_event_entry_ends;

        $mailNotification->options = $options;

        $mailNotification->save();

        $recipient = auth()->user();

        Notification::make()
            ->title('Saved successfully')
            ->sendToDatabase($recipient);


        Filament::notify('success', 'Data o notifikacích jsme uložili.');

    }


    public function getCancelButtonUrlProperty(): string
    {
        return static::getUrl();
    }

    protected function getBreadcrumbs(): array
    {
        return [
            url('/admin/users') => 'Uzivatel',
            url()->current() => 'E-mailová notifikace',
        ];
    }

    protected function getActions(): array
    {
        return [
            // ButtonAction::make('settings')->action('openSettingsModal'),
        ];
    }

    protected function getFormModel(): Model | string | null
    {
        return \App\Models\UserMailNotification::class;
    }

    protected function getFormSchema(): array
    {
        return [
            Section::make('Upozornění na Novinky')
                ->columns(2)
                ->schema([
                    CheckboxList::make('news')
                        ->label('Novinky')
                        ->options([
                            '0' => 'Novinky veřejné',
                            '1' => 'Novinky interní',
                        ]),
                    TextInput::make('news_time_trigger')
                        ->label('Přibližná hodina upozornění')
                        ->numeric()
                        ->maxValue(24)
                        ->minValue(0)
                        ->default(self::DEFAULT_TRIGGER_EVENT),

                ]),
            Section::make('Upozornění na blížící se konec přihlášek k závodům')
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
        ];
    }
}
