<?php

declare(strict_types=1);

namespace App\Filament\Resources\SportEvents\RelationManagers;

use Filament\Schemas\Schema;
use Filament\Schemas\Components\Grid;
use Filament\Actions\CreateAction;
use Filament\Actions\ActionGroup;
use Filament\Actions\EditAction;
use Filament\Actions\DeleteAction;
use App\Models\SportClass;
use App\Models\SportClassDefinition;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class SportClassesRelationManager extends RelationManager
{
    protected static string $relationship = 'sportClasses';

    protected static ?string $recordTitleAttribute = 'name';

    protected static ?string $label = 'Kategorie';

    protected static ?string $title = 'Kategorie';

    public function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Grid::make()->columnSpanFull()->schema([
                    TextInput::make('name')
                        ->label('Název kategorie')
                        ->required(),
                    Select::make('class_definition_id')
                        ->label('Definice kategorie (věk/gender)')
                        ->required()
                        ->options(SportClassDefinition::all()->pluck('class_definition_full_label', 'id'))
                        ->searchable(),
                ])->columns(2),
                TextInput::make('distance')
                    ->label('Délka')
                    ->suffix('km')
                    ->numeric()
                    ->inputMode('decimal')
                    ->minValue(0),
                TextInput::make('climbing')
                    ->label('Převýšení')
                    ->suffix('m')
                    ->numeric()
                    ->inputMode('decimal')
                    ->minValue(0),
                TextInput::make('controls')
                    ->label('Kontrol')
                    ->numeric()
                    ->minValue(0),
                TextInput::make('fee')
                    ->label('Poplatek')
                    ->numeric()
                    ->inputMode('decimal')
                    ->minValue(0),
                TextInput::make('legs')
                    ->label('Počet úseků štafety')
                    ->numeric()
                    ->integer()
                    ->minValue(1),

            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')
                    ->label('Kategorie')
                    ->description(
                        fn (SportClass $record): string => $record->classDefinition->class_definition_fullLabel ?? ''
                    )
                    ->searchable(),
                TextColumn::make('oris_id')->label('ORIS ID'),
                TextColumn::make('distance')->label('Vzdálenost'),
                TextColumn::make('climbing')->label('Stoupání'),
                TextColumn::make('controls')->label('Kontrol'),
                TextColumn::make('fee')->label('Cena'),
                TextColumn::make('legs')->label('Úseků'),
            ])
            ->filters([
                //
            ])
            ->headerActions([
                CreateAction::make(),
            ])
            ->recordActions([
                ActionGroup::make([
                    EditAction::make(),
                    DeleteAction::make(),
                ]),
            ])
            ->toolbarActions([
                // Tables\Actions\DeleteBulkAction::make(),
            ]);
    }
}
