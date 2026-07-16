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
                ->label(__('sport-event.entry_form.si'))
                ->numeric(),
            ToggleButtons::make('rent_si')
                ->label(__('sport-event.entry_form.rent_si'))
                ->options([0 => __('sport-event.entry_form.rent_si_no'), 1 => __('sport-event.entry_form.rent_si_yes')])
                ->default(0)
                ->inline(),
        ])->columns(2);
    }

    public static function stagesField(SportEvent $sportEvent): Grid
    {
        return Grid::make()->schema([
            Select::make('entry_stages')
                ->label(__('sport-event.entry_form.stages'))
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
        return Section::make(__('sport-event.entry_form.additional_info_section'))
            ->description(__('sport-event.entry_form.additional_info_section_description'))
            ->schema([
                Grid::make()->schema([
                    TextInput::make('note')
                        ->label(__('sport-event.entry_form.note'))
                        ->hint(__('sport-event.entry_form.note_hint')),
                    TextInput::make('club_note')
                        ->label(__('sport-event.entry_form.club_note'))
                        ->hint(__('sport-event.entry_form.club_note_hint')),
                    Grid::make()->columnSpanFull()->schema([
                        TextInput::make('requested_start')
                            ->label(__('sport-event.entry_form.requested_start'))
                            ->hint(fn (): HtmlString => new HtmlString(
                                '<a href="'.AppHelper::getPageHelpUrl('jak-se-prihlasit-na-oris-zavod.html').'" target="_blank">'.__('sport-event.entry_form.requested_start_hint_link_text').'</a>'
                            ))
                            ->hintColor('primary')
                            ->hintIcon('heroicon-m-question-mark-circle')
                            ->columnSpan(['sm' => 6, 'xl' => 4]),
                        Select::make('startListHint')
                            ->label(__('sport-event.entry_form.start_hint_select'))
                            ->options([
                                __('sport-event.entry_form.start_hint_group_single') => [
                                    'E0_early' => __('sport-event.entry_form.start_hint_e0_early'),
                                    'E0_late' => __('sport-event.entry_form.start_hint_e0_late'),
                                    'E0_similarly' => __('sport-event.entry_form.start_hint_e0_similarly'),
                                    'E0_variously' => __('sport-event.entry_form.start_hint_e0_variously'),
                                    'E0_note' => __('sport-event.entry_form.start_hint_e0_note'),
                                ],
                                __('sport-event.entry_form.start_hint_group_multi') => [
                                    'E123_early' => __('sport-event.entry_form.start_hint_e123_early'),
                                    'E123_late' => __('sport-event.entry_form.start_hint_e123_late'),
                                    'E123_similarly' => __('sport-event.entry_form.start_hint_e123_similarly'),
                                    'E123_variously' => __('sport-event.entry_form.start_hint_e123_variously'),
                                    'E123_note' => __('sport-event.entry_form.start_hint_e123_note'),
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
