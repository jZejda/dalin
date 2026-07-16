<?php

declare(strict_types=1);

namespace App\Filament\Resources\Users\RelationManagers;

use App\Services\OrisApiService;
use Filament\Schemas\Schema;
use Filament\Schemas\Components\Utilities\Set;
use Filament\Actions\Action;
use Filament\Schemas\Components\Section;
use Filament\Actions\CreateAction;
use Filament\Actions\ActionGroup;
use Filament\Actions\EditAction;
use App\Shared\Helpers\AppHelper;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Notifications\Notification;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Client\RequestException;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\HtmlString;

class UserRaceProfilesRelationManager extends RelationManager
{
    protected static string $relationship = 'userRaceProfiles';

    protected static ?string $recordTitleAttribute = 'reg_number';

    public static function getTitle(Model $ownerRecord, string $pageClass): string
    {
        return __('users.race_profile_relation.title');
    }

    protected static function getModelLabel(): ?string
    {
        return __('users.race_profile_relation.label');
    }

    public function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('reg_number')
                    ->label(__('user-race-profile.table.reg_number'))
                    ->unique(ignoreRecord: true)
                    ->required()
                    ->hint(function (): HtmlString {
                        return new HtmlString(__('user-race-profile.form.reg_number_hint', ['help_url' => AppHelper::getPageHelpUrl('jak-pridat-uzivateli-registraci.html')]));
                    })
                    ->hintColor('primary')
                    ->hintIcon('heroicon-m-question-mark-circle')
                    ->suffixAction(
                        fn ($state, Set $set) => Action::make(
                            'search_oris_id_by_reg_num'
                        )
                            ->icon('heroicon-o-magnifying-glass')
                            ->action(function () use ($state, $set) {
                                if (blank($state)) {
                                    Notification::make()
                                        ->title(__('user-race-profile.actions.search_oris.validation_title'))
                                        ->body(__('user-race-profile.actions.search_oris.validation_body'))
                                        ->danger()
                                        ->seconds(8)
                                        ->send();

                                    return;
                                }

                                try {
                                    $orisResponse = Http::get(
                                        OrisApiService::ORIS_API_URL,
                                        [
                                            'format' => 'json',
                                            'method' => 'getUser',
                                            'rgnum' => $state,
                                        ]
                                    )
                                        ->throw()
                                        ->json('Data');

                                } catch (RequestException $e) {
                                    Notification::make()
                                        ->title(__('user-race-profile.actions.search_oris.notification_title'))
                                        ->body(__('user-race-profile.actions.search_oris.error_body'))
                                        ->danger()
                                        ->seconds(8)
                                        ->send();

                                    return;
                                }

                                try {
                                    $orisResponseClubUser = Http::get(
                                        OrisApiService::ORIS_API_URL,
                                        [
                                            'format' => 'json',
                                            'method' => 'getClubUsers',
                                            'user' => $orisResponse['ID'],
                                        ]
                                    )
                                        ->throw()
                                        ->json('Data');

                                    $userClubId = null;

                                    foreach ($orisResponseClubUser as $userClub) {
                                        if ($userClub['RegNo'] === $state) {
                                            $userClubId = $userClub['ID'];
                                            break;
                                        }
                                    }

                                } catch (RequestException $e) {
                                    Notification::make()
                                        ->title(__('user-race-profile.actions.search_oris.notification_title'))
                                        ->body(__('user-race-profile.actions.search_oris.club_error_body'))
                                        ->danger()
                                        ->seconds(8)
                                        ->send();

                                    return;
                                }

                                Notification::make()
                                    ->title(__('user-race-profile.actions.search_oris.notification_title'))
                                    ->body(__('user-race-profile.actions.search_oris.success_body'))
                                    ->success()
                                    ->seconds(8)
                                    ->send();

                                $set('oris_id', $orisResponse['ID'] ?? null);
                                $set('first_name', $orisResponse['FirstName'] ?? null);
                                $set('last_name', $orisResponse['LastName'] ?? null);
                                $set('club_user_id', $userClubId);
                            })
                    ),
                Select::make('gender')
                    ->label(__('user-race-profile.form.gender'))
                    ->options([
                        'H' => __('user-race-profile.form.gender_male'),
                        'D' => __('user-race-profile.form.gender_female'),
                    ])
                    ->required(),

                TextInput::make('first_name')
                    ->label(__('user-race-profile.table.first_name'))
                    ->required(),
                TextInput::make('last_name')
                    ->label(__('user-race-profile.table.last_name'))
                    ->required(),
                TextInput::make('oris_id')
                    ->label(__('user-race-profile.table.oris_id')),
                TextInput::make('club_user_id')
                    ->label(__('user-race-profile.form.club_user_id')),

                Section::make(__('user-race-profile.form.section_address'))
                    ->schema([
                        TextInput::make('city')
                            ->label(__('user-race-profile.table.city')),
                        TextInput::make('street')
                            ->label(__('user-race-profile.form.street')),
                        TextInput::make('zip')
                            ->label(__('user-race-profile.table.zip')),

                        TextInput::make('email')
                            ->label(__('user-race-profile.table.email')),
                        TextInput::make('phone')
                            ->label(__('user-race-profile.table.phone')),
                    ])
                    ->columns(2)
                    ->columnSpan(2),
                Section::make(__('user-race-profile.common.si'))
                    ->schema([
                        TextInput::make('si')
                            ->label(__('user-race-profile.form.si'))
                            ->helperText(__('user-race-profile.form.si_helper'))
                            ->numeric()
                            ->integer()
                            ->columnSpan('full'),
                    ])->columns(2)
                    ->columnSpan(2),
                Section::make(__('user-race-profile.form.section_licence'))
                    ->schema([
                        Select::make('licence_ob')
                            ->label(__('user-race-profile.form.licence_ob'))
                            ->options(
                                self::getSportLicenceOptions()
                            )
                            ->default('-'),
                        Select::make('licence_lob')
                            ->label(__('user-race-profile.form.licence_lob'))
                            ->options(
                                self::getSportLicenceOptions()
                            )
                            ->default('-'),
                        Select::make('licence_mtbo')
                            ->label(__('user-race-profile.form.licence_mtbo'))
                            ->options(
                                self::getSportLicenceOptions()
                            )
                            ->default('-'),
                    ])->columns(2)
                    ->columnSpan(2),
            ]);
    }

    private static function getSportLicenceOptions(): array
    {
        return [
            'E' => 'E',
            'A' => 'A',
            'B' => 'B',
            'C' => 'C',
            'D' => 'D',
            'R' => 'R',
            '-' => '-',
        ];
    }

    public function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('reg_number')
                    ->label(__('user-race-profile.table.reg_number')),
                TextColumn::make('first_name')
                    ->label(__('user-race-profile.table.first_name')),
                TextColumn::make('last_name')
                    ->label(__('user-race-profile.table.last_name')),
                TextColumn::make('oris_id')
                    ->label(__('user-race-profile.table.oris_id')),
                TextColumn::make('si')
                    ->label(__('user-race-profile.table.si')),
            ])
            ->filters([])
            ->headerActions([
                CreateAction::make(),
            ])
            ->recordActions([
                ActionGroup::make([
                    EditAction::make(),
                ]),

                // Tables\Actions\DeleteAction::make(),
            ])
            ->toolbarActions([
                // Tables\Actions\DeleteBulkAction::make(),
            ]);
    }
}
