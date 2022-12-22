<?php

namespace App\Filament\Resources;

use App\Filament\Resources\SportEventResource\Pages;
use App\Models\ContentCategory;
use App\Models\Page;
use App\Models\SportDiscipline;
use App\Models\SportEvent;
use App\Models\SportEventExport;
use App\Models\SportRegion;
use Closure;
use Filament\Facades\Filament;
use Filament\Forms\Components\Actions\Action;
use Filament\Forms\Components\Card;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Grid;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Resources\Form;
use Filament\Resources\Resource;
use Filament\Resources\Table;
use Filament\Tables;
use Illuminate\Http\Client\RequestException;
use Illuminate\Support\Facades\Http;

class SportEventResource extends Resource
{
    protected static ?string $model = SportEvent::class;

    protected static ?string $navigationIcon = 'heroicon-o-collection';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Grid::make([
                    'sm' => 1,
                    'md' => 12,
                ])->schema([
                    // Main column
                    Card::make()
                        ->schema([

                            TextInput::make('oris_id')
                                ->suffixAction(fn ($state, Closure $set) =>
                                Action::make('hledej-podle-oris-id')
                                    ->icon('heroicon-o-search')
                                    ->action(function () use ($state, $set) {
                                        if (blank($state))
                                        {
                                            Filament::notify('danger', 'Vyplň prosím ORIS ID závodu.');
                                            return;
                                        }

                                        try {
                                            $orisResponse = Http::get('https://oris.orientacnisporty.cz/API',
                                                [
                                                    'format' => 'json',
                                                    'method' => 'getEvent',
                                                    'id' => $state,
                                                ])
                                                ->throw()
                                                ->json('Data');


//                                            dd($countryData);

                                        } catch (RequestException $e) {
                                            Filament::notify('danger', 'Nepodařilo se načíst data.');
                                            return;
                                        }
                                    $set('name', $orisResponse['Name'] ?? null);
                                    $set('place', $orisResponse['Place'] ?? null);
                                    $set('date', $orisResponse['Date'] ?? null);
                                    $set('entry_date_1', $orisResponse['EntryDate1'] ?? null);
                                    $set('entry_date_2', $orisResponse['EntryDate2'] ?? null);
                                    $set('entry_date_3', $orisResponse['EntryDate3'] ?? null);

                                    $set('sport_id', $orisResponse['Sport']['Id'] ?? null);
                                    $set('region', $orisResponse['Region'] ?? null);

                                    $set('discipline_id', $orisResponse['Discipline']['ID'] ?? null);

                                    $set('use_oris_for_entries', $orisResponse['UseORISForEntries'] ?? null);
                                    })
                                ),
                            TextInput::make('name')->required(),
                            TextInput::make('place'),
                            DatePicker::make('date')->displayFormat('d.m.Y'),

                            Grid::make()->schema([
                                DateTimePicker::make('entry_date_1')->displayFormat('d.m.Y H:i:s')->required()->label('První termín'),
                                DateTimePicker::make('entry_date_2')->displayFormat('d.m.Y H:i:s')->label('Druhý termín'),
                                DateTimePicker::make('entry_date_3')->displayFormat('d.m.Y H:i:s')->label('Třetí termín'),

                                Select::make('discipline_id')
                                    ->options(SportDiscipline::all()->pluck('long_name', 'id'))
                                    ->searchable(),
                                Select::make('region')
                                    ->options(SportRegion::all()->pluck('long_name', 'short_name'))
                                    ->searchable(),

                                Toggle::make('use_oris_for_entries')->inline()
                                    ->label('Použít ORIS k přihláškám?')
                                    ->onIcon('heroicon-s-check')
                                    ->offIcon('heroicon-s-x'),


                            ])->columns(3),
                        ])
                        ->columns(2)
                        ->columnSpan([
                            'sm' => 1,
                            'md' => 12
                        ]),

                ])
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                //
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\DeleteBulkAction::make(),
            ]);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListSportEvents::route('/'),
            'create' => Pages\CreateSportEvent::route('/create'),
            'edit' => Pages\EditSportEvent::route('/{record}/edit'),
        ];
    }

}
