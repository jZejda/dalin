<?php

declare(strict_types=1);

namespace App\Filament\Resources\SportEvents\RelationManagers;

use App\Models\RelayTeam;
use App\Models\SportClass;
use Filament\Actions\ActionGroup;
use Filament\Actions\CreateAction;
use Filament\Actions\DeleteAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Utilities\Set;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Model;

class RelayTeamsRelationManager extends RelationManager
{
    protected static string $relationship = 'relayTeams';

    protected static ?string $recordTitleAttribute = 'name';

    public static function getTitle(Model $ownerRecord, string $pageClass): string
    {
        return __('sport-event.relation_relay_teams.title');
    }

    protected static function getModelLabel(): ?string
    {
        return __('sport-event.relation_relay_teams.label');
    }

    public function form(Schema $schema): Schema
    {
        return $schema->components([
            Grid::make()->columnSpanFull()->schema([
                TextInput::make('name')
                    ->label(__('sport-event.relation_relay_teams.name'))
                    ->required(),
                Select::make('sport_class_id')
                    ->label(__('sport-event.relation_relay_teams.class'))
                    ->options(function (): array {
                        return SportClass::query()
                            ->where('sport_event_id', $this->getOwnerRecord()->getKey())
                            ->orderBy('name')
                            ->get(['id', 'name', 'legs'])
                            ->mapWithKeys(fn (SportClass $class): array => [
                                $class->id => $class->legs !== null
                                    ? $class->name.__('sport-event.relation_relay_teams.class_option_legs_suffix', ['legs' => $class->legs])
                                    : $class->name,
                            ])
                            ->toArray();
                    })->required()
                    ->searchable()
                    ->live()
                    ->afterStateUpdated(function (Set $set, ?int $state): void {
                        if ($state === null) {
                            return;
                        }
                        $legs = SportClass::find($state)?->legs;
                        if ($legs !== null) {
                            $set('slots_count', $legs);
                        }
                    }),
                Select::make('relay_type')
                    ->label(__('sport-event.relation_relay_teams.relay_type'))
                    ->options([
                        'ST' => __('sport-event.relation_relay_teams.relay_type_relay'),
                        'SS' => __('sport-event.relation_relay_teams.relay_type_sprint_relay'),
                        'DR' => __('sport-event.relation_relay_teams.relay_type_team'),
                    ])
                    ->required(),
                TextInput::make('slots_count')
                    ->label(__('sport-event.relation_relay_teams.slots_count'))
                    ->required()
                    ->numeric()
                    ->minValue(1)
                    ->maxValue(10)
                    ->default(3)
                    ->readOnly(),
            ])->columns(2),
        ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')
                    ->label(__('sport-event.relation_relay_teams.table.name'))
                    ->searchable(),
                TextColumn::make('sportClass.name')
                    ->label(__('sport-event.relation_relay_teams.table.class'))
                    ->placeholder(__('sport-event.relation_relay_teams.table.class_placeholder')),
                TextColumn::make('relay_type')
                    ->label(__('sport-event.relation_relay_teams.table.relay_type')),
                TextColumn::make('slots_count')
                    ->label(__('sport-event.relation_relay_teams.table.slots_count')),
                TextColumn::make('occupied_slots')
                    ->label(__('sport-event.relation_relay_teams.table.occupied_slots'))
                    ->state(fn (RelayTeam $record): int => $record->members()->whereNotNull('user_race_profile_id')->count()),
            ])
            ->headerActions([
                CreateAction::make(),
            ])
            ->recordActions([
                ActionGroup::make([
                    EditAction::make(),
                    DeleteAction::make(),
                ]),
            ]);
    }
}
