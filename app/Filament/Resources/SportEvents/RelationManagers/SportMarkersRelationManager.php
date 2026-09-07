<?php

declare(strict_types=1);

namespace App\Filament\Resources\SportEvents\RelationManagers;

use Filament\Schemas\Schema;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Components\Utilities\Set;
use Filament\Actions\ActionGroup;
use Filament\Actions\EditAction;
use Filament\Actions\DeleteAction;
use Filament\Actions\CreateAction;
use App\Enums\SportEventMarkerType;
use App\Filament\Forms\Components\LocationPicker;
use App\Models\SportEvent;
use App\Models\SportEventMarker;
use App\Services\Map\MapMarkerResolver;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Model;

class SportMarkersRelationManager extends RelationManager
{
    protected static string $relationship = 'sportEventMarkers';

    protected static ?string $recordTitleAttribute = 'label';

    public static function getTitle(Model $ownerRecord, string $pageClass): string
    {
        return __('sport-event.relation_markers.title');
    }

    protected static function getModelLabel(): ?string
    {
        return __('sport-event.relation_markers.label');
    }

    public function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Grid::make()->columnSpanFull()->schema([
                    TextInput::make('label')
                        ->label(__('sport-event.relation_markers.name'))
                        ->required(),
                ])->columns(1),
                LocationPicker::make('picked_location')
                    ->hiddenLabel()
                    ->columnSpanFull()
                    ->live()
                    ->dehydrated(false)
                    // ->default() is only applied on a blank (create) form — Filament skips
                    // default-state hydration entirely when the schema is filled from an
                    // existing record, so editing a marker never picked up its lat/lon here
                    // and the map always opened without a pin. afterStateHydrated() runs on
                    // both create and edit fills.
                    ->afterStateHydrated(function (LocationPicker $component, Get $get): void {
                        $component->state([$get('lat'), $get('lon')]);
                    })
                    ->afterStateUpdated(function (?array $state, Set $set): void {
                        $set('lat', $state[0] ?? null);
                        $set('lon', $state[1] ?? null);
                    }),
                TextInput::make('lat')
                    ->label(__('sport-event.relation_markers.lat'))
                    ->required()
                    ->numeric(),
                TextInput::make('lon')
                    ->label(__('sport-event.relation_markers.lon'))
                    ->required()
                    ->numeric(),
                TextInput::make('desc')
                    ->label(__('sport-event.relation_markers.desc')),
                Select::make('type')
                    ->label(__('sport-event.relation_markers.type'))
                    ->required()
                    ->allowHtml()
                    ->options($this->markerTypeOptions()),
            ]);
    }

    /**
     * Renders each marker type option as its real map icon + label, so the
     * dropdown preview matches exactly what will show up on the map and in
     * the event's public "Body zájmu" list — see MapMarkerResolver.
     *
     * Only offers types that are meaningful to pick by hand — see
     * SportEventMarkerType::isSelectableForNewMarker() for why the legacy
     * "same icon as the event" variants are excluded here.
     *
     * @return array<string, string>
     */
    private function markerTypeOptions(): array
    {
        /** @var SportEvent $sportEvent */
        $sportEvent = $this->getOwnerRecord();
        $resolver = new MapMarkerResolver();

        $options = [];

        foreach (SportEventMarkerType::cases() as $type) {
            if (! $type->isSelectableForNewMarker()) {
                continue;
            }

            $visual = $resolver->resolveForMarker(new SportEventMarker(['type' => $type]), $sportEvent);

            $options[$type->value] = view('filament.forms.components.marker-type-option', [
                'visual' => $visual,
                'label' => __('sport-event.type_enum_markers.'.$type->value),
            ])->render();
        }

        return $options;
    }

    public function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('label')
                    ->label(__('sport-event.relation_markers.table.name'))
                    ->searchable()
                    ->sortable(),
                TextColumn::make('desc')
                    ->label(__('sport-event.relation_markers.table.desc'))
                    ->sortable(),
                TextColumn::make('lat')
                    ->label(__('sport-event.relation_markers.table.lat'))
                    ->sortable(),
                TextColumn::make('lon')
                    ->label(__('sport-event.relation_markers.table.lon'))
                    ->sortable(),
                TextColumn::make('type')
                    ->label(__('sport-event.relation_markers.table.type'))
                    ->formatStateUsing(fn (?SportEventMarkerType $state): ?string => $state !== null
                        ? SportEventMarkerType::enumArray()[$state->value]
                        : null)
                    ->sortable(),
            ])
            ->filters([
                //
            ])
            ->headerActions(self::buttonCreateActionVisibility())
            ->recordActions([
                ActionGroup::make([
                    EditAction::make(),
                    DeleteAction::make(),
                ]),
            ])
            ->toolbarActions([
                //   Tables\Actions\DeleteBulkAction::make(),
            ]);
    }

    private static function buttonCreateActionVisibility(): array
    {
        // TODO z recordu nejak vytahnout jestli je oris nebo ne a pak to skryt
        return [CreateAction::make()];
    }
}
