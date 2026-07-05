<?php

declare(strict_types=1);

namespace App\Filament\Resources\SportEvents\Pages\Actions\Helpers;

use App\Filament\Resources\SportEvents\Service\SportEventService;
use App\Models\SportClass;
use App\Models\SportClassDefinition;
use App\Models\SportEvent;
use App\Shared\Helpers\AppHelper;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\ToggleButtons;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Utilities\Set;
use Illuminate\Support\HtmlString;

/**
 * Form fields shared by the create and update entry modals.
 */
class EntryFormFields
{
    public static function siFields(): Grid
    {
        return Grid::make()->schema([
            TextInput::make('si')
                ->label('Číslo SI čipu')
                ->numeric(),
            ToggleButtons::make('rent_si')
                ->label('Půjčit čip')
                ->options([0 => 'Ne', 1 => 'Ano '])
                ->default(0)
                ->inline(),
        ])->columns(2);
    }

    public static function stagesField(SportEvent $sportEvent): Grid
    {
        return Grid::make()->schema([
            Select::make('entry_stages')
                ->label('Zvol etapy')
                ->options(function () use ($sportEvent): array {
                    if ($sportEvent->stages !== null && $sportEvent->sport_id >= 1) {
                        return (new SportEventService())->getMultiEventStagesOptions($sportEvent);
                    }

                    return [];
                })
                ->multiple()
                ->default(fn (): array => (new SportEventService())->getMultiEventDefaultOptions($sportEvent))
                ->minItems(fn (): int => ($sportEvent->stages === null || $sportEvent->stages === 0) ? 0 : 1)
                ->required(fn (): bool => ! ($sportEvent->stages === null || $sportEvent->stages === 0))
                ->visible(fn (): bool => ! ($sportEvent->stages === null || $sportEvent->stages === 0)),
        ])->columns(1);
    }

    public static function additionalInformationSection(): Section
    {
        return Section::make('Doplňkové informace')
            ->description('Další informace k přihlášce doplň po rozkliknutí.')
            ->schema([
                Grid::make()->schema([
                    TextInput::make('note')
                        ->label('Poznámka')
                        ->hint('Poznámka pořadateli.'),
                    TextInput::make('club_note')
                        ->label('Klubová poznámka')
                        ->hint('Interní poznámka.'),
                    Grid::make()->columnSpanFull()->schema([
                        TextInput::make('requested_start')
                            ->label('Požadovaný start')
                            ->hint(fn (): HtmlString => new HtmlString(
                                '<a href="'.AppHelper::getPageHelpUrl('jak-se-prihlasit-na-oris-zavod.html').'" target="_blank">Prosím čtěte nápovědu.</a>'
                            ))
                            ->hintColor('primary')
                            ->hintIcon('heroicon-m-question-mark-circle')
                            ->columnSpan(['sm' => 6, 'xl' => 4]),
                        Select::make('startListHint')
                            ->label('Vzor start požadavku')
                            ->options([
                                'Jednoetapové' => [
                                    'E0_early' => 'E0 - Brzy',
                                    'E0_late' => 'E0 - Pozdě',
                                    'E0_similarly' => 'E0 - Podobně',
                                    'E0_variously' => 'E0 - Různě',
                                    'E0_note' => 'E0 - Poznámka',
                                ],
                                'Etapové' => [
                                    'E123_early' => 'E123 - Brzy',
                                    'E123_late' => 'E123 - Pozdě',
                                    'E123_similarly' => 'E123 - Podobně',
                                    'E123_variously' => 'E123 - Různě',
                                    'E123_note' => 'E123 - Poznámka',
                                ],
                            ])
                            ->live()
                            ->afterStateUpdated(function ($state, Set $set): void {
                                $pattern = match ($state) {
                                    'E0_early' => '(E0;brzy;)',
                                    'E0_late' => '(E0;pozde;)',
                                    'E0_similarly' => '(E0;podobne;REG_CISLO)',
                                    'E0_variously' => '(E0;ruzne;REG_CISLO)',
                                    'E0_note' => '(E0;ruzne;POZNAMKA)',
                                    'E123_early' => '(E123;brzy;)',
                                    'E123_late' => '(E123;pozde;)',
                                    'E123_similarly' => '(E123;podobne;REG_CISLO)',
                                    'E123_variously' => '(E123;ruzne;REG_CISLO)',
                                    'E123_note' => '(E123;ruzne;POZNAMKA)',
                                    default => '',
                                };

                                if ($pattern !== '') {
                                    $set('requested_start', $pattern);
                                }
                            })
                            ->columnSpan(['sm' => 6, 'xl' => 2]),
                    ])->columns(6),
                ])->columns(2),
            ])
            ->collapsible()
            ->persistCollapsed()
            ->id('entry_additional_information');
    }

    /**
     * Options for the class select of events that do not use ORIS entries.
     *
     * @return array<int|string, string>
     */
    public static function localClassOptions(SportEvent $sportEvent): array
    {
        $eventClasses = SportClass::where('sport_event_id', '=', $sportEvent->id)->get();
        $classes = [];
        foreach ($eventClasses as $eventClass) {
            $classDefinitionName = SportClassDefinition::where('id', '=', $eventClass->class_definition_id)->first();
            $label = $classDefinitionName instanceof SportClassDefinition
                ? $classDefinitionName->classDefinitionFullLabel
                : '';
            $classes[$eventClass->id] = '<span class="font-medium">'.e($eventClass->name)
                .'</span> <span class="text-gray-400"> | '
                .e($label)
                .'</span>';
        }

        return $classes;
    }
}
