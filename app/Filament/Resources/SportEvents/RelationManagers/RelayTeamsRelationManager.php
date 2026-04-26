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
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class RelayTeamsRelationManager extends RelationManager
{
    protected static string $relationship = 'relayTeams';

    protected static ?string $recordTitleAttribute = 'name';

    protected static ?string $label = 'Štafety';

    protected static ?string $title = 'Štafety / družstva';

    public function form(Schema $schema): Schema
    {
        return $schema->components([
            Grid::make()->schema([
                TextInput::make('name')
                    ->label('Název týmu')
                    ->required(),
                TextInput::make('slots_count')
                    ->label('Počet míst')
                    ->required()
                    ->numeric()
                    ->minValue(1)
                    ->maxValue(10)
                    ->default(3),
                Select::make('relay_type')
                    ->label('Typ')
                    ->options([
                        'ST' => 'Štafeta',
                        'SS' => 'Sprintová štafeta',
                        'DR' => 'Družstva',
                    ])
                    ->required(),
                Select::make('sport_class_id')
                    ->label('Kategorie')
                    ->options(function (): array {
                        return SportClass::query()
                            ->where('sport_event_id', $this->getOwnerRecord()->id)
                            ->orderBy('name')
                            ->pluck('name', 'id')
                            ->toArray();
                    })
                    ->searchable(),
            ])->columns(2),
        ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')
                    ->label('Tým')
                    ->searchable(),
                TextColumn::make('sportClass.name')
                    ->label('Kategorie')
                    ->placeholder('—'),
                TextColumn::make('relay_type')
                    ->label('Typ'),
                TextColumn::make('slots_count')
                    ->label('Míst'),
                TextColumn::make('occupied_slots')
                    ->label('Obsazeno')
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
