<?php

declare(strict_types=1);

namespace App\Filament\Resources\UserResource\RelationManagers;

use App\Shared\Helpers\AppHelper;
use Filament\Forms;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Forms\Set;
use Filament\Notifications\Notification;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Http\Client\RequestException;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\HtmlString;

class UserRaceProfilesRelationManager extends RelationManager
{
    protected static string $relationship = 'userRaceProfiles';

    protected static ?string $recordTitleAttribute = 'reg_number';

    protected static ?string $label = 'Závodní profil';

    protected static ?string $title = 'Závodní profil';

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                TextInput::make('reg_number')
                    ->label('Registrace')
                    ->unique(ignoreRecord: true)
                    ->required()
                    ->hint(function (): HtmlString {
                        return new HtmlString('<a href="'.AppHelper::getPageHelpUrl('jak-pridat-uzivateli-registraci.html').'" target="_blank">Vyplň registraci a klikni na lupu.</a>');
                    })
                    ->hintColor('primary')
                    ->hintIcon('heroicon-m-question-mark-circle')
                    ->suffixAction(
                        fn ($state, Set $set) => Forms\Components\Actions\Action::make(
                            'search_oris_id_by_reg_num'
                        )
                            ->icon('heroicon-o-magnifying-glass')
                            ->action(function () use ($state, $set) {
                                if (blank($state)) {
                                    Notification::make()
                                        ->title('Formulář vstupy')
                                        ->body('Vyplň prosím Registracni cislo.')
                                        ->danger()
                                        ->seconds(8)
                                        ->send();

                                    return;
                                }

                                try {
                                    $orisResponse = Http::get(
                                        'https://oris.orientacnisporty.cz/API',
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
                                        ->title('ORIS API')
                                        ->body('Nepodařilo se načíst data.')
                                        ->danger()
                                        ->seconds(8)
                                        ->send();

                                    return;
                                }
                                Notification::make()
                                    ->title('ORIS API')
                                    ->body('ORIS v pořádku vrátil požadovaná data.')
                                    ->success()
                                    ->seconds(8)
                                    ->send();

                                $set('oris_id', $orisResponse['ID'] ?? null);
                                $set('first_name', $orisResponse['FirstName'] ?? null);
                                $set('last_name', $orisResponse['LastName'] ?? null);

                            })
                    ),
                Select::make('gender')
                    ->label('Pohlaví')
                    ->options([
                        'H' => 'Muž',
                        'D' => 'Žena',
                    ])
                    ->required(),

                TextInput::make('first_name')
                    ->label('Jméno')
                    ->required(),
                TextInput::make('last_name')
                    ->label('Příjmení')
                    ->required(),
                TextInput::make('oris_id')
                    ->label('ORIS ID'),

                Section::make('Adresa nepovinné')
                    ->schema([
                        TextInput::make('city')
                            ->label('Město'),
                        TextInput::make('street')
                            ->label('Ulize číslo domu'),
                        TextInput::make('zip')
                            ->label('PSČ'),

                        TextInput::make('email')
                            ->label('E-mail'),
                        TextInput::make('phone')
                            ->label('Telefon'),
                    ])
                    ->columns(2),
                Section::make('SI')
                    ->schema([
                        Forms\Components\TextInput::make('si')
                            ->label('Si čip')
                            ->helperText('Preferovaný SI čip')
                            ->numeric()
                            ->integer()
                            ->columnSpan('full'),
                    ]),
                Section::make('Licence')
                    ->schema([
                        Select::make('licence_ob')
                            ->label('Licence OB')
                            ->options(
                                self::getSportLicenceOptions()
                            )
                            ->default('-'),
                        Select::make('licence_lob')
                            ->label('Licence LOB')
                            ->options(
                                self::getSportLicenceOptions()
                            )
                            ->default('-'),
                        Select::make('licence_mtbo')
                            ->label('Licence MTBO')
                            ->options(
                                self::getSportLicenceOptions()
                            )
                            ->default('-'),
                    ])->columns(2),
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
                TextColumn::make('si')->label('SI')
                    ->label(__('user-race-profile.table.si')),
            ])
            ->filters([])
            ->headerActions([
                Tables\Actions\CreateAction::make(),
            ])
            ->actions([
                Tables\Actions\ActionGroup::make([
                    Tables\Actions\EditAction::make(),
                ]),

                // Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                // Tables\Actions\DeleteBulkAction::make(),
            ]);
    }
}
