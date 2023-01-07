<?php

declare(strict_types=1);

namespace App\Filament\Pages;

use App\Http\Controllers\DiscordRaceEventNotification;
use App\Mail\SendSportEventNearesMail;
use App\Mail\SendSportEventNearestMail;
use App\Models\SportEvent;
use App\Models\UserCredit;
use App\Models\UserRaceProfile;
use App\Services\OrisApiService;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Select;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Pages\Actions\ButtonAction;
use Filament\Pages\Page;
use Filament\Resources\Form;
use Filament\Tables\Actions\Action;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Concerns\InteractsWithTable;
use Filament\Tables\Contracts\HasTable;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Storage;
use Illuminate\Database\Eloquent\Builder;

class EntryData extends Page implements HasForms,HasTable
{
    use InteractsWithForms;
    use InteractsWithTable;

    protected static ?string $navigationIcon = 'heroicon-o-document-text';
    protected static string $view = 'filament.pages.entry-data';
    protected static ?string $slug = 'custom-url-slug';
    protected static ?string $navigationGroup = 'Správa';

    public string $sportEventId = '';

    public array $last_files = [];



    public function mount(): void
    {
        $this->last_files = Storage::disk('local')->allFiles();
    }

    public function submit(): void
    {

        /** @var Form $pokus */
        $this->form->getState();

        $formData = array_filter([
            'sportEventId' => $this->sportEventId,
        ]);

        /** @var SportEvent $eventEntry */
        $eventEntry = DB::table('sport_events')
            ->select('oris_id', 'id')
            ->where('id', '=', $formData['sportEventId'] )
            ->first();

        $orisResponse = Http::get(OrisApiService::ORIS_API_URL,
            [
                'format' => 'json',
                'method' => 'getEventEntries',
                'clubid' => 1,
                'eventid' => $eventEntry->oris_id,
            ])
            ->throw()
            ->object();

        /** @var UserRaceProfile $userRaceProfiles */
        $userRaceProfiles = DB::table('user_race_profiles')->get();


        if ($orisResponse->Status === 'OK') {
            foreach ($orisResponse->Data as $entry) {

                $regUserNumber = $entry->RegNo;

                $userRaceProfile = DB::table('user_race_profiles')
                    ->where('reg_number', '=', $regUserNumber)
                    ->get();

                //dd($userRaceProfile[0]->id);

                // TODO cekni jestli uz to neni prirazeno

                if (count($userRaceProfile) === 1) {
                    DB::table('user_credits')->insert([
                        'user_id' => $userRaceProfile[0]->user_id,
                        'user_race_profile_id' => $userRaceProfile[0]->id,
                        'sport_event_id' => $eventEntry->id,
                        'amount' => -$entry->Fee,
                        'currency' => UserCredit::CURRENCY_CZK,
                        'credit_type' => UserCredit::CREDIT_TYPE_CACHE_OUT,
                        'source' => UserCredit::SOURCE_CRON,
                        'source_user_id' => auth()->user()->id,
                        'created_at' => now(),
                        'updated_at' => now(),
                    ]);
                } else {
                    // profil neexistuje tak neco udelej :-)
                }
            }
        }

//        $content = 'pokus';
//        $discordNotify = new DiscordRaceEventNotification($content);
//        $discordNotify->notification();



//        $detail = [
//            'url' => 'Mail from ItSolutionStuff.com',
//        ];
//
//
//

        $eventEntry = SportEvent::select('oris_id', 'id')
                ->where('id', '=', $formData['sportEventId'] )
                ->first();

        Mail::to('zejda.jiri@gmail.com')->send(new SendSportEventNearestMail($eventEntry));

        // auth()->user()->update($state);

        // $this->reset(['sportEventId']);
        $this->notify('success', 'Nacteno z ORISU OK.');
    }

    protected function getTableQuery(): Builder
    {
        return UserCredit::where('sport_event_id', '=', $this->sportEventId);
        // return SportEvent::query();
    }

    protected function getTablePollingInterval(): ?string
    {
        return '60s';
    }

    protected function getTableColumns(): array
    {
        return [
            TextColumn::make('created_at')
                ->label(__('user-credit.table.created_at_title'))
                ->dateTime('d.m.Y'),
            TextColumn::make('sportEvent.name')
                ->label(__('user-credit.table.sport_event_title'))
                ->description(fn (UserCredit $record): string => 'popis transakce'),
            TextColumn::make('userRaceProfile.reg_number')
                ->label('RegNumber')
                ->description(fn (UserCredit $record): string => $record->userRaceProfile->user_race_full_name ?? ''),
            TextColumn::make('amount')
                ->label(__('user-credit.table.amount_title')),
            TextColumn::make('amount')
                ->label(__('user-credit.table.amount_title')),
            TextColumn::make('sourceUser.name')
                ->label(__('user-credit.table.source_user_title')),
        ];
    }

    protected function getTableFilters(): array
    {
        return [];
    }


    protected function getTableActions(): array
    {
        return [];
    }


    protected function getTableBulkActions(): array
    {
        return [];
    }

    public function getCancelButtonUrlProperty(): string
    {
        return static::getUrl();
    }

    protected function getBreadcrumbs(): array
    {
        return [
            url()->current() => 'Média',
        ];
    }

    protected function getTableRecordsPerPageSelectOptions(): array
    {
        return [10, 25, 50, 100];
    }

    protected function getTableEmptyStateIcon(): ?string
    {
        return 'heroicon-o-bookmark';
    }

    protected function getTableEmptyStateHeading(): ?string
    {
        return 'Zatím zde není žádný záznam';
    }

    protected function getTableEmptyStateDescription(): ?string
    {
        return 'Záznam můžeš vložit ručně tlačítkem dole.';
    }

    protected function getTableEmptyStateActions(): array
    {
        return [
            Action::make('create')
                ->label('Vložit záznam')
                ->url(route('login'))
                ->icon('heroicon-o-plus')
                ->button(),
        ];
    }

    protected function getActions(): array
    {
        return [
            ButtonAction::make('settings')->action('openSettingsModal'),
        ];
    }


    protected function getFormSchema(): array
    {
        return [
            Section::make('Vyber závod k nactení přihlášených uživatelů')
                ->columns(1)
                ->schema([
                    Select::make('sportEventId')
                        ->label('Vyber zavod/udalost')
                        ->options(SportEvent::all()->pluck('sport_event_oris_title', 'id'))
                        ->searchable()
                        ->required(),
                ]),
        ];
    }
}
